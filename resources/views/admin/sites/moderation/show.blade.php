<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.sites.moderation.index') }}">Модерация сайтов</a></li>
                <li class="breadcrumb-item active">{{ $site->name }}</li>
            </ol>
        </nav>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Информация о сайте</h5>
                @if($site->isPending())
                    <span class="badge bg-warning">На модерации</span>
                @elseif($site->isApproved())
                    <span class="badge bg-success">Одобрен</span>
                @else
                    <span class="badge bg-danger">Отклонен</span>
                @endif
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start mb-4">
                    @if($site->logo)
                        <img src="{{ asset('storage/' . $site->logo) }}"
                             alt="{{ $site->name }}"
                             class="rounded-circle me-3"
                             style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width: 80px; height: 80px;">
                            <i class="bi bi-globe text-white fs-2"></i>
                        </div>
                    @endif
                    <div>
                        <h3 class="mb-1">{{ $site->name }}</h3>
                        <a href="{{ $site->url }}" target="_blank" class="text-primary">
                            {{ $site->url }} <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    </div>
                </div>

                <table class="table table-borderless">
                    <tr>
                        <th style="width: 150px;">Категория:</th>
                        <td>
                            @if($site->category)
                                <span class="badge bg-info">{{ $site->category->name }}</span>
                                @if($site->category->parent)
                                    <small class="text-muted">({{ $site->category->parent->name }})</small>
                                @endif
                            @else
                                <span class="text-muted">Не указана</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Email для связи:</th>
                        <td>
                            <a href="mailto:{{ $site->contact_email }}">{{ $site->contact_email }}</a>
                        </td>
                    </tr>
                    <tr>
                        <th>Автор:</th>
                        <td>
                            @if($site->user)
                                {{ $site->user->name }} ({{ $site->user->email }})
                            @else
                                <span class="text-muted">Пользователь удален</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Дата создания:</th>
                        <td>{{ $site->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                    @if($site->moderated_at)
                    <tr>
                        <th>Модерация:</th>
                        <td>
                            {{ $site->moderated_at->format('d.m.Y H:i') }}
                            @if($site->moderator)
                                ({{ $site->moderator->name }})
                            @endif
                        </td>
                    </tr>
                    @endif
                    @if($site->moderation_comment)
                    <tr>
                        <th>Комментарий:</th>
                        <td>{{ $site->moderation_comment }}</td>
                    </tr>
                    @endif
                </table>

                <hr>

                <h5>Описание</h5>
                <div class="border rounded p-3 bg-light">
                    @if($site->description)
                        @editorJsRender($site->description)
                    @else
                        <p class="text-muted mb-0">Описание не указано</p>
                    @endif
                </div>

                @if($site->images && count($site->images) > 0)
                <hr>
                <h5>Дополнительные изображения</h5>
                <div class="row g-2">
                    @foreach($site->images as $image)
                    <div class="col-md-4">
                        <a href="{{ $image['url'] ?? asset('storage/' . $image['path']) }}" target="_blank">
                            <img src="{{ $image['url'] ?? asset('storage/' . $image['path']) }}"
                                 class="img-thumbnail"
                                 style="width: 100%; height: 150px; object-fit: cover;">
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        {{-- Moderation actions --}}
        @if($site->isPending())
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Действия модератора</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.sites.moderation.approve', $site) }}" class="mb-3">
                    @csrf
                    <div class="mb-2">
                        <label for="approve-comment" class="form-label">Комментарий (необязательно)</label>
                        <textarea class="form-control" id="approve-comment" name="comment" rows="2"
                                  placeholder="Комментарий к одобрению..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-lg me-1"></i>Одобрить
                    </button>
                </form>

                <hr>

                <form method="POST" action="{{ route('admin.sites.moderation.reject', $site) }}">
                    @csrf
                    <div class="mb-2">
                        <label for="reject-reason" class="form-label">Причина отклонения <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject-reason" name="reason" rows="3" required
                                  placeholder="Укажите причину отклонения..."></textarea>
                        <div class="form-text">Будет отправлена на email автору</div>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-x-lg me-1"></i>Отклонить
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Danger zone --}}
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Опасная зона</h5>
            </div>
            <div class="card-body">
                <form id="delete-site-form" method="POST"
                      action="{{ route('admin.sites.moderation.destroy', $site) }}" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
                <button type="button" class="btn btn-outline-danger w-100"
                        x-data
                        @click="$dispatch('confirm', {
                            title: 'Удалить сайт?',
                            message: '{{ $site->name }}',
                            type: 'danger',
                            confirmText: 'Удалить навсегда',
                            form: 'delete-site-form'
                        })">
                    <i class="bi bi-trash me-1"></i>Удалить сайт
                </button>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
