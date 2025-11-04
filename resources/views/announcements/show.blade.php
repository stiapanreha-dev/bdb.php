<x-app-layout>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <!-- Карточка объявления -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($announcement->type === 'supplier')
                                <span class="badge bg-success">Я поставщик</span>
                            @elseif($announcement->type === 'buyer')
                                <span class="badge bg-primary">Я покупатель</span>
                            @else
                                <span class="badge bg-info">Ищу дилера</span>
                            @endif
                        </div>
                        <div>
                            @auth
                                @if($announcement->user_id === Auth::id() || Auth::user()->isAdmin())
                                    <a href="{{ route('announcements.edit', $announcement->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Редактировать
                                    </a>
                                    <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Вы уверены, что хотите удалить это объявление?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Удалить
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h3 class="card-title mb-3">{{ $announcement->title }}</h3>

                    <div class="mb-3">
                        <strong>Описание:</strong>
                        <div class="mt-2 announcement-content">
                            @editorJsRender($announcement->description)
                        </div>
                    </div>

                    @if($announcement->images && count($announcement->images) > 0)
                        <div class="mb-3">
                            <strong>Дополнительные изображения:</strong>
                            <div class="row g-2 mt-2">
                                @foreach($announcement->images as $imageUrl)
                                    <div class="col-6 col-md-4">
                                        <a href="{{ $imageUrl }}" target="_blank">
                                            <img src="{{ $imageUrl }}" alt="Изображение" class="img-thumbnail" style="width: 100%; height: 150px; object-fit: cover; cursor: pointer;">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Дата публикации:</strong>
                        <span class="text-muted">{{ $announcement->published_at?->format('d.m.Y H:i') }}</span>
                    </div>

                    @if($announcement->register_as_purchase)
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Это объявление также зарегистрировано в базе закупок
                        </div>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <a href="{{ route('announcements.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Назад к списку
                </a>
            </div>
        </div>

        <!-- Сайдбар с формой заявки -->
        <div class="col-md-4">
            <!-- Контакты автора -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Контактная информация</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Компания:</strong>
                        <div>{{ $announcement->user->name ?? 'Не указано' }}</div>
                    </div>

                    @if($announcement->user->work_email ?? $announcement->user->email)
                        <div class="mb-3">
                            <strong>Email:</strong>
                            <div>
                                <a href="mailto:{{ $announcement->user->work_email ?? $announcement->user->email }}">
                                    {{ $announcement->user->work_email ?? $announcement->user->email }}
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($announcement->user->work_phone ?? $announcement->user->phone)
                        <div class="mb-3">
                            <strong>Телефон:</strong>
                            <div>
                                <a href="tel:{{ $announcement->user->work_phone ?? $announcement->user->phone }}">
                                    {{ $announcement->user->work_phone ?? $announcement->user->phone }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Форма отправки заявки -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Отправить заявку</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('announcements.inquiry', $announcement->id) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Ваше имя <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ Auth::user()->name ?? old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ Auth::user()->email ?? old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Телефон <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ Auth::user()->phone ?? old('phone') }}" required placeholder="+7 (999) 123-45-67">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Сообщение <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="4" required placeholder="Опишите ваш запрос">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-send"></i> Отправить заявку
                        </button>
                    </form>

                    <div class="alert alert-warning mt-3 mb-0" role="alert">
                        <small>
                            <i class="bi bi-info-circle"></i> Ваша заявка будет отправлена на email автора объявления
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .announcement-content {
        line-height: 1.6;
    }
    .announcement-content img {
        max-width: 100%;
        height: auto;
        margin: 10px 0;
        border-radius: 4px;
    }
    .announcement-content h1,
    .announcement-content h2,
    .announcement-content h3 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    .announcement-content ul,
    .announcement-content ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }
    .announcement-content blockquote {
        border-left: 4px solid #ddd;
        padding-left: 1rem;
        margin: 1rem 0;
        color: #666;
    }
    .announcement-content pre {
        background-color: #f5f5f5;
        padding: 1rem;
        border-radius: 4px;
        overflow-x: auto;
    }
    .announcement-content a {
        color: #0d6efd;
        text-decoration: underline;
    }
</style>
@endpush
</x-app-layout>
