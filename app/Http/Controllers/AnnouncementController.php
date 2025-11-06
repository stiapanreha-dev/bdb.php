<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the announcements.
     */
    public function index(Request $request)
    {
        $query = Announcement::with('user')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc');

        // Фильтр по типу
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Поиск по заголовку и описанию
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        $announcements = $query->paginate(20);

        return view('announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        // Получаем компании пользователя (в нашем случае это сам пользователь)
        $user = Auth::user();

        return view('announcements.create', compact('user'));
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request)
    {
        \Log::info('[ANNOUNCEMENT] Store method called', [
            'all_data' => $request->all(),
            'description_length' => strlen($request->input('description', '')),
            'description_preview' => substr($request->input('description', ''), 0, 100)
        ]);

        $validated = $request->validate([
            'type' => ['required', 'in:supplier,buyer,dealer'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', function ($attribute, $value, $fail) {
                // Проверяем что Editor.js JSON содержит блоки
                $data = json_decode($value, true);
                if (!$data || !isset($data['blocks']) || empty($data['blocks'])) {
                    $fail('Поле описание обязательно для заполнения.');
                }
            }],
            'images' => ['nullable', 'json'],
            'register_as_purchase' => ['boolean'],
        ]);

        $user = Auth::user();

        // Обрабатываем images (из JSON строки в массив)
        $images = null;
        if ($request->has('images') && !empty($request->input('images'))) {
            $imagesJson = json_decode($request->input('images'), true);
            if (is_array($imagesJson) && !empty($imagesJson)) {
                $images = $imagesJson;
            }
        }

        \Log::info('[ANNOUNCEMENT] Images data', [
            'images_input' => $request->input('images'),
            'images_parsed' => $images
        ]);

        // Создаем объявление
        $announcement = new Announcement();
        $announcement->user_id = $user->id;
        $announcement->type = $validated['type'];
        $announcement->title = $validated['title'];
        $announcement->description = $validated['description'];
        $announcement->images = $images;
        $announcement->register_as_purchase = $request->has('register_as_purchase');
        $announcement->company_id = $user->id; // В нашем случае company_id = user_id
        $announcement->status = 'active';
        $announcement->published_at = now();
        $announcement->save();

        // Если выбран чекбокс "Зарегистрировать в закупках" и тип "Я покупатель"
        if ($announcement->register_as_purchase && $announcement->type === 'buyer') {
            $this->registerAsPurchase($announcement);
        }

        return redirect()->route('announcements.index')->with('success', 'Объявление успешно создано');
    }

    /**
     * Display the specified announcement.
     */
    public function show($id)
    {
        $announcement = Announcement::with('user')->findOrFail($id);

        return view('announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);

        // Проверка прав доступа
        if ($announcement->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'У вас нет прав на редактирование этого объявления');
        }

        return view('announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, $id)
    {
        \Log::info('[ANNOUNCEMENT] Update method called', [
            'id' => $id,
            'all_data' => $request->all(),
            'description_length' => strlen($request->input('description', '')),
            'description_preview' => substr($request->input('description', ''), 0, 100)
        ]);

        $announcement = Announcement::findOrFail($id);

        // Проверка прав доступа
        if ($announcement->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'У вас нет прав на редактирование этого объявления');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:supplier,buyer,dealer'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', function ($attribute, $value, $fail) {
                // Проверяем что Editor.js JSON содержит блоки
                $data = json_decode($value, true);
                if (!$data || !isset($data['blocks']) || empty($data['blocks'])) {
                    $fail('Поле описание обязательно для заполнения.');
                }
            }],
            'images' => ['nullable', 'json'],
            'register_as_purchase' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        // Обрабатываем images (из JSON строки в массив)
        $images = null;
        if ($request->has('images') && !empty($request->input('images'))) {
            $imagesJson = json_decode($request->input('images'), true);
            if (is_array($imagesJson) && !empty($imagesJson)) {
                $images = $imagesJson;
            }
        }

        \Log::info('[ANNOUNCEMENT] Update images data', [
            'images_input' => $request->input('images'),
            'images_parsed' => $images
        ]);

        $announcement->type = $validated['type'];
        $announcement->title = $validated['title'];
        $announcement->description = $validated['description'];
        $announcement->images = $images;
        $announcement->register_as_purchase = $request->has('register_as_purchase');

        // Обновляем дату публикации только если это админ и поле передано
        if (Auth::user()->isAdmin() && $request->has('published_at')) {
            $announcement->published_at = $validated['published_at'] ?? $announcement->published_at;
        }

        $announcement->save();

        return redirect()->route('announcements.show', $announcement->id)->with('success', 'Объявление успешно обновлено');
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);

        // Проверка прав доступа
        if ($announcement->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'У вас нет прав на удаление этого объявления');
        }

        $announcement->delete();

        return redirect()->route('announcements.index')->with('success', 'Объявление успешно удалено');
    }

    /**
     * Send inquiry to announcement author.
     */
    public function sendInquiry(Request $request, $id)
    {
        $announcement = Announcement::with('user')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'message' => ['required', 'string'],
        ]);

        // Определяем email получателя (рабочий или основной)
        $recipientEmail = $announcement->user->work_email ?? $announcement->user->email;

        if (!$recipientEmail) {
            return back()->with('error', 'У автора объявления не указан email');
        }

        try {
            // Отправляем email автору объявления
            Mail::send([], [], function ($message) use ($validated, $announcement, $recipientEmail) {
                $message->to($recipientEmail)
                    ->subject('Новая заявка на объявление: ' . $announcement->title)
                    ->html($this->buildInquiryEmailHtml($validated, $announcement));
            });

            \Log::info('[ANNOUNCEMENT] Inquiry sent', [
                'announcement_id' => $announcement->id,
                'from' => $validated['email'],
                'to' => $recipientEmail
            ]);

            return back()->with('success', 'Ваша заявка успешно отправлена');
        } catch (\Exception $e) {
            \Log::error('[ANNOUNCEMENT] Failed to send inquiry email', [
                'error' => $e->getMessage(),
                'announcement_id' => $announcement->id
            ]);

            return back()->with('error', 'Не удалось отправить заявку. Попробуйте позже.');
        }
    }

    /**
     * Build HTML for inquiry email.
     */
    private function buildInquiryEmailHtml($data, $announcement)
    {
        return "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
                    .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
                    .field { margin-bottom: 15px; }
                    .label { font-weight: bold; color: #555; }
                    .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
                    .announcement-link { display: inline-block; margin-top: 15px; padding: 10px 20px; background-color: #2196F3; color: white; text-decoration: none; border-radius: 4px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Новая заявка на ваше объявление</h2>
                    </div>
                    <div class='content'>
                        <div class='field'>
                            <span class='label'>Объявление:</span><br>
                            <strong>{$announcement->title}</strong>
                        </div>
                        <hr>
                        <div class='field'>
                            <span class='label'>Имя отправителя:</span><br>
                            {$data['name']}
                        </div>
                        <div class='field'>
                            <span class='label'>Email:</span><br>
                            <a href='mailto:{$data['email']}'>{$data['email']}</a>
                        </div>
                        <div class='field'>
                            <span class='label'>Телефон:</span><br>
                            <a href='tel:{$data['phone']}'>{$data['phone']}</a>
                        </div>
                        <div class='field'>
                            <span class='label'>Сообщение:</span><br>
                            " . nl2br(htmlspecialchars($data['message'])) . "
                        </div>
                        <div style='text-align: center;'>
                            <a href='" . route('announcements.show', $announcement->id) . "' class='announcement-link'>
                                Посмотреть объявление
                            </a>
                        </div>
                    </div>
                    <div class='footer'>
                        <p>Это письмо отправлено с сайта businessdb.ru</p>
                        <p>Пожалуйста, не отвечайте на это письмо. Для ответа используйте контакты отправителя выше.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Bulk delete announcements (admin only).
     */
    public function bulkDelete(Request $request)
    {
        // Проверка прав администратора
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Доступ запрещен');
        }

        // Получаем массив ID из запроса
        $idsJson = $request->input('ids');

        if (!$idsJson) {
            return redirect()->route('announcements.index')->with('error', 'Не выбрано ни одного объявления');
        }

        try {
            $ids = json_decode($idsJson, true);

            if (!is_array($ids) || empty($ids)) {
                return redirect()->route('announcements.index')->with('error', 'Некорректные данные');
            }

            // Удаляем объявления
            $deleted = Announcement::whereIn('id', $ids)->delete();

            \Log::info('[ANNOUNCEMENT] Bulk delete', [
                'admin_id' => Auth::id(),
                'deleted_count' => $deleted,
                'ids' => $ids
            ]);

            return redirect()->route('announcements.index')->with('success', "Успешно удалено объявлений: {$deleted}");

        } catch (\Exception $e) {
            \Log::error('[ANNOUNCEMENT] Bulk delete failed', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return redirect()->route('announcements.index')->with('error', 'Ошибка при удалении объявлений');
        }
    }

    /**
     * Register announcement as purchase in zakupki table.
     */
    private function registerAsPurchase(Announcement $announcement)
    {
        try {
            \Log::info('[ANNOUNCEMENT] Registering as purchase', [
                'announcement_id' => $announcement->id,
                'title' => $announcement->title,
                'type' => $announcement->type
            ]);

            // Извлекаем текст из Editor.js JSON
            $cleanDescription = '';
            try {
                $decoded = json_decode($announcement->description);
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded->blocks)) {
                    foreach ($decoded->blocks as $block) {
                        if (isset($block->data)) {
                            if (isset($block->data->text)) {
                                $cleanDescription .= strip_tags($block->data->text) . ' ';
                            } elseif ($block->type === 'list' && isset($block->data->items)) {
                                foreach ($block->data->items as $item) {
                                    $cleanDescription .= strip_tags($item) . ' ';
                                }
                            }
                        }
                    }
                } else {
                    $cleanDescription = strip_tags($announcement->description);
                }
            } catch (\Exception $e) {
                $cleanDescription = strip_tags($announcement->description);
            }
            $cleanDescription = trim($cleanDescription);

            // Получаем текущий год для определения БД
            $currentYear = now()->year;
            $connection = $currentYear === 2025 ? 'mssql' : "mssql_{$currentYear}";

            \Log::info('[ANNOUNCEMENT] Inserting into zakupki', [
                'connection' => $connection,
                'year' => $currentYear,
                'description_length' => strlen($cleanDescription)
            ]);

            // Вставляем запись в таблицу zakupki
            // ISO 8601 формат для SQL Server (таймзона берется из config/app.php)
            $createdAt = now()->format('Y-m-d\TH:i:s.v');

            DB::connection($connection)->table('zakupki')->insert([
                'created' => $createdAt,
                'purchase_object' => $announcement->title,
                'customer' => $announcement->user->name ?? 'Не указано',
                'purchase_type' => 10, // Специальный тип для объявлений с доски
                'url' => route('announcements.show', $announcement->id),
                'contact_number' => $announcement->user->work_phone ?? $announcement->user->phone ?? null,
                'email' => $announcement->user->work_email ?? $announcement->user->email ?? null,
            ]);

            \Log::info('[ANNOUNCEMENT] Successfully registered as purchase in zakupki');

        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем создание объявления
            \Log::error('[ANNOUNCEMENT] Failed to register announcement as purchase', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
