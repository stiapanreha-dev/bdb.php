<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>Модерация идей</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @forelse($ideas as $idea)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $idea->title }}</strong>
                    @if($idea->user)
                        <small class="text-muted">— {{ $idea->user->name }} ({{ $idea->user->username }})</small>
                    @else
                        <small class="text-muted">— Анонимный</small>
                    @endif
                </div>
                <div>
                    @if($idea->status === 'pending')
                        <span class="badge bg-warning">На рассмотрении</span>
                    @elseif($idea->status === 'approved')
                        <span class="badge bg-success">Одобрена</span>
                    @else
                        <span class="badge bg-danger">Отклонена</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <p>{{ $idea->description }}</p>
                <p class="text-muted mb-0">
                    <small>Создано: {{ $idea->created_at->format('d.m.Y H:i') }}</small>
                </p>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <form method="POST" action="{{ route('admin.ideas.update-status', $idea) }}" class="d-inline">
                    @csrf
                    <div class="btn-group" role="group">
                        <button type="submit" name="status" value="pending" class="btn btn-sm btn-warning">
                            На рассмотрении
                        </button>
                        <button type="submit" name="status" value="approved" class="btn btn-sm btn-success">
                            Одобрить
                        </button>
                        <button type="submit" name="status" value="rejected" class="btn btn-sm btn-secondary">
                            Отклонить
                        </button>
                    </div>
                </form>
                <form method="POST" action="{{ route('admin.ideas.delete', $idea) }}" class="d-inline" 
                      onsubmit="return confirm('Вы уверены что хотите удалить эту идею?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        Удалить
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="alert alert-info">
            Идей пока нет
        </div>
        @endforelse

        <div class="mt-3">
            {{ $ideas->links() }}
        </div>
    </div>
</div>
</x-app-layout>
