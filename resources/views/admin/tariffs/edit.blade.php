<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>Редактирование тарифа</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.tariffs.update', $tariff) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Название тарифа</label>
                <input type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       id="name"
                       name="name"
                       value="{{ old('name', $tariff->name) }}"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="duration_days" class="form-label">Длительность (дней)</label>
                <input type="number"
                       class="form-control @error('duration_days') is-invalid @enderror"
                       id="duration_days"
                       name="duration_days"
                       value="{{ old('duration_days', $tariff->duration_days) }}"
                       min="1"
                       required>
                @error('duration_days')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    <strong>Внимание:</strong> Изменение длительности будет записано в историю
                </div>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Цена (руб)</label>
                <input type="number"
                       class="form-control @error('price') is-invalid @enderror"
                       id="price"
                       name="price"
                       value="{{ old('price', $tariff->price) }}"
                       step="0.01"
                       min="0"
                       required>
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    <strong>Внимание:</strong> Изменение цены будет записано в историю
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox"
                       class="form-check-input"
                       id="is_active"
                       name="is_active"
                       {{ old('is_active', $tariff->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    Тариф активен
                </label>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="{{ route('admin.tariffs.index') }}" class="btn btn-secondary">Отмена</a>
                <a href="{{ route('admin.tariffs.show', $tariff) }}" class="btn btn-info">
                    История изменений
                </a>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
