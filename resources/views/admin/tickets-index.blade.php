<x-app-layout>
    <div class="container mt-4">
        <h2 class="mb-4">Управление тикетами</h2>

        <!-- Статистика -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-primary">{{ $stats['total'] }}</h3>
                        <p class="mb-0">Всего тикетов</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-info">{{ $stats['new'] }}</h3>
                        <p class="mb-0">Новые</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-warning">{{ $stats['in_progress'] }}</h3>
                        <p class="mb-0">В работе</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-secondary">{{ $stats['closed'] }}</h3>
                        <p class="mb-0">Закрытые</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Фильтры -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.tickets.index') }}">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search"
                                   placeholder="Поиск по номеру или email..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="status">
                                <option value="">Все статусы</option>
                                <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>Новые</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>В работе</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Закрытые</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Найти
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-x-circle"></i> Сбросить
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Список тикетов -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Номер</th>
                                <th>Пользователь</th>
                                <th>Тема</th>
                                <th>Статус</th>
                                <th>Сообщений</th>
                                <th>Создано</th>
                                <th>Обновлено</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td>
                                        <strong>#{{ $ticket->ticket_number }}</strong>
                                    </td>
                                    <td>
                                        {{ $ticket->user->name }}<br>
                                        <small class="text-muted">{{ $ticket->user->email }}</small>
                                    </td>
                                    <td>{{ Str::limit($ticket->subject, 40) }}</td>
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
                                        <a href="{{ route('admin.tickets.show', $ticket) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Открыть
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        Тикеты не найдены
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $tickets->links() }}
        </div>
    </div>
</x-app-layout>
