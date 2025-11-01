<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>Статистика рассылок</h2>
        <p class="text-muted">Управление и мониторинг всех рассылок пользователей</p>
    </div>
</div>

<!-- Общая статистика -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">Всего рассылок</h6>
                <h3 class="card-title mb-0">{{ $stats['total_newsletters'] }}</h3>
                <small class="text-white-50">Активных: {{ $stats['active_newsletters'] }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">Отправлено сегодня</h6>
                <h3 class="card-title mb-0">{{ $stats['total_sent_today'] }}</h3>
                <small class="text-white-50">рассылок</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">Всего отправлено</h6>
                <h3 class="card-title mb-0">{{ $stats['total_logs'] }}</h3>
                <small class="text-white-50">Закупок: {{ number_format($stats['total_zakupki_sent']) }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">Истекшие подписки</h6>
                <h3 class="card-title mb-0">{{ $stats['expired_subscriptions'] }}</h3>
                <small class="text-white-50">Ошибок: {{ $stats['failed_logs'] }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Фильтры -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.newsletters') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Статус рассылки</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Все рассылки</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Активные</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Неактивные</option>
                        <option value="valid" {{ request('status') === 'valid' ? 'selected' : '' }}>С действующей подпиской</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>С истекшей подпиской</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="user_search" class="form-label">Пользователь</label>
                    <input type="text"
                           name="user_search"
                           id="user_search"
                           class="form-control"
                           placeholder="Имя или email"
                           value="{{ request('user_search') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Применить</button>
                    <a href="{{ route('admin.newsletters') }}" class="btn btn-secondary">Сбросить</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Таблица рассылок -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-3">Список рассылок</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Пользователь</th>
                        <th>Email рассылки</th>
                        <th>Статус</th>
                        <th>Подписка до</th>
                        <th>Ключевые слова</th>
                        <th>Последняя отправка</th>
                        <th>Создана</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($newsletters as $newsletter)
                    <tr>
                        <td>{{ $newsletter->id }}</td>
                        <td>
                            <strong>{{ $newsletter->user->name }}</strong><br>
                            <small class="text-muted">{{ $newsletter->user->email }}</small>
                        </td>
                        <td>
                            @if($newsletter->email)
                                <code>{{ $newsletter->email }}</code>
                            @else
                                <span class="text-muted">По умолчанию</span>
                            @endif
                        </td>
                        <td>
                            @if($newsletter->is_active)
                                <span class="badge bg-success">Активна</span>
                            @else
                                <span class="badge bg-secondary">Неактивна</span>
                            @endif
                        </td>
                        <td>
                            @if($newsletter->subscription_ends_at)
                                @if($newsletter->subscription_ends_at->isPast())
                                    <span class="badge bg-danger">{{ $newsletter->subscription_ends_at->format('d.m.Y') }}</span>
                                @else
                                    <span class="badge bg-success">{{ $newsletter->subscription_ends_at->format('d.m.Y') }}</span>
                                @endif
                            @else
                                <span class="text-muted">Не указана</span>
                            @endif
                        </td>
                        <td>
                            @if($newsletter->keywords->count() > 0)
                                <small>
                                    @foreach($newsletter->keywords->take(3) as $keyword)
                                        <span class="badge bg-info">{{ $keyword->keyword }}</span>
                                    @endforeach
                                    @if($newsletter->keywords->count() > 3)
                                        <span class="text-muted">+{{ $newsletter->keywords->count() - 3 }}</span>
                                    @endif
                                </small>
                            @else
                                <span class="text-muted">Нет</span>
                            @endif
                        </td>
                        <td>
                            @if($newsletter->last_sent_at)
                                {{ $newsletter->last_sent_at->format('d.m.Y H:i') }}
                            @else
                                <span class="text-muted">Не отправлялась</span>
                            @endif
                        </td>
                        <td>{{ $newsletter->created_at->format('d.m.Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <p class="text-muted mb-0">Рассылок не найдено</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $newsletters->links() }}
        </div>
    </div>
</div>

<!-- Последние отправки -->
<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Последние отправки (30 дней)</h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Дата отправки</th>
                        <th>Пользователь</th>
                        <th>Email</th>
                        <th>Закупок в письме</th>
                        <th>Статус</th>
                        <th>Ошибка</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLogs as $log)
                    <tr>
                        <td>{{ $log->sent_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <strong>{{ $log->newsletter->user->name }}</strong>
                        </td>
                        <td>
                            <small>{{ $log->newsletter->getEmailAddress() }}</small>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $log->zakupki_count }}</span>
                        </td>
                        <td>
                            @if($log->status === 'sent')
                                <span class="badge bg-success">Отправлено</span>
                            @elseif($log->status === 'failed')
                                <span class="badge bg-danger">Ошибка</span>
                            @else
                                <span class="badge bg-secondary">{{ $log->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($log->error_message)
                                <small class="text-danger">{{ Str::limit($log->error_message, 50) }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <p class="text-muted mb-0">Нет отправок за последние 30 дней</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
