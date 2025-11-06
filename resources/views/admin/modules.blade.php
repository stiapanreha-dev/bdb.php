<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold mb-6">Управление модулями</h1>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <p class="text-gray-600 mb-4">
                        Управляйте доступностью модулей системы. Отключенные модули будут скрыты из меню
                        и недоступны для пользователей.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Модуль
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Описание
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Статус
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Настройки
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($modules as $module)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $module->module_name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $module->module_key }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600">
                                            {{ $module->description ?? 'Нет описания' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                class="sr-only peer module-toggle"
                                                data-module-key="{{ $module->module_key }}"
                                                {{ $module->is_enabled ? 'checked' : '' }}
                                            >
                                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                            <span class="ms-3 text-sm font-medium text-gray-900">
                                                {{ $module->is_enabled ? 'Включен' : 'Отключен' }}
                                            </span>
                                        </label>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($module->hasSettings())
                                            <a href="{{ $module->settings_route }}"
                                               class="text-blue-600 hover:text-blue-800">
                                                Настройки
                                            </a>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
                    const statusText = this.parentElement.querySelector('span');
                    const originalText = statusText.textContent;
                    statusText.textContent = 'Обновление...';

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
                            statusText.textContent = isEnabled ? 'Включен' : 'Отключен';
                            showNotification(data.message, 'success');
                        } else {
                            toggle.checked = !isEnabled;
                            statusText.textContent = originalText;
                            showNotification(data.message || 'Ошибка обновления статуса', 'error');
                        }
                    })
                    .catch(error => {
                        toggle.checked = !isEnabled;
                        statusText.textContent = originalText;
                        showNotification('Ошибка соединения с сервером', 'error');
                        console.error('Error:', error);
                    });
                });
            });

            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 ' +
                    (type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700');
                notification.textContent = message;
                document.body.appendChild(notification);
                setTimeout(() => notification.remove(), 3000);
            }
        });
    </script>
    @endpush
</x-app-layout>
