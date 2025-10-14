<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use Illuminate\Http\Request;

class IdeasController extends Controller
{
    /**
     * Display a listing of approved ideas.
     */
    public function index()
    {
        $ideas = Idea::with('user')
            ->approved()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ideas.index', compact('ideas'));
    }

    /**
     * Show the form for creating a new idea.
     */
    public function create()
    {
        return view('ideas.create');
    }

    /**
     * Store a newly created idea in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Idea::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->check() ? auth()->id() : null,
            'status' => 'pending',
        ]);

        return redirect()->route('ideas.index')
            ->with('success', 'Идея отправлена на модерацию! После проверки администратором она появится на сайте.');
    }
}
