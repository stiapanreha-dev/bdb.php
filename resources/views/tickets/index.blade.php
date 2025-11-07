<x-app-layout>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Мои обращения</h2>
            <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Новое обращение
            </a>
        </div>

        @if($tickets->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> У вас пока нет обращений в поддержку.
                <a href="{{ route('tickets.create') }}">Создать первое обращение</a>
            </div>
        @else
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Номер</th>
                                    <th>Тема</th>
                                    <th>Статус</th>
                                    <th>Сообщений</th>
                                    <th>Создано</th>
                                    <th>Обновлено</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                    <tr>
                                        <td>
                                            <strong>#{{ $ticket->ticket_number }}</strong>
                                        </td>
                                        <td>{{ Str::limit($ticket->subject, 50) }}</td>
                                        <td>
                                            <span class="badge {{ $ticket->getStatusBadgeClass() }}">
                                                {{ $ticket->getStatusName() }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $ticket->messages->count() }}
                                            </span>
                                        </td>
                                        <td>{{ $ticket->created_at->format('d.m.Y H:i') }}</td>
                                        <td>{{ $ticket->updated_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Открыть
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
