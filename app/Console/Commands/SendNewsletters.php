<?php

namespace App\Console\Commands;

use App\Exports\NewsletterExport;
use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\NewsletterLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SendNewsletters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletters:send {--user_id= : Send to specific user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send newsletters with zakupki matching user keywords';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting newsletter sending...');

        // Get active newsletters
        $query = Newsletter::with(['user', 'keywords'])
            ->where('is_active', true);

        if ($userId = $this->option('user_id')) {
            $query->where('user_id', $userId);
        }

        $newsletters = $query->get();

        if ($newsletters->isEmpty()) {
            $this->info('No active newsletters found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$newsletters->count()} active newsletter(s).");

        $successCount = 0;
        $failCount = 0;

        foreach ($newsletters as $newsletter) {
            try {
                $this->info("Processing newsletter for user: {$newsletter->user->name} ({$newsletter->user->email})");

                $result = $this->processNewsletter($newsletter);

                if ($result['success']) {
                    $successCount++;
                    $this->info("✓ Sent {$result['count']} zakupki to {$newsletter->getEmailAddress()}");
                } else {
                    $failCount++;
                    $this->error("✗ Failed: {$result['error']}");
                }
            } catch (\Exception $e) {
                $failCount++;
                $this->error("✗ Error processing newsletter: {$e->getMessage()}");
                Log::error('Newsletter sending error', [
                    'newsletter_id' => $newsletter->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->info("\n" . str_repeat('=', 50));
        $this->info("Newsletter sending completed!");
        $this->info("Success: {$successCount}, Failed: {$failCount}");
        $this->info(str_repeat('=', 50));

        return Command::SUCCESS;
    }

    /**
     * Process single newsletter.
     */
    private function processNewsletter(Newsletter $newsletter): array
    {
        // Determine date range
        $dateTo = Carbon::now();
        $dateFrom = $newsletter->last_sent_at
            ? Carbon::parse($newsletter->last_sent_at)
            : $dateTo->copy()->startOfDay();

        // Get keywords
        $keywords = $newsletter->keywords;
        if ($keywords->isEmpty()) {
            return [
                'success' => false,
                'error' => 'No keywords defined',
                'count' => 0
            ];
        }

        // Collect zakupki
        $allZakupki = [];
        foreach ($keywords as $keywordRow) {
            $keywordsList = $keywordRow->getKeywordsArray();
            if (empty($keywordsList)) {
                continue;
            }

            $zakupki = $this->getZakupkiByKeywords(
                $keywordsList,
                $dateFrom,
                $dateTo
            );

            $allZakupki = array_merge($allZakupki, $zakupki);
        }

        // Remove duplicates by ID
        $allZakupki = collect($allZakupki)->unique('id')->values()->toArray();

        $zakupkiCount = count($allZakupki);

        // Log the attempt
        $log = NewsletterLog::create([
            'newsletter_id' => $newsletter->id,
            'sent_at' => now(),
            'zakupki_count' => $zakupkiCount,
            'status' => 'pending',
        ]);

        // If no zakupki found, mark as success but don't send email
        if ($zakupkiCount === 0) {
            $log->update(['status' => 'success']);
            $newsletter->update(['last_sent_at' => now()]);

            return [
                'success' => true,
                'count' => 0,
                'message' => 'No zakupki found for this period'
            ];
        }

        try {
            // Generate Excel file
            $fileName = 'newsletter_' . $newsletter->id . '_' . time() . '.xlsx';
            $filePath = storage_path('app/temp/' . $fileName);

            // Ensure temp directory exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            // Create Excel file manually
            $excelContent = Excel::raw(new NewsletterExport($allZakupki), \Maatwebsite\Excel\Excel::XLSX);
            file_put_contents($filePath, $excelContent);

            // Send email
            $period = $dateFrom->format('d.m.Y H:i') . ' - ' . $dateTo->format('d.m.Y H:i');

            Mail::to($newsletter->getEmailAddress())
                ->send(new NewsletterMail(
                    $newsletter->user->name,
                    $zakupkiCount,
                    $period,
                    $filePath
                ));

            // Clean up temp file
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Update log and newsletter
            $log->update(['status' => 'success']);
            $newsletter->update(['last_sent_at' => now()]);

            return [
                'success' => true,
                'count' => $zakupkiCount
            ];

        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Get zakupki by keywords.
     */
    private function getZakupkiByKeywords(
        array $keywords,
        Carbon $dateFrom,
        Carbon $dateTo
    ): array {
        $query = DB::connection('mssql')->table('zakupki as z')
            ->select([
                'z.id',
                'z.created as date_request',
                'z.purchase_object',
                'z.start_cost_var',
                'z.start_cost',
                'z.customer',
                DB::raw('ISNULL(z.email, z.additional_contacts) as email'),
                'z.contact_number as phone',
                'z.post_address as address',
                'z.purchase_type',
            ])
            ->whereRaw("CONVERT(DATE, z.created) >= ?", [$dateFrom->format('Y-m-d')])
            ->whereRaw("CONVERT(DATE, z.created) <= ?", [$dateTo->format('Y-m-d')])
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('z.purchase_object', 'like', "%{$keyword}%")
                      ->orWhere('z.customer', 'like', "%{$keyword}%");
                }
            })
            ->orderBy('z.created', 'desc')
            ->limit(1000)  // Limit to 1000 per keyword set
            ->get()
            ->toArray();

        return array_map(fn($item) => (array) $item, $query);
    }
}
