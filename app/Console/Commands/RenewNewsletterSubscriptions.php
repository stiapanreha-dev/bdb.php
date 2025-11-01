<?php

namespace App\Console\Commands;

use App\Models\Newsletter;
use App\Models\NewsletterSetting;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RenewNewsletterSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletters:renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renew expired newsletter subscriptions by charging users';

    const NEWSLETTER_PRICE = 500; // 500₽ per month

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting newsletter subscription renewal...');

        // Проверка: включено ли автопродление
        if (!NewsletterSetting::get('renew_enabled', true)) {
            $this->info('Newsletter renewal is disabled in settings.');
            return Command::SUCCESS;
        }

        // Проверка: настало ли нужное время для продления
        if (!$this->shouldRun()) {
            $this->info('Not the right time to run renewal.');
            return Command::SUCCESS;
        }

        // Find active newsletters that need renewal (subscription_ends_at is today or past)
        $newsletters = Newsletter::with('user')
            ->where('is_active', true)
            ->whereNotNull('subscription_ends_at')
            ->where('subscription_ends_at', '<=', now())
            ->get();

        if ($newsletters->isEmpty()) {
            $this->info('No subscriptions need renewal.');
            return Command::SUCCESS;
        }

        $this->info("Found {$newsletters->count()} subscription(s) to renew.");

        $successCount = 0;
        $failCount = 0;

        foreach ($newsletters as $newsletter) {
            $user = $newsletter->user;

            try {
                $this->info("Processing newsletter for user: {$user->name} ({$user->email})");

                // Check if user has enough balance
                if ($user->balance < self::NEWSLETTER_PRICE) {
                    $this->warn("✗ Insufficient balance for {$user->email}. Deactivating subscription.");

                    $newsletter->is_active = false;
                    $newsletter->save();

                    $failCount++;

                    Log::warning('Newsletter subscription deactivated due to insufficient balance', [
                        'user_id' => $user->id,
                        'newsletter_id' => $newsletter->id,
                        'balance' => $user->balance,
                    ]);

                    continue;
                }

                DB::beginTransaction();
                try {
                    // Deduct money from user balance
                    $user->balance -= self::NEWSLETTER_PRICE;
                    $user->save();

                    // Create transaction record
                    Transaction::create([
                        'user_id' => $user->id,
                        'amount' => -self::NEWSLETTER_PRICE,
                        'payment_id' => 'newsletter_renewal_' . time() . '_' . $newsletter->id,
                        'status' => 'completed',
                    ]);

                    // Extend subscription by 1 month
                    $newsletter->subscription_ends_at = now()->addMonth();
                    $newsletter->save();

                    DB::commit();

                    $successCount++;
                    $this->info("✓ Renewed subscription for {$user->email}. New balance: {$user->balance}₽");

                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }

            } catch (\Exception $e) {
                $failCount++;
                $this->error("✗ Error renewing subscription for {$user->email}: {$e->getMessage()}");
                Log::error('Newsletter subscription renewal error', [
                    'newsletter_id' => $newsletter->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->info("\n" . str_repeat('=', 50));
        $this->info("Newsletter subscription renewal completed!");
        $this->info("Success: {$successCount}, Failed/Deactivated: {$failCount}");
        $this->info(str_repeat('=', 50));

        return Command::SUCCESS;
    }

    /**
     * Проверка: настало ли время для продления (по настройке renew_time)
     */
    private function shouldRun(): bool
    {
        $renewTime = NewsletterSetting::get('renew_time', '00:00');
        $currentTime = now()->format('H:i');

        // Проверяем совпадение часа (с точностью до часа)
        [$renewHour] = explode(':', $renewTime);
        [$currentHour] = explode(':', $currentTime);

        return $renewHour === $currentHour;
    }
}
