<x-app-layout>
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Статьи</h2>
        </div>
        <div class="col-md-4 text-end">
            @auth
                <a href="{{ route('articles.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Добавить статью
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary">
                    <i class="bi bi-box-arrow-in-right"></i> Войти чтобы добавить статью
                </a>
            @endauth
        </div>
    </div>

    <!-- Фильтры -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('articles.index') }}" class="row g-3">
                <div class="col-md-9">
                    <label for="search" class="form-label">Поиск</label>
                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Поиск по заголовку и содержанию">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Поиск
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Панель массовых действий для админа -->
    @auth
        @if(Auth::user()->isAdmin())
            <div class="mb-3">
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" id="selectAllBtn" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-check-square"></i> Выбрать все
                    </button>
                    <button type="button" id="bulkDeleteBtn" class="btn btn-sm btn-danger" style="display: none;">
                        <i class="bi bi-trash"></i> Удалить выбранные (<span id="selectedCount">0</span>)
                    </button>
                </div>
            </div>
        @endif
    @endauth

    <!-- Форма массового удаления -->
    <form id="bulkDeleteForm" method="POST" action="{{ route('articles.bulkDelete') }}" style="display: none;">
        @csrf
        @method('DELETE')
        <input type="hidden" name="ids" id="selectedIds" value="">
    </form>

    <!-- Список статей -->
    <div class="row">
        @forelse($articles as $article)
            <div class="col-md-6 mb-4">
                <div class="card h-100 article-card" data-article-id="{{ $article->id }}">
                    <!-- Чекбокс для админа -->
                    @auth
                        @if(Auth::user()->isAdmin())
                            <div class="article-checkbox">
                                <input type="checkbox" class="form-check-input article-select" value="{{ $article->id }}" id="article-{{ $article->id }}">
                            </div>
                        @endif
                    @endauth

                    <div class="d-flex h-100">
                        @if($article->images && count($article->images) > 0)
                            <div class="article-thumbnail">
                                <img src="{{ $article->images[0] }}" alt="Превью">
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <div class="card-body h-100 d-flex flex-column position-relative">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0 flex-grow-1 pe-3">
                                        <a href="{{ route('articles.show', $article->id) }}" class="text-decoration-none">
                                            {{ $article->title }}
                                        </a>
                                    </h5>
                                    <small class="text-muted text-nowrap">{{ $article->published_at?->format('d.m.Y') }}</small>
                                </div>
                                <div class="card-text article-preview flex-grow-1">
                                    @php
                                        $text = '';
                                        try {
                                            $decoded = json_decode($article->content);
                                            if (json_last_error() === JSON_ERROR_NONE && isset($decoded->blocks)) {
                                                // Извлекаем текст из блоков Editor.js
                                                foreach ($decoded->blocks as $block) {
                                                    if (isset($block->data)) {
                                                        if (isset($block->data->text)) {
                                                            $text .= strip_tags($block->data->text) . ' ';
                                                        } elseif ($block->type === 'list' && isset($block->data->items)) {
                                                            foreach ($block->data->items as $item) {
                                                                $text .= strip_tags($item) . ' ';
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
                                                $text = $article->content;
                                            }
                                        } catch (\Exception $e) {
                                            $text = $article->content;
                                        }
                                        $text = trim($text);
                                    @endphp
                                    {{ \Illuminate\Support\Str::limit($text, 200) }}
                                </div>
                                <div class="article-meta mt-2">
                                    <small class="text-muted">
                                        <i class="bi bi-person"></i> {{ $article->user->name ?? 'Автор не указан' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Статей не найдено. Будьте первым, кто создаст статью!
                </div>
            </div>
        @endforelse
    </div>

    <!-- Пагинация -->
    @if($articles->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $articles->links() }}
        </div>
    @endif
</div>

@push('styles')
<style>
    .article-card {
        position: relative;
    }
    .article-checkbox {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 10;
        background: white;
        padding: 5px;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .article-checkbox input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    .article-card.selected {
        box-shadow: 0 0 0 3px #0d6efd;
    }
    .article-thumbnail {
        flex-shrink: 0;
        width: 30%;
        max-width: 200px;
        overflow: hidden;
        border-radius: 0.375rem 0 0 0.375rem;
    }
    .article-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .article-preview {
        max-height: 150px;
        overflow: hidden;
        position: relative;
    }

    /* Мобильные устройства */
    @media (max-width: 768px) {
        .article-thumbnail {
            width: 35%;
            max-width: 120px;
        }
        .card-body {
            padding: 0.75rem !important;
        }
        .card-title {
            font-size: 1rem;
        }
    }
    .article-preview::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 50px;
        background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%);
    }
    .article-preview h1,
    .article-preview h2,
    .article-preview h3,
    .article-preview h4 {
        font-size: 1rem;
        margin: 0.5rem 0;
    }
    .article-preview p {
        margin: 0.25rem 0;
        font-size: 0.875rem;
        line-height: 1.5;
    }
    .article-preview ul,
    .article-preview ol {
        margin: 0.25rem 0;
        padding-left: 1.5rem;
        font-size: 0.875rem;
    }
    .article-preview img {
        max-width: 100%;
        height: auto;
        max-height: 100px;
        object-fit: cover;
        border-radius: 4px;
    }
    .article-preview blockquote {
        border-left: 3px solid #ddd;
        padding-left: 0.5rem;
        margin: 0.25rem 0;
        font-size: 0.875rem;
        color: #666;
    }
    .article-preview pre {
        display: none;
    }
    .article-preview table {
        font-size: 0.75rem;
        max-width: 100%;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.article-select');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    const selectedIdsInput = document.getElementById('selectedIds');

    if (!checkboxes.length) return; // Если нет чекбоксов (не админ), выходим

    // Обновление состояния кнопок и счетчика
    function updateUI() {
        const selectedCheckboxes = document.querySelectorAll('.article-select:checked');
        const count = selectedCheckboxes.length;

        selectedCountSpan.textContent = count;

        if (count > 0) {
            bulkDeleteBtn.style.display = 'inline-block';
        } else {
            bulkDeleteBtn.style.display = 'none';
        }

        // Обновление визуального выделения карточек
        checkboxes.forEach(checkbox => {
            const card = checkbox.closest('.article-card');
            if (checkbox.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });

        // Обновление текста кнопки "Выбрать все"
        if (count === checkboxes.length) {
            selectAllBtn.innerHTML = '<i class="bi bi-square"></i> Снять выделение';
        } else {
            selectAllBtn.innerHTML = '<i class="bi bi-check-square"></i> Выбрать все';
        }
    }

    // Обработчик изменения чекбоксов
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateUI);
    });

    // Кнопка "Выбрать все / Снять выделение"
    selectAllBtn.addEventListener('click', function() {
        const allChecked = document.querySelectorAll('.article-select:checked').length === checkboxes.length;

        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });

        updateUI();
    });

    // Кнопка "Удалить выбранные"
    bulkDeleteBtn.addEventListener('click', function() {
        const selectedCheckboxes = document.querySelectorAll('.article-select:checked');
        const count = selectedCheckboxes.length;

        if (count === 0) return;

        // Собираем ID выбранных статей
        const ids = Array.from(selectedCheckboxes).map(cb => cb.value);
        selectedIdsInput.value = JSON.stringify(ids);

        // Показываем модальное окно подтверждения
        window.dispatchEvent(new CustomEvent('confirm', {
            detail: {
                title: 'Удалить статьи?',
                message: `Будет удалено статей: ${count}`,
                type: 'danger',
                confirmText: 'Удалить',
                form: bulkDeleteForm.id
            }
        }));
    });
});
</script>
@endpush
</x-app-layout>
