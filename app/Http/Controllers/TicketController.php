<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Список тикетов пользователя
     */
    public function index()
    {
        $tickets = Ticket::where('user_id', auth()->id())
            ->with('messages')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Форма создания тикета
     */
    public function create()
    {
        return view('tickets.create');
    }

    /**
     * Создание тикета
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_code' => 'required|string|max:5',
            'phone' => 'required|string|max:15',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,txt',
        ]);

        // Создаем тикет
        $ticket = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'user_id' => auth()->id(),
            'country_code' => $validated['country_code'],
            'phone' => $validated['phone'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        // Обработка прикрепленных файлов
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket-attachments', 'public');
                $attachments[] = $path;
            }
        }

        // Создаем первое сообщение
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'attachments' => $attachments,
            'is_admin' => false,
        ]);

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Обращение #' . $ticket->ticket_number . ' успешно создано!');
    }

    /**
     * Просмотр тикета с перепиской
     */
    public function show(Ticket $ticket)
    {
        // Проверка доступа
        if ($ticket->user_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }

        $ticket->load(['messages.user', 'user']);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Добавление сообщения в тикет
     */
    public function addMessage(Request $request, Ticket $ticket)
    {
        // Проверка доступа
        if ($ticket->user_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }

        // Нельзя добавлять сообщения в закрытые тикеты
        if ($ticket->isClosed()) {
            return back()->with('error', 'Тикет закрыт. Невозможно добавить сообщение.');
        }

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
            'is_admin' => false,
        ]);

        return back()->with('success', 'Сообщение добавлено');
    }
}
