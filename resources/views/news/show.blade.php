<x-app-layout>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Карточка новости -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Новость</h5>
                        <div>
                            @auth
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('news.edit', $news) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Редактировать
                                    </a>
                                    <form id="delete-news-form" action="{{ route('news.destroy', $news) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            x-data
                                            @click="$dispatch('confirm', {
                                                title: 'Удалить новость?',
                                                message: 'Новость будет удалена без возможности восстановления',
                                                type: 'danger',
                                                confirmText: 'Удалить',
                                                form: 'delete-news-form'
                                            })">
                                        <i class="bi bi-trash"></i> Удалить
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h2 class="card-title mb-3">{{ $news->title }}</h2>

                    <div class="mb-3">
                        <span class="text-muted">
                            <i class="bi bi-calendar3"></i>
                            {{ $news->published_at ? $news->published_at->format('d.m.Y H:i') : $news->created_at->format('d.m.Y H:i') }}
                        </span>
                    </div>

                    <hr>

                    @if($news->images && count($news->images) > 0)
                        <div class="mb-4">
                            <div class="row g-2 justify-content-center">
                                @foreach($news->images as $imageUrl)
                                    <div class="col-6 col-md-4">
                                        <a href="{{ $imageUrl }}" target="_blank">
                                            <img src="{{ $imageUrl }}" alt="Изображение" class="img-thumbnail" style="width: 100%; height: 200px; object-fit: cover; cursor: pointer;">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="news-content mt-4">
                        @editorJsRender($news->content)
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <a href="{{ route('news.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Назад к списку
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .news-content {
        line-height: 1.6;
        font-size: 1.05rem;
    }
    .news-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 20px 0;
    }
    .news-content h1 {
        font-size: 2rem;
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    .news-content h2 {
        font-size: 1.75rem;
        margin-top: 1.75rem;
        margin-bottom: 0.875rem;
        font-weight: 600;
    }
    .news-content h3 {
        font-size: 1.5rem;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }
    .news-content h4 {
        font-size: 1.25rem;
        margin-top: 1.25rem;
        margin-bottom: 0.625rem;
        font-weight: 600;
    }
    .news-content p {
        margin-bottom: 1.2rem;
    }
    .news-content a {
        color: #0d6efd;
        text-decoration: underline;
    }
    .news-content blockquote {
        border-left: 4px solid #dee2e6;
        padding-left: 1.5rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: #6c757d;
    }
    .news-content ul, .news-content ol {
        padding-left: 2rem;
        margin-bottom: 1.2rem;
    }
    .news-content li {
        margin-bottom: 0.5rem;
    }
    .news-content pre {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.375rem;
        overflow-x: auto;
        margin: 1rem 0;
    }
    .news-content code {
        background-color: #f8f9fa;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-family: 'Courier New', monospace;
        font-size: 0.9em;
    }
    .news-content table {
        width: 100%;
        margin: 1.5rem 0;
        border-collapse: collapse;
    }
    .news-content table th,
    .news-content table td {
        border: 1px solid #dee2e6;
        padding: 0.75rem;
    }
    .news-content table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
</style>
@endpush
</x-app-layout>
