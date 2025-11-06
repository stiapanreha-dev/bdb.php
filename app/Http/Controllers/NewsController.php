<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of published news.
     */
    public function index()
    {
        $news = News::published()
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('news.index', compact('news'));
    }

    /**
     * Show the form for creating a new news item.
     */
    public function create()
    {
        // Only admins can create news
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('news.create');
    }

    /**
     * Store a newly created news item in storage.
     */
    public function store(Request $request)
    {
        // Only admins can create news
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => ['required', 'string', function ($attribute, $value, $fail) {
                // Проверяем что Editor.js JSON содержит блоки
                $data = json_decode($value, true);
                if (!$data || !isset($data['blocks']) || empty($data['blocks'])) {
                    $fail('Поле содержание обязательно для заполнения.');
                }
            }],
            'images' => 'nullable|json',
            'published_at' => 'nullable|date',
        ]);

        // Обрабатываем images (из JSON строки в массив)
        $images = null;
        if ($request->has('images') && !empty($request->input('images'))) {
            $imagesJson = json_decode($request->input('images'), true);
            if (is_array($imagesJson) && !empty($imagesJson)) {
                $images = $imagesJson;
            }
        }

        News::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'images' => $images,
            'published_at' => $validated['published_at'] ?? now(),
            'is_published' => true,
        ]);

        return redirect()->route('news.index')
            ->with('success', 'Новость успешно добавлена!');
    }

    /**
     * Display the specified news item.
     */
    public function show(News $news)
    {
        return view('news.show', compact('news'));
    }

    /**
     * Show the form for editing the specified news item.
     */
    public function edit(News $news)
    {
        // Only admins can edit news
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('news.edit', compact('news'));
    }

    /**
     * Update the specified news item in storage.
     */
    public function update(Request $request, News $news)
    {
        // Only admins can update news
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => ['required', 'string', function ($attribute, $value, $fail) {
                // Проверяем что Editor.js JSON содержит блоки
                $data = json_decode($value, true);
                if (!$data || !isset($data['blocks']) || empty($data['blocks'])) {
                    $fail('Поле содержание обязательно для заполнения.');
                }
            }],
            'images' => 'nullable|json',
            'published_at' => 'nullable|date',
        ]);

        // Обрабатываем images (из JSON строки в массив)
        $images = null;
        if ($request->has('images') && !empty($request->input('images'))) {
            $imagesJson = json_decode($request->input('images'), true);
            if (is_array($imagesJson) && !empty($imagesJson)) {
                $images = $imagesJson;
            }
        }

        $news->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'images' => $images,
            'published_at' => $validated['published_at'] ?? $news->published_at ?? now(),
        ]);

        return redirect()->route('news.show', $news)
            ->with('success', 'Новость успешно обновлена!');
    }

    /**
     * Remove the specified news item from storage.
     */
    public function destroy(News $news)
    {
        // Only admins can delete news
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }

        $title = $news->title;
        $news->delete();

        return redirect()->route('news.index')
            ->with('success', "Новость \"{$title}\" удалена");
    }
}
