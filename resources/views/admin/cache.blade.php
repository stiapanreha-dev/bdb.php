<x-app-layout>
<div class="row">
    <div class="col-md-12">
        <h2>Управление кешем</h2>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Информация
            </div>
            <div class="card-body">
                <p>На этой странице вы можете очистить различные типы кеша Laravel.</p>
                <ul>
                    <li><strong>Весь кеш</strong> - очищает все типы кеша (config, route, view, cache, opcache)</li>
                    <li><strong>Конфигурация</strong> - очищает кеш файлов конфигурации (.env, config/*.php)</li>
                    <li><strong>Маршруты</strong> - очищает кеш маршрутов (routes/*.php)</li>
                    <li><strong>Представления</strong> - очищает скомпилированные Blade шаблоны (resources/views/*.blade.php)</li>
                    <li><strong>Приложение</strong> - очищает кеш данных приложения (cache facade)</li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h4 class="mb-3">Очистка кеша</h4>
            </div>
        </div>

        <div class="row g-3">
            <!-- Очистить весь кеш -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-arrow-clockwise text-danger"></i> Весь кеш
                        </h5>
                        <p class="card-text text-muted">
                            Очистить все типы кеша одновременно (optimize:clear)
                        </p>
                        <form method="POST" action="{{ route('admin.cache.clear') }}" onsubmit="return confirm('Вы уверены, что хотите очистить весь кеш?')">
                            @csrf
                            <input type="hidden" name="type" value="all">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash"></i> Очистить весь кеш
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Кеш конфигурации -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-gear text-primary"></i> Конфигурация
                        </h5>
                        <p class="card-text text-muted">
                            Очистить кеш файлов конфигурации (config:clear)
                        </p>
                        <form method="POST" action="{{ route('admin.cache.clear') }}">
                            @csrf
                            <input type="hidden" name="type" value="config">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-trash"></i> Очистить config
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Кеш маршрутов -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-signpost text-success"></i> Маршруты
                        </h5>
                        <p class="card-text text-muted">
                            Очистить кеш маршрутов (route:clear)
                        </p>
                        <form method="POST" action="{{ route('admin.cache.clear') }}">
                            @csrf
                            <input type="hidden" name="type" value="route">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-trash"></i> Очистить routes
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Кеш представлений -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-file-earmark-code text-warning"></i> Представления
                        </h5>
                        <p class="card-text text-muted">
                            Очистить скомпилированные Blade шаблоны (view:clear)
                        </p>
                        <form method="POST" action="{{ route('admin.cache.clear') }}">
                            @csrf
                            <input type="hidden" name="type" value="view">
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-trash"></i> Очистить views
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Кеш приложения -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-database text-info"></i> Приложение
                        </h5>
                        <p class="card-text text-muted">
                            Очистить кеш данных приложения (cache:clear)
                        </p>
                        <form method="POST" action="{{ route('admin.cache.clear') }}">
                            @csrf
                            <input type="hidden" name="type" value="cache">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bi bi-trash"></i> Очистить cache
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-terminal"></i> Эквивалентные команды
            </div>
            <div class="card-body">
                <p>Эти действия эквивалентны выполнению следующих команд в терминале:</p>
                <pre class="bg-dark text-light p-3 rounded"><code>php artisan optimize:clear  # Очистить весь кеш
php artisan config:clear    # Очистить кеш конфигурации
php artisan route:clear     # Очистить кеш маршрутов
php artisan view:clear      # Очистить кеш представлений
php artisan cache:clear     # Очистить кеш приложения</code></pre>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
