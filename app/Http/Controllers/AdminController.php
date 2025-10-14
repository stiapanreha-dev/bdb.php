<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Idea;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display list of users.
     */
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Toggle admin role for user.
     */
    public function toggleAdmin(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'Вы не можете изменить свою роль');
        }

        $user->role = $user->role === 'admin' ? 'user' : 'admin';
        $user->save();

        return redirect()->route('admin.users')
            ->with('success', 'Роль пользователя обновлена');
    }

    /**
     * Update user balance.
     */
    public function updateBalance(Request $request, User $user)
    {
        $request->validate([
            'balance' => 'required|numeric|min:0',
        ]);

        $user->balance = $request->balance;
        $user->save();

        return redirect()->route('admin.users')
            ->with('success', 'Баланс пользователя обновлен');
    }

    /**
     * Display list of ideas for moderation.
     */
    public function ideas()
    {
        $ideas = Idea::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.ideas', compact('ideas'));
    }

    /**
     * Update idea status.
     */
    public function updateIdeaStatus(Request $request, Idea $idea)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $idea->status = $request->status;
        $idea->save();

        return redirect()->route('admin.ideas')
            ->with('success', 'Статус идеи обновлен');
    }

    /**
     * Delete idea.
     */
    public function deleteIdea(Idea $idea)
    {
        $idea->delete();

        return redirect()->route('admin.ideas')
            ->with('success', 'Идея удалена');
    }

    /**
     * SQL query executor (optional feature).
     */
    public function sql()
    {
        return view('admin.sql');
    }
}
