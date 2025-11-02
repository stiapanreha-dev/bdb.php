<x-app-layout>
<h2 class="mb-4">Рассылки по ключевым словам</h2>

    <!-- Информация о сервисе -->
    <div class="card mb-3 border-primary">
        <div class="card-body bg-light">
            <h5 class="card-title text-primary">О сервисе рассылок</h5>
            <ul class="mb-0">
                <li>Получайте автоматические рассылки закупок по вашим ключевым словам</li>
                <li>Рассылки отправляются каждые 3 часа на ваш email</li>
                <li>В письме будет Excel файл со всеми найденными закупками</li>
                <li>Стоимость: <strong>+500₽/месяц</strong> к основной подписке</li>
            </ul>
        </div>
    </div>

    <!-- Настройки рассылки -->
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">Настройки</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('newsletters.store') }}">
                @csrf

                <!-- Включить/выключить рассылку -->
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               {{ $newsletter->is_active ? 'checked' : '' }}
                               class="form-check-input"
                               id="isActive">
                        <label class="form-check-label" for="isActive">
                            <strong>Включить рассылку</strong>
                        </label>
                    </div>
                </div>

                <!-- Дополнительный email -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        Email для рассылок (опционально)
                    </label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email', $newsletter->email) }}"
                           placeholder="Оставьте пустым для использования email аккаунта"
                           class="form-control">
                    <div class="form-text">
                        По умолчанию: {{ $user->email }}
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    Сохранить настройки
                </button>
            </form>
        </div>
    </div>

    <!-- Ключевые слова -->
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">Ключевые слова</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('newsletters.keywords') }}" id="keywordsForm">
                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Введите ключевые слова для поиска закупок
                    </label>
                    <div class="alert alert-info">
                        <small>
                            Каждая строка - отдельный набор ключевых слов. В одной строке можно указать несколько слов через пробел или запятую.
                            <br>Например: "программное обеспечение microsoft" или "компьютеры, ноутбуки, принтеры"
                        </small>
                    </div>

                    <div id="keywordsContainer" class="mb-3">
                        @forelse($newsletter->keywords as $index => $keyword)
                            <div class="keyword-row mb-2">
                                <div class="input-group">
                                    <input type="text"
                                           name="keywords[]"
                                           value="{{ $keyword->keywords }}"
                                           placeholder="Например: программное обеспечение"
                                           class="form-control">
                                    <button type="button"
                                            onclick="removeKeywordRow(this)"
                                            class="btn btn-outline-danger">
                                        Удалить
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="keyword-row mb-2">
                                <div class="input-group">
                                    <input type="text"
                                           name="keywords[]"
                                           placeholder="Например: программное обеспечение"
                                           class="form-control">
                                    <button type="button"
                                            onclick="removeKeywordRow(this)"
                                            class="btn btn-outline-danger">
                                        Удалить
                                    </button>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <button type="button"
                            onclick="addKeywordRow()"
                            class="btn btn-secondary">
                        + Добавить строку
                    </button>
                </div>

                <button type="submit" class="btn btn-primary">
                    Сохранить ключевые слова
                </button>
            </form>
        </div>
    </div>

    <!-- История рассылок -->
    @if($newsletter->logs->isNotEmpty())
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">История рассылок</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Дата отправки</th>
                            <th>Найдено закупок</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($newsletter->logs as $log)
                        <tr>
                            <td>{{ $log->sent_at->format('d.m.Y H:i') }}</td>
                            <td>{{ $log->zakupki_count }}</td>
                            <td>
                                @if($log->status === 'success')
                                    <span class="badge bg-success">Успешно</span>
                                @else
                                    <span class="badge bg-danger">Ошибка</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($newsletter->last_sent_at)
            <p class="text-muted mb-0">
                Последняя рассылка: <strong>{{ $newsletter->last_sent_at->format('d.m.Y H:i') }}</strong>
            </p>
            @endif
        </div>
    </div>
    @endif

@push('scripts')
<script>
function addKeywordRow() {
    const container = document.getElementById('keywordsContainer');
    const row = document.createElement('div');
    row.className = 'keyword-row mb-2';
    row.innerHTML = `
        <div class="input-group">
            <input type="text"
                   name="keywords[]"
                   placeholder="Например: программное обеспечение"
                   class="form-control">
            <button type="button"
                    onclick="removeKeywordRow(this)"
                    class="btn btn-outline-danger">
                Удалить
            </button>
        </div>
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
@endpush
</x-app-layout>
