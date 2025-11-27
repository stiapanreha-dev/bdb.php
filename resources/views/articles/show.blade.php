<x-app-layout>
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <!-- Карточка статьи -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <i class="bi bi-person"></i> {{ $article->user->name ?? 'Автор не указан' }}
                            </small>
                        </div>
                        <div>
                            @auth
                                @if($article->user_id === Auth::id() || Auth::user()->isAdmin())
                                    <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Редактировать
                                    </a>
                                    <form id="delete-article-form" action="{{ route('articles.destroy', $article->id) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            x-data
                                            @click="$dispatch('confirm', {
                                                title: 'Удалить статью?',
                                                message: 'Статья будет удалена без возможности восстановления',
                                                type: 'danger',
                                                confirmText: 'Удалить',
                                                form: 'delete-article-form'
                                            })">
                                        <i class="bi bi-trash"></i> Удалить
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h2 class="card-title mb-3">{{ $article->title }}</h2>

                    @if($article->images && count($article->images) > 0)
                        <div class="mb-4">
                            <img src="{{ $article->images[0] }}" alt="{{ $article->title }}" class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover;">
                        </div>
                    @endif

                    <div class="mb-3">
                        <div class="article-content">
                            @editorJsRender($article->content)
                        </div>
                    </div>

                    @if($article->images && count($article->images) > 1)
                        <div class="mb-3">
                            <strong>Дополнительные изображения:</strong>
                            <div class="row g-2 mt-2">
                                @foreach(array_slice($article->images, 1) as $imageUrl)
                                    <div class="col-6 col-md-4">
                                        <a href="{{ $imageUrl }}" target="_blank">
                                            <img src="{{ $imageUrl }}" alt="Изображение" class="img-thumbnail" style="width: 100%; height: 150px; object-fit: cover; cursor: pointer;">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mb-3 text-muted">
                        <small>
                            <i class="bi bi-calendar"></i> Опубликовано: {{ $article->published_at?->format('d.m.Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <a href="{{ route('articles.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Назад к списку
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .article-content {
        line-height: 1.8;
        font-size: 1.05rem;
    }
    .article-content img {
        max-width: 100%;
        height: auto;
        margin: 10px 0;
        border-radius: 4px;
    }
    .article-content h1,
    .article-content h2,
    .article-content h3 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    .article-content ul,
    .article-content ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }
    .article-content blockquote {
        border-left: 4px solid #ddd;
        padding-left: 1rem;
        margin: 1rem 0;
        color: #666;
    }
    .article-content pre {
        background-color: #f5f5f5;
        padding: 1rem;
        border-radius: 4px;
        overflow-x: auto;
    }
    .article-content a {
        color: #0d6efd;
        text-decoration: underline;
    }
    .article-content p {
        margin-bottom: 1rem;
    }
</style>
@endpush
</x-app-layout>
