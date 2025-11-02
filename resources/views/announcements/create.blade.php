<x-app-layout>
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">Добавить объявление</h2>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('announcements.store') }}">
                        @csrf

                        <!-- Тип объявления -->
                        <div class="mb-3">
                            <label class="form-label">Тип объявления <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_supplier" value="supplier" {{ old('type') === 'supplier' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="type_supplier">
                                        Я поставщик
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_buyer" value="buyer" {{ old('type') === 'buyer' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="type_buyer">
                                        Я покупатель
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_dealer" value="dealer" {{ old('type') === 'dealer' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="type_dealer">
                                        Ищу дилера
                                    </label>
                                </div>
                            </div>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Категория -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Категория</label>
                            <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category') }}" placeholder="Например: Строительство, IT, Продукты">
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Необязательное поле</small>
                        </div>

                        <!-- Заголовок -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Заголовок <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required placeholder="Краткое описание вашего объявления">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Описание -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required placeholder="Подробное описание вашего предложения или запроса">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Компания (для будущего использования) -->
                        <div class="mb-3">
                            <label class="form-label">Компания</label>
                            <div class="form-control-plaintext">
                                {{ $user->name }}
                            </div>
                            <small class="text-muted">Выбрана ваша компания по умолчанию</small>
                        </div>

                        <!-- Чекбокс "Зарегистрировать в закупках" (показывается только для типа "Я покупатель") -->
                        <div class="mb-3" id="register_purchase_container" style="display: none;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="register_as_purchase" id="register_as_purchase" value="1" {{ old('register_as_purchase') ? 'checked' : '' }}>
                                <label class="form-check-label" for="register_as_purchase">
                                    Зарегистрировать в закупках
                                </label>
                            </div>
                            <small class="text-muted">Объявление будет добавлено в базу закупок с типом purchase_type = 10</small>
                        </div>

                        <!-- Контакты для связи -->
                        <div class="alert alert-info">
                            <h6>Контакты для связи:</h6>
                            <p class="mb-1"><strong>Email:</strong> {{ $user->work_email ?? $user->email }}</p>
                            <p class="mb-0"><strong>Телефон:</strong> {{ $user->work_phone ?? $user->phone ?? 'Не указан' }}</p>
                            <small>
                                Вы можете изменить рабочие контакты в <a href="{{ route('profile.edit') }}" target="_blank">профиле</a>
                            </small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('announcements.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Назад
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Создать объявление
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Показываем чекбокс "Зарегистрировать в закупках" только для типа "Я покупатель"
document.addEventListener('DOMContentLoaded', function() {
    const typeInputs = document.querySelectorAll('input[name="type"]');
    const registerPurchaseContainer = document.getElementById('register_purchase_container');

    function toggleRegisterPurchase() {
        const selectedType = document.querySelector('input[name="type"]:checked');
        if (selectedType && selectedType.value === 'buyer') {
            registerPurchaseContainer.style.display = 'block';
        } else {
            registerPurchaseContainer.style.display = 'none';
            document.getElementById('register_as_purchase').checked = false;
        }
    }

    typeInputs.forEach(input => {
        input.addEventListener('change', toggleRegisterPurchase);
    });

    // Вызываем при загрузке для установки начального состояния
    toggleRegisterPurchase();
});
</script>
@endpush
</x-app-layout>
