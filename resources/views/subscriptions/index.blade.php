<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>Выбор тарифа</h2>
    </div>
</div>

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
                           value="1000"
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

<!-- Дополнительная услуга: Рассылка -->
<div class="card border-info mt-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="bi bi-envelope-at"></i> Дополнительная услуга: Email-рассылка закупок
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h5>Получайте уведомления о новых закупках на почту</h5>
                <p class="mb-3">
                    Автоматическая рассылка закупок по вашим ключевым словам прямо на email.
                    Не пропустите интересные тендеры!
                </p>

                <h6>Преимущества:</h6>
                <ul>
                    <li>✓ Уведомления о закупках в режиме реального времени</li>
                    <li>✓ Настройка до 20 ключевых слов для фильтрации</li>
                    <li>✓ Автоматическая отправка на ваш email</li>
                    <li>✓ Удобный формат с детальной информацией о закупке</li>
                    <li>✓ Возможность включать/отключать рассылку в любой момент</li>
                </ul>

                <h6 class="mt-3">Стоимость:</h6>
                <p class="mb-2">
                    <strong class="text-info fs-4">500 руб/месяц</strong>
                    <span class="text-muted">(~16,67 руб/день)</span>
                </p>

                <div class="alert alert-light mt-3">
                    <small>
                        <i class="bi bi-info-circle"></i>
                        <strong>Как начать:</strong>
                        После оформления основного тарифа перейдите в раздел
                        <a href="{{ route('newsletters.index') }}">Рассылки</a>,
                        создайте рассылку и укажите ключевые слова. Стоимость будет автоматически списываться с баланса.
                    </small>
                </div>
            </div>

            <div class="col-md-4 text-center d-flex flex-column justify-content-center">
                <div class="p-3 bg-light rounded">
                    <i class="bi bi-envelope-at-fill text-info" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-info">500 ₽</h4>
                    <p class="text-muted">за 30 дней</p>
                    @auth
                        <a href="{{ route('newsletters.index') }}" class="btn btn-info w-100 mt-2">
                            Настроить рассылку
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-info w-100 mt-2">
                            Войти для настройки
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

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
