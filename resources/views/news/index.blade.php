<x-app-layout>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Новости</h2>
    @auth
        @if(auth()->user()->isAdmin())
            <a href="{{ route('news.create') }}" class="btn btn-primary">Добавить новость</a>
        @endif
    @endauth
</div>

@if($news->isNotEmpty())
    @foreach($news as $item)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="card-title">{{ $item->title }}</h5>
                        <div class="card-text news-content">{!! $item->content !!}</div>
                        <p class="text-muted small mt-3">{{ $item->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    @auth
                        @if(auth()->user()->isAdmin())
                        <div>
                            <form method="POST" action="{{ route('news.destroy', $item) }}" onsubmit="return confirm('Вы уверены, что хотите удалить эту новость?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                            </form>
                        </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="alert alert-info">Новостей пока нет</div>
@endif

@push('styles')
<style>
    .news-content img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        margin: 10px 0;
    }
    .news-content h1, .news-content h2, .news-content h3 {
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }
    .news-content p {
        margin-bottom: 0.5rem;
    }
    .news-content a {
        color: #0d6efd;
        text-decoration: underline;
    }
    .news-content blockquote {
        border-left: 4px solid #ddd;
        padding-left: 1rem;
        margin: 1rem 0;
        color: #666;
    }
    .news-content ul, .news-content ol {
        padding-left: 2rem;
        margin-bottom: 0.5rem;
    }
</style>
@endpush
</x-app-layout>
