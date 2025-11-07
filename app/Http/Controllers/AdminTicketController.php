<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{
    /**
     * Список всех тикетов (для админа)
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'messages']);

        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Поиск по номеру тикета или email пользователя
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);

        // Статистика
        $stats = [
            'total' => Ticket::count(),
            'new' => Ticket::where('status', 'new')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
        ];

        return view('admin.tickets-index', compact('tickets', 'stats'));
    }

    /**
     * Просмотр тикета (для админа)
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['messages.user', 'user']);

        return view('admin.tickets-show', compact('ticket'));
    }

    /**
     * Изменение статуса тикета
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,in_progress,closed',
        ]);

        $ticket->update([
            'status' => $validated['status'],
            'closed_at' => $validated['status'] === 'closed' ? now() : null,
        ]);

        return back()->with('success', 'Статус тикета обновлен');
    }

    /**
     * Добавление ответа от админа
     */
    public function addMessage(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,txt',
        ]);

        // Обработка прикрепленных файлов
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket-attachments', 'public');
                $attachments[] = $path;
            }
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'attachments' => $attachments,
            'is_admin' => true,
        ]);

        // Если тикет был в статусе "новый", переводим в "в работе"
        if ($ticket->isNew()) {
            $ticket->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Ответ отправлен');
    }
}
