<x-app-layout>
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Доска объявлений</h2>
        </div>
        <div class="col-md-4 text-end">
            @auth
                <a href="{{ route('announcements.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Добавить объявление
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary">
                    <i class="bi bi-box-arrow-in-right"></i> Войти чтобы добавить объявление
                </a>
            @endauth
        </div>
    </div>

    <!-- Фильтры -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('announcements.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="type" class="form-label">Тип объявления</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">Все</option>
                        <option value="supplier" {{ request('type') === 'supplier' ? 'selected' : '' }}>Я поставщик</option>
                        <option value="buyer" {{ request('type') === 'buyer' ? 'selected' : '' }}>Я покупатель</option>
                        <option value="dealer" {{ request('type') === 'dealer' ? 'selected' : '' }}>Ищу дилера</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="category" class="form-label">Категория</label>
                    <input type="text" name="category" id="category" class="form-control" value="{{ request('category') }}" placeholder="Категория">
                </div>

                <div class="col-md-4">
                    <label for="search" class="form-label">Поиск</label>
                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Поиск по заголовку и описанию">
                </div>

                <div class="col-md-2 d-flex align-items-end">
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

    <!-- Список объявлений -->
    <div class="row">
        @forelse($announcements as $announcement)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if($announcement->type === 'supplier')
                                    <span class="badge bg-success">Я поставщик</span>
                                @elseif($announcement->type === 'buyer')
                                    <span class="badge bg-primary">Я покупатель</span>
                                @else
                                    <span class="badge bg-info">Ищу дилера</span>
                                @endif

                                @if($announcement->category)
                                    <span class="badge bg-secondary">{{ $announcement->category }}</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $announcement->published_at?->format('d.m.Y') }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('announcements.show', $announcement->id) }}" class="text-decoration-none">
                                {{ $announcement->title }}
                            </a>
                        </h5>
                        <div class="card-text announcement-preview">
                            @editorJsRender($announcement->description)
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-person"></i> {{ $announcement->user->name ?? 'Не указано' }}
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('announcements.show', $announcement->id) }}" class="btn btn-sm btn-outline-primary">
                            Подробнее <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Объявлений не найдено. Будьте первым, кто разместит объявление!
                </div>
            </div>
        @endforelse
    </div>

    <!-- Пагинация -->
    @if($announcements->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $announcements->links() }}
        </div>
    @endif
</div>

@push('styles')
<style>
    .announcement-preview {
        max-height: 150px;
        overflow: hidden;
        position: relative;
        margin-bottom: 1rem;
    }
    .announcement-preview::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 50px;
        background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%);
    }
    .announcement-preview h1,
    .announcement-preview h2,
    .announcement-preview h3,
    .announcement-preview h4 {
        font-size: 1rem;
        margin: 0.5rem 0;
    }
    .announcement-preview p {
        margin: 0.25rem 0;
        font-size: 0.875rem;
        line-height: 1.5;
    }
    .announcement-preview ul,
    .announcement-preview ol {
        margin: 0.25rem 0;
        padding-left: 1.5rem;
        font-size: 0.875rem;
    }
    .announcement-preview img {
        max-width: 100%;
        height: auto;
        max-height: 100px;
        object-fit: cover;
        border-radius: 4px;
    }
    .announcement-preview blockquote {
        border-left: 3px solid #ddd;
        padding-left: 0.5rem;
        margin: 0.25rem 0;
        font-size: 0.875rem;
        color: #666;
    }
    .announcement-preview pre {
        display: none;
    }
    .announcement-preview table {
        font-size: 0.75rem;
        max-width: 100%;
    }
</style>
@endpush
</x-app-layout>
