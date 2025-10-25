<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>История изменений тарифа: {{ $tariff->name }}</h2>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h5>Информация о тарифе</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Название:</strong><br>
                {{ $tariff->name }}
            </div>
            <div class="col-md-3">
                <strong>Длительность:</strong><br>
                {{ $tariff->duration_days }} дней
            </div>
            <div class="col-md-3">
                <strong>Цена:</strong><br>
                {{ number_format($tariff->price, 2, '.', ' ') }} руб
            </div>
            <div class="col-md-3">
                <strong>Статус:</strong><br>
                @if($tariff->is_active)
                    <span class="badge bg-success">Активен</span>
                @else
                    <span class="badge bg-secondary">Неактивен</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h5>История изменений</h5>
    </div>
    <div class="card-body">
        @if($tariff->history->count() > 0)
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Поле</th>
                        <th>Старое значение</th>
                        <th>Новое значение</th>
                        <th>Изменил</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tariff->history->sortByDesc('changed_at') as $change)
                    <tr>
                        <td>{{ $change->changed_at->format('d.m.Y H:i:s') }}</td>
                        <td>
                            @switch($change->field_name)
                                @case('name') Название @break
                                @case('price') Цена @break
                                @case('duration_days') Длительность @break
                                @case('is_active') Статус @break
                                @default {{ $change->field_name }}
                            @endswitch
                        </td>
                        <td>{{ $change->old_value }}</td>
                        <td>{{ $change->new_value }}</td>
                        <td>
                            @if($change->changedBy)
                                {{ $change->changedBy->name }}
                            @else
                                Система
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted">История изменений пуста</p>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>Активные подписки ({{ $tariff->subscriptions->count() }})</h5>
    </div>
    <div class="card-body">
        @if($tariff->subscriptions->count() > 0)
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Пользователь</th>
                        <th>Email</th>
                        <th>Начало</th>
                        <th>Окончание</th>
                        <th>Оплачено</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tariff->subscriptions as $subscription)
                    <tr>
                        <td>{{ $subscription->user->name }}</td>
                        <td>{{ $subscription->user->email }}</td>
                        <td>{{ $subscription->starts_at->format('d.m.Y H:i') }}</td>
                        <td>{{ $subscription->expires_at->format('d.m.Y H:i') }}</td>
                        <td>{{ number_format($subscription->paid_amount, 2, '.', ' ') }} руб</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted">Активных подписок нет</p>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.tariffs.index') }}" class="btn btn-secondary">Назад к списку</a>
    <a href="{{ route('admin.tariffs.edit', $tariff) }}" class="btn btn-warning">Редактировать</a>
</div>
</x-app-layout>
