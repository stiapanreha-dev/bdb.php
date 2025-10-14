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

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        News::create([
            'title' => $request->title,
            'content' => $request->content,
            'is_published' => true,
        ]);

        return redirect()->route('news.index')
            ->with('success', 'Новость успешно добавлена!');
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
