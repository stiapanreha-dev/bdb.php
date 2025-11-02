<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Business database')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Bootstrap CSS (local) -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Yandex Verification -->
    <meta name="yandex-verification" content="c326a374924bae98" />

    @stack('styles')
</head>
<body>

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function(m,e,t,r,i,k,a){
        m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();
        for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
        k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
    })(window, document,'script','https://mc.yandex.ru/metrika/tag.js?id=104591444', 'ym');

    ym(104591444, 'init', {ssr:true, webvisor:true, clickmap:true, ecommerce:"dataLayer", accurateTrackBounce:true, trackLinks:true});
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/104591444" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Business database" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('news.index') }}">
                            Новости <span class="badge bg-light text-dark">{{ $news_count ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('ideas.index') }}">
                            Есть идея <span class="badge bg-light text-dark">{{ $ideas_count ?? 0 }}</span>
                        </a>
                    </li>
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('newsletters.index') }}">
                            Рассылки
                        </a>
                    </li>
                    @endauth
{{--                    <li class="nav-item">--}}
{{--                        <a class="nav-link" href="{{ route('invite') }}">Пригласи друга</a>--}}
{{--                    </li>--}}
                </ul>
                <ul class="navbar-nav d-lg-none">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="mobileUserDropdown" data-bs-toggle="dropdown">
                                {{ auth()->user()->name }}
                                @if(auth()->user()->isAdmin())
                                    <span class="badge bg-danger ms-1">Admin</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="mobileUserDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> Мой профиль</a></li>
                                <li><a class="dropdown-item" href="{{ route('subscriptions.index') }}"><i class="bi bi-card-list"></i> Тарифы и подписки</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#balanceModal"><i class="bi bi-wallet2"></i> Пополнить баланс</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('privacy-policy') }}"><i class="bi bi-shield-check"></i> Политика конфиденциальности</a></li>
                                <li><a class="dropdown-item" href="{{ route('terms-of-service') }}"><i class="bi bi-file-text"></i> Пользовательское соглашение</a></li>
                                <li><a class="dropdown-item" href="{{ route('offer') }}"><i class="bi bi-file-earmark-text"></i> Публичная оферта</a></li>
                                @if(auth()->user()->isAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header">Администрирование</h6></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.users') }}">Управление пользователями</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.ideas') }}">Модерация идей</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.tariffs.index') }}">Управление тарифами</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.payments') }}">Платежи ЮKassa</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.newsletters') }}">Статистика рассылок</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.newsletter-settings') }}">Настройки рассылки</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.cache') }}">Управление кешем</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.sql') }}">SQL Запросы</a></li>
                                @endif
                            </ul>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link">
                                <i class="bi bi-wallet2"></i> {{ number_format(auth()->user()->balance, 2) }} ₽
                            </span>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-light w-100">Выход</button>
                            </form>
                        </li>
                    @endauth
                </ul>
                <div class="d-none d-lg-flex align-items-center">
                    @auth
                        @php
                            $activeSubscription = auth()->user()->activeSubscription;
                        @endphp
                        @if($activeSubscription)
                            <a href="{{ route('subscriptions.index') }}" class="text-white text-decoration-none me-3">
                                <span class="badge bg-success">
                                    {{ $activeSubscription->tariff->name }} до {{ $activeSubscription->expires_at->format('d.m.Y') }}
                                </span>
                            </a>
                        @else
                            <a href="{{ route('subscriptions.index') }}" class="btn btn-warning btn-sm me-3">
                                Выбрать тариф
                            </a>
                        @endif
                        <span class="text-white me-3">
                            <i class="bi bi-wallet2"></i> {{ number_format(auth()->user()->balance, 2) }} ₽
                        </span>
                        <div class="dropdown d-inline">
                            <a href="#" class="text-white text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ auth()->user()->name }}
                                @if(auth()->user()->isAdmin())
                                    <span class="badge bg-danger ms-1">Admin</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> Мой профиль</a></li>
                                <li><a class="dropdown-item" href="{{ route('subscriptions.index') }}"><i class="bi bi-card-list"></i> Тарифы и подписки</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#balanceModal"><i class="bi bi-wallet2"></i> Пополнить баланс</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('privacy-policy') }}"><i class="bi bi-shield-check"></i> Политика конфиденциальности</a></li>
                                <li><a class="dropdown-item" href="{{ route('terms-of-service') }}"><i class="bi bi-file-text"></i> Пользовательское соглашение</a></li>
                                <li><a class="dropdown-item" href="{{ route('offer') }}"><i class="bi bi-file-earmark-text"></i> Публичная оферта</a></li>
                                @if(auth()->user()->isAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header">Администрирование</h6></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.users') }}">Управление пользователями</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.ideas') }}">Модерация идей</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.tariffs.index') }}">Управление тарифами</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.payments') }}">Платежи ЮKassa</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.newsletters') }}">Статистика рассылок</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.newsletter-settings') }}">Настройки рассылки</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.cache') }}">Управление кешем</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.sql') }}">SQL Запросы</a></li>
                                @endif
                            </ul>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline ms-2">
                            @csrf
                            <button type="submit" class="btn btn-outline-light">Выход</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light me-2">Вход</a>
                        <a href="{{ route('register') }}" class="btn btn-outline-light">Регистрация</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Flash Messages -->
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

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{ $slot }}
    </div>

    <!-- Footer -->
    <footer class="footer mt-5 py-4 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="mb-2">
                        <a href="{{ route('privacy-policy') }}" class="text-decoration-none me-3">Согласие на обработку персональных данных</a>
                        <a href="{{ route('terms-of-service') }}" class="text-decoration-none me-3">Пользовательское соглашение</a>
                    <!--    <a href="{{ route('offer') }}" class="text-decoration-none me-3">Публичная оферта</a> -->
                        <a href="{{ route('contacts') }}" class="text-decoration-none me-3">Контакты</a>
                        <a href="{{ route('subscriptions.index') }}" class="text-decoration-none">Тарифы</a>
                    </p>
                    <p class="text-muted mb-0">&copy; 2025 Business database. Все права защищены.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Balance Modal -->
    @auth
    <div class="modal fade" id="balanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Пополнение баланса</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('payment.create') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label">Сумма пополнения (₽)</label>
                            <input type="number" class="form-control" id="amount" name="amount"
                                   min="1" step="0.01" value="1000" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Пополнить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endauth

    <!-- Bootstrap JS (local) -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
    // Исправление проблемы с backdrop модалки
    document.addEventListener('DOMContentLoaded', function() {
        const balanceModal = document.getElementById('balanceModal');
        if (balanceModal) {
            balanceModal.addEventListener('hidden.bs.modal', function () {
                // Удаляем backdrop если он остался
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                // Убираем класс modal-open с body
                document.body.classList.remove('modal-open');
                // Восстанавливаем скролл
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        }
    });
    </script>

    @stack('scripts')
</body>
</html>
