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
                        <p class="card-text">{{ $item->content }}</p>
                        <p class="text-muted small">{{ $item->created_at->format('d.m.Y H:i') }}</p>
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
</x-app-layout>
