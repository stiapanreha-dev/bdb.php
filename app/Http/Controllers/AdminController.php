<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Idea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * Execute SQL query.
     */
    public function executeQuery(Request $request)
    {
        // Validate input
        $request->validate([
            'query' => 'required|string|max:10000',
            'connection' => 'required|string|in:pgsql,mssql,mssql_2020,mssql_2021,mssql_2022,mssql_2023,mssql_2024,mssql_2025,mssql_2026,mssql_cp1251',
        ]);

        $query = trim($request->query);
        $connection = $request->connection;

        // Security: Block dangerous operations
        $dangerousKeywords = [
            'DROP', 'TRUNCATE', 'DELETE', 'UPDATE', 'INSERT', 'ALTER', 'CREATE',
            'GRANT', 'REVOKE', 'EXEC', 'EXECUTE', 'sp_', 'xp_'
        ];

        $upperQuery = strtoupper($query);
        foreach ($dangerousKeywords as $keyword) {
            if (strpos($upperQuery, $keyword) !== false) {
                return redirect()->route('admin.sql')
                    ->with('error', "Запрещенная операция: {$keyword}. Разрешены только SELECT запросы.");
            }
        }

        // Execute query with timeout and limit
        try {
            $startTime = microtime(true);

            // Set query timeout (10 seconds)
            if (str_starts_with($connection, 'mssql')) {
                DB::connection($connection)->statement('SET LOCK_TIMEOUT 10000');
            }

            // Execute query with limit
            $results = DB::connection($connection)
                ->select($query);

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            // Limit results to 1000 rows
            if (count($results) > 1000) {
                $results = array_slice($results, 0, 1000);
                $limitWarning = 'Показаны первые 1000 строк из ' . count($results) . ' результатов';
            } else {
                $limitWarning = null;
            }

            // Convert stdClass to array for easier display
            $results = array_map(fn($row) => (array) $row, $results);

            return view('admin.sql', [
                'results' => $results,
                'query' => $query,
                'connection' => $connection,
                'executionTime' => $executionTime,
                'rowCount' => count($results),
                'limitWarning' => $limitWarning,
            ]);

        } catch (\Exception $e) {
            return redirect()->route('admin.sql')
                ->with('error', 'Ошибка выполнения запроса: ' . $e->getMessage())
                ->withInput();
        }
    }
}
