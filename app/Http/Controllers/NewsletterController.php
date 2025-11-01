<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Models\NewsletterKeyword;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewsletterController extends Controller
{
    const NEWSLETTER_PRICE = 500; // 500₽ per month

    /**
     * Display newsletter management page.
     */
    public function index()
    {
        $user = Auth::user();

        // Get or create newsletter for user
        $newsletter = Newsletter::firstOrCreate(
            ['user_id' => $user->id],
            ['is_active' => false]
        );

        // Load relationships
        $newsletter->load(['keywords', 'logs' => function($query) {
            $query->orderBy('sent_at', 'desc')->limit(10);
        }]);

        return view('newsletters.index', [
            'newsletter' => $newsletter,
            'user' => $user,
        ]);
    }

    /**
     * Update newsletter settings.
     */
    public function store(Request $request)
    {
        $request->validate([
            'is_active' => 'boolean',
            'email' => 'nullable|email',
        ]);

        $user = Auth::user();

        $newsletter = Newsletter::firstOrCreate(
            ['user_id' => $user->id],
            ['is_active' => false]
        );

        $wasActive = $newsletter->is_active;
        $wantsActive = $request->boolean('is_active');

        // If activating subscription
        if (!$wasActive && $wantsActive) {
            // Check if user has enough balance
            if ($user->balance < self::NEWSLETTER_PRICE) {
                return redirect()->route('newsletters.index')
                    ->with('error', 'Недостаточно средств на балансе. Требуется ' . self::NEWSLETTER_PRICE . '₽');
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
                    'payment_id' => 'newsletter_subscription_' . time(),
                    'status' => 'completed',
                ]);

                // Activate newsletter and set subscription end date
                $newsletter->is_active = true;
                $newsletter->email = $request->input('email');
                $newsletter->subscription_ends_at = now()->addMonth();
                $newsletter->save();

                DB::commit();

                return redirect()->route('newsletters.index')
                    ->with('success', 'Подписка на рассылку активирована! Списано ' . self::NEWSLETTER_PRICE . '₽. Подписка действует до ' . $newsletter->subscription_ends_at->format('d.m.Y'));

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('newsletters.index')
                    ->with('error', 'Ошибка при активации подписки: ' . $e->getMessage());
            }
        }

        // If deactivating subscription
        if ($wasActive && !$wantsActive) {
            $newsletter->is_active = false;
            $newsletter->save();

            return redirect()->route('newsletters.index')
                ->with('success', 'Рассылка отключена. Подписка действует до ' . $newsletter->subscription_ends_at->format('d.m.Y'));
        }

        // Just update email if staying active
        $newsletter->email = $request->input('email');
        $newsletter->save();

        return redirect()->route('newsletters.index')
            ->with('success', 'Настройки рассылки сохранены');
    }

    /**
     * Update newsletter keywords.
     */
    public function updateKeywords(Request $request)
    {
        $request->validate([
            'keywords' => 'required|array',
            'keywords.*' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        $newsletter = Newsletter::firstOrCreate(
            ['user_id' => $user->id],
            ['is_active' => false]
        );

        // Delete old keywords
        $newsletter->keywords()->delete();

        // Create new keywords
        foreach ($request->input('keywords') as $keywordLine) {
            if (!empty(trim($keywordLine))) {
                NewsletterKeyword::create([
                    'newsletter_id' => $newsletter->id,
                    'keywords' => trim($keywordLine),
                ]);
            }
        }

        return redirect()->route('newsletters.index')
            ->with('success', 'Ключевые слова обновлены');
    }

    /**
     * Toggle newsletter active status.
     */
    public function toggle(Request $request)
    {
        $user = Auth::user();

        $newsletter = Newsletter::where('user_id', $user->id)->firstOrFail();

        // Use store method logic for proper payment handling
        return $this->store(
            $request->merge(['is_active' => !$newsletter->is_active])
        );
    }

    /**
     * Delete newsletter.
     */
    public function destroy()
    {
        $user = Auth::user();

        Newsletter::where('user_id', $user->id)->delete();

        return redirect()->route('newsletters.index')
            ->with('success', 'Рассылка удалена');
    }
}
