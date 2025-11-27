<x-app-layout>
<div class="row">
    <div class="col-md-8 offset-md-2">
        <h2 class="mb-4">Профиль пользователя</h2>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Личная информация</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Фото профиля -->
                    <div class="col-md-3 text-center mb-3">
                        <div class="mb-3">
                            @if($user->avatar ?? false)
                                <img src="{{ asset('storage/avatars/' . $user->avatar) }}"
                                     alt="Фото профиля"
                                     class="rounded-circle img-thumbnail"
                                     style="width: 150px; height: 150px; object-fit: cover;"
                                     id="avatar-preview">
                            @else
                                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center"
                                     style="width: 150px; height: 150px;"
                                     id="avatar-placeholder">
                                    <i class="bi bi-person-fill text-white" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                        </div>
                        <small class="text-muted d-block mb-2">{{ $user->username }}</small>

                        <!-- Форма загрузки фото -->
                        <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data" id="avatar-form">
                            @csrf
                            <input type="file" name="avatar" id="avatar-input" class="d-none" accept="image/*">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('avatar-input').click()">
                                <i class="bi bi-camera"></i> Загрузить фото
                            </button>
                            @if($user->avatar)
                                <form id="delete-avatar-form" action="{{ route('profile.avatar.delete') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2"
                                        x-data
                                        @click="$dispatch('confirm', {
                                            title: 'Удалить фото профиля?',
                                            message: 'Фото будет удалено без возможности восстановления',
                                            type: 'danger',
                                            confirmText: 'Удалить',
                                            form: 'delete-avatar-form'
                                        })">
                                    <i class="bi bi-trash"></i> Удалить
                                </button>
                            @endif
                        </form>
                    </div>

                    <!-- Основная информация -->
                    <div class="col-md-9">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Имя пользователя:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $user->username }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Email:</strong>
                            </div>
                            <div class="col-md-4">
                                {{ $user->email }}
                            </div>
                            <div class="col-md-4">
                                @if($user->email_verified)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Подтвержден
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="bi bi-exclamation-circle"></i> Не подтвержден
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Телефон:</strong>
                            </div>
                            <div class="col-md-4">
                                {{ $user->phone ?? '-' }}
                            </div>
                            <div class="col-md-4">
                                @if($user->phone)
                                    @if($user->phone_verified)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Подтвержден
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="bi bi-exclamation-circle"></i> Не подтвержден
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Не указан</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Роль:</strong>
                            </div>
                            <div class="col-md-8">
                                @if($user->isAdmin())
                                    <span class="badge bg-danger">Администратор</span>
                                @else
                                    <span class="badge bg-primary">Пользователь</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Дата регистрации:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $user->created_at ? $user->created_at->format('d.m.Y H:i') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Рабочие контакты</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.work-contacts.update') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="work_email" class="form-label"><strong>Рабочий Email:</strong></label>
                        </div>
                        <div class="col-md-8">
                            <input type="email" class="form-control @error('work_email') is-invalid @enderror" id="work_email" name="work_email" value="{{ old('work_email', $user->work_email) }}" placeholder="work@example.com">
                            @error('work_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Этот email будет отображаться в ваших объявлениях</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="work_phone" class="form-label"><strong>Рабочий телефон:</strong></label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control @error('work_phone') is-invalid @enderror" id="work_phone" name="work_phone" value="{{ old('work_phone', $user->work_phone) }}" placeholder="+7 (999) 123-45-67">
                            @error('work_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Этот телефон будет отображаться в ваших объявлениях</small>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Сохранить рабочие контакты
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Баланс</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Текущий баланс:</strong>
                    </div>
                    <div class="col-md-8">
                        <h4 class="mb-0">
                            {{ number_format($user->balance, 2, '.', ',') }} ₽
                        </h4>
                    </div>
                </div>

                @if($user->balance <= 0)
                <div class="alert alert-info">
                    <strong>Обратите внимание:</strong> Для полного доступа к данным необходимо пополнить баланс.
                    <div class="mt-2">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#balanceModal">
                            Пополнить баланс
                        </button>
                    </div>
                </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-primary btn-sm me-2">
                        Пополнить баланс
                    </a>
                    <a href="{{ route('payment.history') }}" class="btn btn-outline-secondary btn-sm me-2">
                        История платежей
                    </a>
                    <a href="{{ route('subscriptions.history') }}" class="btn btn-outline-secondary btn-sm">
                        История подписок
                    </a>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Статус верификации</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                @if($user->email_verified)
                                    <svg width="24" height="24" fill="green" class="me-2" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                    </svg>
                                    <span>Email подтвержден</span>
                                @else
                                    <svg width="24" height="24" fill="orange" class="me-2" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                                    </svg>
                                    <span>Email не подтвержден</span>
                                @endif
                            </div>
                            @if(!$user->email_verified)
                                <form action="{{ route('verification.email.resend') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-envelope-check"></i> Верифицировать
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            @if($user->phone_verified)
                                <svg width="24" height="24" fill="green" class="me-2" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                </svg>
                                <span>Телефон подтвержден</span>
                            @else
                                <svg width="24" height="24" fill="orange" class="me-2" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                                </svg>
                                <span>Телефон не подтвержден</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('home') }}" class="btn btn-secondary">Назад к списку закупок</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Принудительно открываем модалку при клике на кнопки пополнения баланса
document.addEventListener('DOMContentLoaded', function() {
    const balanceButtons = document.querySelectorAll('[data-bs-target="#balanceModal"]');
    balanceButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const modal = new bootstrap.Modal(document.getElementById('balanceModal'));
            modal.show();
        });
    });

    // Обработка выбора файла аватара
    const avatarInput = document.getElementById('avatar-input');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Проверка размера (макс 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Размер файла не должен превышать 2MB');
                    return;
                }

                // Превью изображения
                const reader = new FileReader();
                reader.onload = function(event) {
                    const placeholder = document.getElementById('avatar-placeholder');
                    const preview = document.getElementById('avatar-preview');

                    if (placeholder) {
                        // Создаем img элемент
                        const img = document.createElement('img');
                        img.src = event.target.result;
                        img.alt = 'Фото профиля';
                        img.className = 'rounded-circle img-thumbnail';
                        img.style.width = '150px';
                        img.style.height = '150px';
                        img.style.objectFit = 'cover';
                        img.id = 'avatar-preview';

                        placeholder.replaceWith(img);
                    } else if (preview) {
                        preview.src = event.target.result;
                    }
                };
                reader.readAsDataURL(file);

                // Автоматическая отправка формы
                document.getElementById('avatar-form').submit();
            }
        });
    }
});

</script>
@endpush
</x-app-layout>
