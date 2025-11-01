<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Models\NewsletterKeyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsletterController extends Controller
{
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
            'is_active' => 'required|boolean',
            'email' => 'nullable|email',
        ]);

        $user = Auth::user();

        $newsletter = Newsletter::updateOrCreate(
            ['user_id' => $user->id],
            [
                'is_active' => $request->boolean('is_active'),
                'email' => $request->input('email'),
            ]
        );

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
        $newsletter->update(['is_active' => !$newsletter->is_active]);

        $message = $newsletter->is_active
            ? 'Рассылка включена'
            : 'Рассылка выключена';

        return redirect()->route('newsletters.index')
            ->with('success', $message);
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
