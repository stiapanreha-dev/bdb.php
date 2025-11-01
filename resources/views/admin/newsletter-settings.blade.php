<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>Настройки рассылки</h2>
        <p class="text-muted">Управление автоматической рассылкой новостей и продлением подписок</p>
    </div>
</div>

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

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.newsletter-settings.update') }}">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">Настройки отправки рассылки</h5>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   role="switch"
                                   name="send_enabled"
                                   id="send_enabled"
                                   value="1"
                                   {{ old('send_enabled', $settings->where('key', 'send_enabled')->first()?->value ?? 'true') === 'true' ? 'checked' : '' }}>
                            <label class="form-check-label" for="send_enabled">
                                <strong>Включить автоматическую рассылку</strong>
                            </label>
                        </div>
                        <small class="text-muted">
                            Если выключено, рассылка не будет отправляться автоматически
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="send_interval_minutes" class="form-label">
                            <strong>Интервал отправки (в минутах)</strong>
                        </label>
                        <input type="number"
                               class="form-control"
                               name="send_interval_minutes"
                               id="send_interval_minutes"
                               value="{{ old('send_interval_minutes', $settings->where('key', 'send_interval_minutes')->first()?->value ?? '180') }}"
                               min="10"
                               max="1440"
                               required>
                        <small class="text-muted">
                            Минимальное время между рассылками (10-1440 минут). По умолчанию: 180 минут (3 часа)
                        </small>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="mb-3">Настройки продления подписок</h5>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   role="switch"
                                   name="renew_enabled"
                                   id="renew_enabled"
                                   value="1"
                                   {{ old('renew_enabled', $settings->where('key', 'renew_enabled')->first()?->value ?? 'true') === 'true' ? 'checked' : '' }}>
                            <label class="form-check-label" for="renew_enabled">
                                <strong>Включить автоматическое продление подписок</strong>
                            </label>
                        </div>
                        <small class="text-muted">
                            Если выключено, подписки не будут продлеваться автоматически
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="renew_time" class="form-label">
                            <strong>Время продления подписок (UTC+3)</strong>
                        </label>
                        <input type="time"
                               class="form-control"
                               name="renew_time"
                               id="renew_time"
                               value="{{ old('renew_time', $settings->where('key', 'renew_time')->first()?->value ?? '00:00') }}"
                               required>
                        <small class="text-muted">
                            Ежедневное время для проверки и продления истекших подписок. По умолчанию: 00:00
                        </small>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="alert alert-info" role="alert">
                <strong>Как это работает:</strong>
                <ul class="mb-0 mt-2">
                    <li>Cron запускается каждый час (безопасный базовый интервал)</li>
                    <li>Команда рассылки проверяет настройки и выполняется только если прошло достаточно времени с последней отправки</li>
                    <li>Команда продления проверяет настройки и выполняется только в указанный час</li>
                    <li>Изменение настроек не требует перезапуска cron или сервера</li>
                </ul>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                    Назад к панели администратора
                </a>
                <button type="submit" class="btn btn-primary">
                    Сохранить настройки
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h5 class="mb-3">Текущие значения настроек</h5>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Ключ</th>
                        <th>Значение</th>
                        <th>Тип</th>
                        <th>Описание</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($settings as $setting)
                    <tr>
                        <td><code>{{ $setting->key }}</code></td>
                        <td>
                            @if($setting->type === 'boolean')
                                <span class="badge {{ $setting->value === 'true' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $setting->value === 'true' ? 'Включено' : 'Выключено' }}
                                </span>
                            @else
                                <strong>{{ $setting->value }}</strong>
                            @endif
                        </td>
                        <td><code>{{ $setting->type }}</code></td>
                        <td>{{ $setting->description }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
