<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>Управление платежами ЮKassa</h2>
        <p class="text-muted">История платежей и транзакций пользователей</p>
    </div>
</div>

<!-- Статистика -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-success">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">Успешные платежи</h6>
                <h3 class="card-title mb-0">{{ number_format($stats['total_amount'], 2, '.', ' ') }} ₽</h3>
                <small class="text-white-50">Всего: {{ $stats['total_count'] }} шт.</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">В ожидании</h6>
                <h3 class="card-title mb-0">{{ $stats['pending_count'] }}</h3>
                <small class="text-white-50">платежей</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-secondary">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">Отменено</h6>
                <h3 class="card-title mb-0">{{ $stats['canceled_count'] }}</h3>
                <small class="text-white-50">платежей</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">Всего платежей</h6>
                <h3 class="card-title mb-0">{{ $stats['total_count'] + $stats['pending_count'] + $stats['canceled_count'] }}</h3>
                <small class="text-white-50">за всё время</small>
            </div>
        </div>
    </div>
</div>

<!-- Фильтры -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.payments') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Статус</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Все статусы</option>
                        <option value="succeeded" {{ request('status') === 'succeeded' ? 'selected' : '' }}>Успешные</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>В ожидании</option>
                        <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>Отменённые</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="user_search" class="form-label">Пользователь</label>
                    <input type="text"
                           name="user_search"
                           id="user_search"
                           class="form-control"
                           placeholder="Имя или email"
                           value="{{ request('user_search') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Дата от</label>
                    <input type="date"
                           name="date_from"
                           id="date_from"
                           class="form-control"
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Дата до</label>
                    <input type="date"
                           name="date_to"
                           id="date_to"
                           class="form-control"
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Применить</button>
                    <a href="{{ route('admin.payments') }}" class="btn btn-secondary">Сбросить</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Таблица платежей -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Дата создания</th>
                        <th>Пользователь</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                        <th>Способ оплаты</th>
                        <th>Дата оплаты</th>
                        <th>ID ЮKassa</th>
                        <th>Описание</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <strong>{{ $payment->user->name }}</strong><br>
                            <small class="text-muted">{{ $payment->user->email }}</small>
                        </td>
                        <td>
                            <strong>{{ number_format($payment->amount, 2, '.', ' ') }} {{ $payment->currency }}</strong>
                        </td>
                        <td>
                            @if($payment->status === 'succeeded')
                                <span class="badge bg-success">Оплачено</span>
                            @elseif($payment->status === 'pending')
                                <span class="badge bg-warning">Ожидание</span>
                            @elseif($payment->status === 'canceled')
                                <span class="badge bg-secondary">Отменено</span>
                            @else
                                <span class="badge bg-secondary">{{ $payment->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->payment_method)
                                <span class="badge bg-info">{{ $payment->payment_method }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->paid_at)
                                {{ $payment->paid_at->format('d.m.Y H:i') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <code class="small">{{ Str::limit($payment->yookassa_payment_id, 20) }}</code>
                        </td>
                        <td>
                            @if($payment->description)
                                {{ Str::limit($payment->description, 30) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <p class="text-muted mb-0">Платежей не найдено</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $payments->links() }}
        </div>
    </div>
</div>

<!-- Дополнительная информация -->
<div class="card mt-3">
    <div class="card-body">
        <h5 class="mb-3">О платежах</h5>
        <ul class="mb-0">
            <li>Платежи обрабатываются через платёжную систему <strong>ЮKassa</strong></li>
            <li>После успешной оплаты баланс пользователя автоматически пополняется</li>
            <li>Webhook обрабатывает уведомления от ЮKassa в реальном времени</li>
            <li>Статус платежа обновляется автоматически при получении уведомления</li>
            <li>История платежей доступна пользователям в разделе <a href="{{ route('payment.history') }}">История платежей</a></li>
        </ul>
    </div>
</div>
</x-app-layout>
