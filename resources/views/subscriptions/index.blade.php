<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>Выбор тарифа</h2>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($activeSubscription)
<div class="card mb-4 border-success">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">Ваш активный тариф</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Тариф:</strong><br>
                {{ $activeSubscription->tariff->name }}
            </div>
            <div class="col-md-3">
                <strong>Начало:</strong><br>
                {{ $activeSubscription->starts_at->format('d.m.Y H:i') }}
            </div>
            <div class="col-md-3">
                <strong>Окончание:</strong><br>
                {{ $activeSubscription->expires_at->format('d.m.Y H:i') }}
            </div>
            <div class="col-md-3">
                <strong>Оплачено:</strong><br>
                {{ number_format($activeSubscription->paid_amount, 2, '.', ' ') }} руб
            </div>
        </div>
        <div class="mt-2">
            @php
                $daysLeft = (int) now()->diffInDays($activeSubscription->expires_at, false);
            @endphp
            @if($daysLeft > 0)
                <span class="badge bg-info">Осталось {{ $daysLeft }} дней</span>
            @else
                <span class="badge bg-warning">Истекает сегодня</span>
            @endif
        </div>
    </div>
</div>
@endif

@auth
<div class="card mb-3">
    <div class="card-header">
        <h5>Ваш баланс: <strong>{{ number_format(auth()->user()->balance, 2, '.', ' ') }} руб</strong></h5>
    </div>
    <div class="card-body">
        <h6>Пополнить баланс</h6>
        <form method="POST" action="{{ route('payment.create') }}" class="row g-3">
            @csrf
            <div class="col-md-6">
                <div class="form-group">
                    <label for="amount">Сумма (руб)</label>
                    <input type="number"
                           class="form-control"
                           id="amount"
                           name="amount"
                           min="1"
                           step="0.01"
                           value="100"
                           required>
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100">
                    Пополнить через ЮKassa
                </button>
            </div>
        </form>
        <div class="mt-2">
            <small class="text-muted">
                Оплата принимается через систему ЮKassa (банковские карты, электронные кошельки)
            </small>
        </div>
    </div>
</div>
@else
<div class="alert alert-info">
    <strong>Войдите в систему</strong>, чтобы оформить подписку.
    <a href="{{ route('login') }}" class="alert-link">Войти</a> или
    <a href="{{ route('register') }}" class="alert-link">Зарегистрироваться</a>
</div>
@endauth

<div class="row">
    @foreach($tariffs as $tariff)
    <div class="col-md-6 mb-4">
        <div class="card h-100 {{ $tariff->duration_days == 30 ? 'border-primary' : '' }}">
            @if($tariff->duration_days == 30)
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Рекомендуем</h5>
            </div>
            @endif
            <div class="card-body">
                <h3 class="card-title">{{ $tariff->name }}</h3>
                <p class="card-text text-muted">{{ $tariff->duration_days }} дней</p>
                <h2 class="text-primary">{{ number_format($tariff->price, 2, '.', ' ') }} руб</h2>
                <p class="text-muted">
                    ~{{ number_format($tariff->price / $tariff->duration_days, 2, '.', ' ') }} руб/день
                </p>
                
                <hr>
                
                <ul class="list-unstyled">
                    <li>✓ Полный доступ к базе компаний</li>
                    <li>✓ Экспорт данных в Excel</li>
                    <li>✓ Просмотр закупок</li>
                    <li>✓ Без ограничений</li>
                </ul>

                @auth
                <form method="POST" action="{{ route('subscriptions.subscribe', $tariff) }}" class="mt-3">
                    @csrf
                    <button type="submit" 
                            class="btn {{ $tariff->duration_days == 30 ? 'btn-primary' : 'btn-outline-primary' }} w-100"
                            {{ auth()->user()->balance < $tariff->price ? 'disabled' : '' }}>
                        @if(auth()->user()->balance < $tariff->price)
                            Недостаточно средств
                        @elseif($activeSubscription)
                            Продлить после {{ $activeSubscription->expires_at->format('d.m.Y') }}
                        @else
                            Оплатить
                        @endif
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}" class="btn btn-primary w-100 mt-3">
                    Войти для оформления
                </a>
                @endauth
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($tariffs->isEmpty())
<div class="alert alert-warning">
    В данный момент нет доступных тарифов. Пожалуйста, обратитесь к администратору.
</div>
@endif

@auth
<div class="mt-3">
    <a href="{{ route('subscriptions.history') }}" class="btn btn-outline-secondary">
        История подписок
    </a>
    <a href="{{ route('payment.history') }}" class="btn btn-outline-secondary ms-2">
        История платежей
    </a>
</div>
@endauth
</x-app-layout>
