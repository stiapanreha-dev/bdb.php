@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Рассылки по ключевым словам</h1>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Информация о сервисе -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-blue-900 mb-3">О сервисе рассылок</h2>
            <ul class="list-disc list-inside text-blue-800 space-y-2">
                <li>Получайте автоматические рассылки закупок по вашим ключевым словам</li>
                <li>Рассылки отправляются каждые 3 часа на ваш email</li>
                <li>В письме будет Excel файл со всеми найденными закупками</li>
                <li>Стоимость: <strong>+500₽/месяц</strong> к основной подписке</li>
            </ul>
        </div>

        <!-- Настройки рассылки -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Настройки</h2>

            <form method="POST" action="{{ route('newsletters.store') }}">
                @csrf

                <!-- Включить/выключить рассылку -->
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               {{ $newsletter->is_active ? 'checked' : '' }}
                               class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500">
                        <span class="ml-2 text-sm font-medium text-gray-900">Включить рассылку</span>
                    </label>
                </div>

                <!-- Дополнительный email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email для рассылок (опционально)
                    </label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email', $newsletter->email) }}"
                           placeholder="Оставьте пустым для использования email аккаунта"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    <p class="mt-1 text-sm text-gray-500">
                        По умолчанию: {{ $user->email }}
                    </p>
                </div>

                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md transition">
                    Сохранить настройки
                </button>
            </form>
        </div>

        <!-- Ключевые слова -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Ключевые слова</h2>

            <form method="POST" action="{{ route('newsletters.keywords') }}" id="keywordsForm">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Введите ключевые слова для поиска закупок
                    </label>
                    <p class="text-sm text-gray-500 mb-3">
                        Каждая строка - отдельный набор ключевых слов. В одной строке можно указать несколько слов через пробел или запятую.
                        Например: "программное обеспечение microsoft" или "компьютеры, ноутбуки, принтеры"
                    </p>

                    <div id="keywordsContainer" class="space-y-2">
                        @forelse($newsletter->keywords as $index => $keyword)
                            <div class="keyword-row flex gap-2">
                                <input type="text"
                                       name="keywords[]"
                                       value="{{ $keyword->keywords }}"
                                       placeholder="Например: программное обеспечение"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                <button type="button"
                                        onclick="removeKeywordRow(this)"
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">
                                    Удалить
                                </button>
                            </div>
                        @empty
                            <div class="keyword-row flex gap-2">
                                <input type="text"
                                       name="keywords[]"
                                       placeholder="Например: программное обеспечение"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                <button type="button"
                                        onclick="removeKeywordRow(this)"
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">
                                    Удалить
                                </button>
                            </div>
                        @endforelse
                    </div>

                    <button type="button"
                            onclick="addKeywordRow()"
                            class="mt-3 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                        + Добавить строку
                    </button>
                </div>

                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md transition">
                    Сохранить ключевые слова
                </button>
            </form>
        </div>

        <!-- История рассылок -->
        @if($newsletter->logs->isNotEmpty())
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">История рассылок</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата отправки</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Найдено закупок</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($newsletter->logs as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->sent_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->zakupki_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($log->status === 'success')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Успешно
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Ошибка
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($newsletter->last_sent_at)
            <p class="mt-4 text-sm text-gray-600">
                Последняя рассылка: <strong>{{ $newsletter->last_sent_at->format('d.m.Y H:i') }}</strong>
            </p>
            @endif
        </div>
        @endif
    </div>
</div>

<script>
function addKeywordRow() {
    const container = document.getElementById('keywordsContainer');
    const row = document.createElement('div');
    row.className = 'keyword-row flex gap-2';
    row.innerHTML = `
        <input type="text"
               name="keywords[]"
               placeholder="Например: программное обеспечение"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
        <button type="button"
                onclick="removeKeywordRow(this)"
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">
            Удалить
        </button>
    `;
    container.appendChild(row);
}

function removeKeywordRow(button) {
    const container = document.getElementById('keywordsContainer');
    const rows = container.querySelectorAll('.keyword-row');

    // Don't remove if it's the last row
    if (rows.length > 1) {
        button.closest('.keyword-row').remove();
    } else {
        alert('Должна остаться хотя бы одна строка');
    }
}
</script>
@endsection
