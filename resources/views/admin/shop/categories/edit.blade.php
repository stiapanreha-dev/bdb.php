<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.shop.categories.index') }}">Категории</a></li>
                <li class="breadcrumb-item active">{{ $category->name }}</li>
            </ol>
        </nav>
        <h2>Редактирование категории</h2>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.shop.categories.update', $category) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Название категории <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', $category->name) }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">URL (slug) <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('slug') is-invalid @enderror"
                               id="slug"
                               name="slug"
                               value="{{ old('slug', $category->slug) }}"
                               required>
                        <div class="form-text">Будет использован в URL: /shop/category/<strong id="slug-preview">{{ $category->slug }}</strong></div>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Родительская категория</label>
                        <select class="form-select @error('parent_id') is-invalid @enderror"
                                id="parent_id"
                                name="parent_id">
                            <option value="">-- Корневая категория --</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Описание</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="3">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="image" class="form-label">Изображение</label>

                        @if($category->image)
                        <div class="mb-2" id="current-image">
                            <img src="{{ asset('storage/' . $category->image) }}"
                                 alt="{{ $category->name }}"
                                 class="img-thumbnail"
                                 style="max-width: 200px;">
                            <div class="form-check mt-2">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="delete_image"
                                       name="delete_image">
                                <label class="form-check-label text-danger" for="delete_image">
                                    Удалить изображение
                                </label>
                            </div>
                        </div>
                        @endif

                        <input type="file"
                               class="form-control @error('image') is-invalid @enderror"
                               id="image"
                               name="image"
                               accept="image/*">
                        <div class="form-text">JPEG, PNG, GIF, WebP. Максимум 5 МБ</div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="image-preview" class="mt-2"></div>
                    </div>

                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Порядок сортировки</label>
                        <input type="number"
                               class="form-control @error('sort_order') is-invalid @enderror"
                               id="sort_order"
                               name="sort_order"
                               value="{{ old('sort_order', $category->sort_order) }}"
                               min="0">
                        <div class="form-text">Меньшее значение - выше в списке</div>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox"
                               class="form-check-input"
                               id="is_active"
                               name="is_active"
                               {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Категория активна
                        </label>
                    </div>

                    {{-- Statistics --}}
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Статистика</h6>
                            <ul class="list-unstyled mb-0">
                                <li><i class="bi bi-box me-2"></i>Товаров: <strong>{{ $category->products->count() }}</strong></li>
                                @if($category->children->count() > 0)
                                <li><i class="bi bi-folder me-2"></i>Подкатегорий: <strong>{{ $category->children->count() }}</strong></li>
                                @endif
                                <li><i class="bi bi-calendar me-2"></i>Создана: {{ $category->created_at->format('d.m.Y H:i') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Сохранить
                </button>
                <a href="{{ route('admin.shop.categories.index') }}" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Slug preview
    const slugInput = document.getElementById('slug');
    const slugPreview = document.getElementById('slug-preview');

    slugInput.addEventListener('input', function() {
        slugPreview.textContent = this.value || '...';
    });

    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const currentImage = document.getElementById('current-image');
    const deleteCheckbox = document.getElementById('delete_image');

    imageInput.addEventListener('change', function() {
        imagePreview.innerHTML = '';
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;"><div class="text-muted small mt-1">Новое изображение</div>';
            };
            reader.readAsDataURL(this.files[0]);

            // Hide current image if new one is selected
            if (currentImage) {
                currentImage.style.display = 'none';
            }
        } else {
            if (currentImage) {
                currentImage.style.display = 'block';
            }
        }
    });

    // Handle delete checkbox
    if (deleteCheckbox) {
        deleteCheckbox.addEventListener('change', function() {
            if (currentImage) {
                currentImage.querySelector('img').style.opacity = this.checked ? '0.3' : '1';
            }
        });
    }
});
</script>
</x-app-layout>
