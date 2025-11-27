<x-app-layout>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Идеи пользователей</h2>
    <a href="{{ route('ideas.create') }}" class="btn btn-primary">Добавить идею</a>
</div>

@if($ideas->isNotEmpty())
    @foreach($ideas as $idea)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="card-title">{{ $idea->title }}</h5>
                        <p class="card-text">{{ $idea->description }}</p>
                        <div class="d-flex justify-content-between">
                            <span class="badge bg-success">
                                {{ $idea->status }}
                            </span>
                            <span class="text-muted small">{{ $idea->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                    </div>
                    @auth
                        @if(auth()->user()->isAdmin())
                        <div class="ms-3">
                            <form id="delete-idea-{{ $idea->id }}" method="POST" action="{{ route('admin.ideas.delete', $idea) }}" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                    x-data
                                    @click="$dispatch('confirm', {
                                        title: 'Удалить идею?',
                                        message: 'Идея будет удалена без возможности восстановления',
                                        type: 'danger',
                                        confirmText: 'Удалить',
                                        form: 'delete-idea-{{ $idea->id }}'
                                    })">
                                Удалить
                            </button>
                        </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="alert alert-info">Идей пока нет</div>
@endif
</x-app-layout>
