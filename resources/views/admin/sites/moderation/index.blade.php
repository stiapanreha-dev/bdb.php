<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Модерация сайтов</h2>
            <div class="d-flex gap-2">
                <span class="badge bg-warning fs-6">Ожидают: {{ $counts['pending'] }}</span>
                <span class="badge bg-success fs-6">Одобрено: {{ $counts['approved'] }}</span>
                <span class="badge bg-danger fs-6">Отклонено: {{ $counts['rejected'] }}</span>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Status filter --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <div class="d-flex gap-2">
            <a href="{{ route('admin.sites.moderation.index') }}"
               class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
                Все
            </a>
            <a href="{{ route('admin.sites.moderation.index', ['status' => 'pending']) }}"
               class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                Ожидают ({{ $counts['pending'] }})
            </a>
            <a href="{{ route('admin.sites.moderation.index', ['status' => 'approved']) }}"
               class="btn btn-sm {{ request('status') == 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                Одобренные
            </a>
            <a href="{{ route('admin.sites.moderation.index', ['status' => 'rejected']) }}"
               class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                Отклоненные
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 60px;">Лого</th>
                        <th>Сайт</th>
                        <th>Категория</th>
                        <th>Автор</th>
                        <th style="width: 100px;">Статус</th>
                        <th style="width: 120px;">Дата</th>
                        <th style="width: 180px;">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sites as $site)
                    <tr class="{{ $site->isPending() ? 'table-warning' : '' }}">
                        <td>{{ $site->id }}</td>
                        <td>
                            @if($site->logo)
                                <img src="{{ asset('storage/' . $site->logo) }}"
                                     alt="{{ $site->name }}"
                                     class="rounded-circle"
                                     style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-globe text-white"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.sites.moderation.show', $site) }}" class="text-decoration-none">
                                <strong>{{ $site->name }}</strong>
                            </a>
                            <br>
                            <small class="text-muted">
                                <a href="{{ $site->url }}" target="_blank" class="text-muted">
                                    {{ Str::limit($site->url, 40) }}
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </small>
                        </td>
                        <td>
                            @if($site->category)
                                <span class="badge bg-info">{{ $site->category->name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            {{ $site->user->name ?? 'Удален' }}
                            <br>
                            <small class="text-muted">{{ $site->contact_email }}</small>
                        </td>
                        <td>
                            @if($site->isPending())
                                <span class="badge bg-warning">На модерации</span>
                            @elseif($site->isApproved())
                                <span class="badge bg-success">Одобрен</span>
                            @else
                                <span class="badge bg-danger">Отклонен</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $site->created_at->format('d.m.Y H:i') }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.sites.moderation.show', $site) }}"
                                   class="btn btn-sm btn-outline-primary" title="Просмотр">
                                    <i class="bi bi-eye"></i>
                                </a>

                                @if($site->isPending())
                                    <form method="POST" action="{{ route('admin.sites.moderation.approve', $site) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Одобрить">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Отклонить"
                                            data-bs-toggle="modal" data-bs-target="#rejectModal{{ $site->id }}">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                @endif

                                <form id="delete-site-{{ $site->id }}" method="POST"
                                      action="{{ route('admin.sites.moderation.destroy', $site) }}" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Удалить"
                                        x-data
                                        @click="$dispatch('confirm', {
                                            title: 'Удалить сайт?',
                                            message: '{{ $site->name }}',
                                            type: 'danger',
                                            confirmText: 'Удалить',
                                            form: 'delete-site-{{ $site->id }}'
                                        })">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            {{-- Reject Modal --}}
                            <div class="modal fade" id="rejectModal{{ $site->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.sites.moderation.reject', $site) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Отклонить сайт</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>{{ $site->name }}</strong></p>
                                                <div class="mb-3">
                                                    <label for="reason{{ $site->id }}" class="form-label">Причина отклонения <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="reason{{ $site->id }}" name="reason" rows="3" required
                                                              placeholder="Укажите причину отклонения..."></textarea>
                                                    <div class="form-text">Будет отправлена на email автору</div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                                <button type="submit" class="btn btn-danger">Отклонить</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                @if(request('status') == 'pending')
                                    Нет сайтов на модерации
                                @else
                                    Сайтов не найдено
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $sites->links() }}
    </div>
</div>
</x-app-layout>
