<x-app-layout>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Новости</h2>
    @auth
        @if(auth()->user()->isAdmin())
            <a href="{{ route('news.create') }}" class="btn btn-primary">Добавить новость</a>
        @endif
    @endauth
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($news->isNotEmpty())
    @foreach($news as $item)
        <div class="card mb-3">
            @if($item->images && count($item->images) > 0)
                <div class="row g-0">
                    <div class="col-md-3">
                        <img src="{{ $item->images[0] }}" class="img-fluid rounded-start" style="width: 100%; height: 200px; object-fit: cover;" alt="{{ $item->title }}">
                    </div>
                    <div class="col-md-9">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="card-title">
                                        <a href="{{ route('news.show', $item) }}" class="text-decoration-none">{{ $item->title }}</a>
                                    </h5>
                                    <div class="card-text news-preview">
                                        @php
                                            $text = '';
                                            try {
                                                $decoded = json_decode($item->content);
                                                if (json_last_error() === JSON_ERROR_NONE && isset($decoded->blocks)) {
                                                    foreach ($decoded->blocks as $block) {
                                                        if (isset($block->data->text)) {
                                                            $text .= strip_tags($block->data->text) . ' ';
                                                        }
                                                    }
                                                } else {
                                                    $text = strip_tags($item->content);
                                                }
                                            } catch (\Exception $e) {
                                                $text = strip_tags($item->content);
                                            }
                                        @endphp
                                        {{ \Illuminate\Support\Str::limit($text, 200) }}
                                    </div>
                                    <p class="text-muted small mt-3">
                                        <i class="bi bi-calendar3"></i>
                                        {{ $item->published_at ? $item->published_at->format('d.m.Y H:i') : $item->created_at->format('d.m.Y H:i') }}
                                    </p>
                                </div>
                                @auth
                                    @if(auth()->user()->isAdmin())
                                    <div class="ms-3">
                                        <a href="{{ route('news.edit', $item) }}" class="btn btn-sm btn-outline-primary mb-1">
                                            <i class="bi bi-pencil"></i> Редактировать
                                        </a>
                                        <form id="delete-news-{{ $item->id }}" method="POST" action="{{ route('news.destroy', $item) }}" class="d-none">
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
                                                    form: 'delete-news-{{ $item->id }}'
                                                })">
                                            <i class="bi bi-trash"></i> Удалить
                                        </button>
                                    </div>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h5 class="card-title">
                                <a href="{{ route('news.show', $item) }}" class="text-decoration-none">{{ $item->title }}</a>
                            </h5>
                            <div class="card-text news-preview">
                                @php
                                    $text = '';
                                    try {
                                        $decoded = json_decode($item->content);
                                        if (json_last_error() === JSON_ERROR_NONE && isset($decoded->blocks)) {
                                            foreach ($decoded->blocks as $block) {
                                                if (isset($block->data->text)) {
                                                    $text .= strip_tags($block->data->text) . ' ';
                                                }
                                            }
                                        } else {
                                            $text = strip_tags($item->content);
                                        }
                                    } catch (\Exception $e) {
                                        $text = strip_tags($item->content);
                                    }
                                @endphp
                                {{ \Illuminate\Support\Str::limit($text, 300) }}
                            </div>
                            <p class="text-muted small mt-3">
                                <i class="bi bi-calendar3"></i>
                                {{ $item->published_at ? $item->published_at->format('d.m.Y H:i') : $item->created_at->format('d.m.Y H:i') }}
                            </p>
                        </div>
                        @auth
                            @if(auth()->user()->isAdmin())
                            <div class="ms-3">
                                <a href="{{ route('news.edit', $item) }}" class="btn btn-sm btn-outline-primary mb-1">
                                    <i class="bi bi-pencil"></i> Редактировать
                                </a>
                                <form id="delete-news-list-{{ $item->id }}" method="POST" action="{{ route('news.destroy', $item) }}" class="d-none">
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
                                            form: 'delete-news-list-{{ $item->id }}'
                                        })">
                                    <i class="bi bi-trash"></i> Удалить
                                </button>
                            </div>
                            @endif
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    @endforeach
@else
    <div class="alert alert-info">Новостей пока нет</div>
@endif

@push('styles')
<style>
    /* Editor.js content styles */
    .news-content {
        line-height: 1.6;
    }
    .news-content img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        margin: 15px 0;
    }
    .news-content h1, .news-content h2, .news-content h3, .news-content h4 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }
    .news-content p {
        margin-bottom: 1rem;
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
        margin-bottom: 1rem;
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
        margin: 1rem 0;
        border-collapse: collapse;
    }
    .news-content table th,
    .news-content table td {
        border: 1px solid #dee2e6;
        padding: 0.5rem;
    }
    .news-content table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
</style>
@endpush
</x-app-layout>
