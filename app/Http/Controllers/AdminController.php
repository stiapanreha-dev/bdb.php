<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Idea;
use App\Models\NewsletterSetting;
use App\Models\Payment;
use App\Models\Newsletter;
use App\Models\NewsletterLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

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

        $query = trim($request->input('query'));
        $connection = $request->input('connection');

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

    /**
     * Display newsletter settings page.
     */
    public function newsletterSettings()
    {
        $settings = NewsletterSetting::all();

        return view('admin.newsletter-settings', compact('settings'));
    }

    /**
     * Update newsletter settings.
     */
    public function updateNewsletterSettings(Request $request)
    {
        $request->validate([
            'send_interval_minutes' => 'required|integer|min:10|max:1440',
            'renew_time' => 'required|string|regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/',
        ]);

        try {
            // Checkboxes send '1' when checked, nothing when unchecked
            NewsletterSetting::set('send_enabled', $request->has('send_enabled') ? true : false);
            NewsletterSetting::set('send_interval_minutes', $request->send_interval_minutes);
            NewsletterSetting::set('renew_enabled', $request->has('renew_enabled') ? true : false);
            NewsletterSetting::set('renew_time', $request->renew_time);

            return redirect()->route('admin.newsletter-settings')
                ->with('success', 'Настройки рассылки обновлены');
        } catch (\Exception $e) {
            return redirect()->route('admin.newsletter-settings')
                ->with('error', 'Ошибка обновления настроек: ' . $e->getMessage());
        }
    }

    /**
     * Display list of payments from YooKassa.
     */
    public function payments(Request $request)
    {
        $query = Payment::with('user');

        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Фильтр по пользователю (поиск по имени или email)
        if ($request->filled('user_search')) {
            $search = $request->user_search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        // Фильтр по датам
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        // Статистика
        $stats = [
            'total_amount' => Payment::where('status', 'succeeded')->sum('amount'),
            'total_count' => Payment::where('status', 'succeeded')->count(),
            'pending_count' => Payment::where('status', 'pending')->count(),
            'canceled_count' => Payment::where('status', 'canceled')->count(),
        ];

        return view('admin.payments', compact('payments', 'stats'));
    }

    /**
     * Display newsletters statistics.
     */
    public function newsletters(Request $request)
    {
        $query = Newsletter::with(['user', 'keywords']);

        // Фильтр по статусу
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('subscription_ends_at', '<', now())
                      ->orWhereNull('subscription_ends_at');
            } elseif ($request->status === 'valid') {
                $query->where('subscription_ends_at', '>=', now());
            }
        }

        // Фильтр по пользователю
        if ($request->filled('user_search')) {
            $search = $request->user_search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        $newsletters = $query->orderBy('created_at', 'desc')->paginate(20);

        // Общая статистика
        $stats = [
            'total_newsletters' => Newsletter::count(),
            'active_newsletters' => Newsletter::where('is_active', true)->count(),
            'inactive_newsletters' => Newsletter::where('is_active', false)->count(),
            'expired_subscriptions' => Newsletter::where('subscription_ends_at', '<', now())->count(),
            'total_logs' => NewsletterLog::count(),
            'total_sent_today' => NewsletterLog::whereDate('sent_at', today())->count(),
            'total_zakupki_sent' => NewsletterLog::sum('zakupki_count'),
            'failed_logs' => NewsletterLog::where('status', 'failed')->count(),
        ];

        // Статистика отправок за последние 30 дней
        $recentLogs = NewsletterLog::with('newsletter.user')
            ->where('sent_at', '>=', now()->subDays(30))
            ->orderBy('sent_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.newsletters', compact('newsletters', 'stats', 'recentLogs'));
    }

    /**
     * Display cache management page.
     */
    public function cache()
    {
        return view('admin.cache');
    }

    /**
     * Clear specific cache type.
     */
    public function clearCache(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:all,config,route,view,cache',
        ]);

        $type = $request->input('type');
        $message = '';

        try {
            switch ($type) {
                case 'all':
                    Artisan::call('optimize:clear');
                    $message = 'Весь кеш очищен (config, route, view, cache, opcache)';
                    break;
                case 'config':
                    Artisan::call('config:clear');
                    $message = 'Кеш конфигурации очищен';
                    break;
                case 'route':
                    Artisan::call('route:clear');
                    $message = 'Кеш маршрутов очищен';
                    break;
                case 'view':
                    Artisan::call('view:clear');
                    $message = 'Кеш представлений очищен';
                    break;
                case 'cache':
                    Artisan::call('cache:clear');
                    $message = 'Кеш приложения очищен';
                    break;
            }

            return redirect()->route('admin.cache')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.cache')
                ->with('error', 'Ошибка очистки кеша: ' . $e->getMessage());
        }
    }
}
