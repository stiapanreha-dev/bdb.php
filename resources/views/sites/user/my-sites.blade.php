<x-app-layout>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Мои сайты</h2>
            <a href="{{ route('sites.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Добавить сайт
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
                        <th style="width: 60px;">Лого</th>
                        <th>Сайт</th>
                        <th>Категория</th>
                        <th style="width: 120px;">Статус</th>
                        <th style="width: 100px;">Просмотры</th>
                        <th style="width: 120px;">Дата</th>
                        <th style="width: 150px;">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sites as $site)
                    <tr>
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
                            <strong>{{ $site->name }}</strong>
                            <br>
                            <small class="text-muted">
                                <a href="{{ $site->url }}" target="_blank">
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
                            @if($site->isPending())
                                <span class="badge bg-warning">На модерации</span>
                            @elseif($site->isApproved())
                                <span class="badge bg-success">Одобрен</span>
                            @else
                                <span class="badge bg-danger" title="{{ $site->moderation_comment }}">Отклонен</span>
                            @endif
                        </td>
                        <td>{{ $site->views_count }}</td>
                        <td>
                            <small>{{ $site->created_at->format('d.m.Y') }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @if($site->isApproved())
                                <a href="{{ route('sites.show', $site->slug) }}"
                                   class="btn btn-sm btn-outline-primary" title="Просмотр">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @endif
                                <a href="{{ route('sites.edit', $site->id) }}"
                                   class="btn btn-sm btn-outline-secondary" title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form id="delete-site-{{ $site->id }}" method="POST"
                                      action="{{ route('sites.destroy', $site->id) }}" class="d-none">
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
                        </td>
                    </tr>

                    @if($site->isRejected() && $site->moderation_comment)
                    <tr class="table-warning">
                        <td colspan="7">
                            <small><strong>Причина отклонения:</strong> {{ $site->moderation_comment }}</small>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-globe fs-1 d-block mb-2"></i>
                                Вы еще не добавили ни одного сайта
                            </div>
                            <a href="{{ route('sites.create') }}" class="btn btn-primary mt-2">
                                Добавить первый сайт
                            </a>
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
