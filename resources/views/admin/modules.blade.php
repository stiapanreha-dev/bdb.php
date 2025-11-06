<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>Управление модулями</h2>
        <p class="text-muted">
            Управляйте доступностью модулей системы. Отключенные модули будут скрыты из меню и недоступны для пользователей.
        </p>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Модуль</th>
                        <th>Описание</th>
                        <th>Статус</th>
                        <th>Настройки</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($modules as $module)
                        <tr>
                            <td>
                                <strong>{{ $module->module_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $module->module_key }}</small>
                            </td>
                            <td>
                                {{ $module->description ?? 'Нет описания' }}
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input module-toggle"
                                        type="checkbox"
                                        role="switch"
                                        id="toggle-{{ $module->module_key }}"
                                        data-module-key="{{ $module->module_key }}"
                                        {{ $module->is_enabled ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="toggle-{{ $module->module_key }}">
                                        <span class="status-text badge bg-{{ $module->is_enabled ? 'success' : 'secondary' }}">
                                            {{ $module->is_enabled ? 'Включен' : 'Отключен' }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                @if ($module->hasSettings())
                                    <a href="{{ $module->settings_route }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-gear"></i> Настройки
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggles = document.querySelectorAll('.module-toggle');

        toggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const moduleKey = this.dataset.moduleKey;
                const isEnabled = this.checked;
                const statusBadge = this.parentElement.querySelector('.status-text');
                const originalClasses = statusBadge.className;
                const originalText = statusBadge.textContent;

                // Показываем индикатор загрузки
                statusBadge.className = 'status-text badge bg-warning';
                statusBadge.textContent = 'Обновление...';

                fetch('/admin/modules/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        module_key: moduleKey,
                        is_enabled: isEnabled
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusBadge.className = 'status-text badge bg-' + (isEnabled ? 'success' : 'secondary');
                        statusBadge.textContent = isEnabled ? 'Включен' : 'Отключен';
                        showNotification(data.message, 'success');
                    } else {
                        toggle.checked = !isEnabled;
                        statusBadge.className = originalClasses;
                        statusBadge.textContent = originalText;
                        showNotification(data.message || 'Ошибка обновления статуса', 'danger');
                    }
                })
                .catch(error => {
                    toggle.checked = !isEnabled;
                    statusBadge.className = originalClasses;
                    statusBadge.textContent = originalText;
                    showNotification('Ошибка соединения с сервером', 'danger');
                    console.error('Error:', error);
                });
            });
        });

        function showNotification(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            document.body.appendChild(alertDiv);

            // Автоматически удаляем уведомление через 3 секунды
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
    });
</script>
@endpush
</x-app-layout>
