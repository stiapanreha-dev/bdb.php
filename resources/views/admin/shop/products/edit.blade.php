<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.shop.products.index') }}">Товары</a></li>
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>
        <h2>Редактирование товара</h2>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($product->trashed())
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Этот товар удалён. Вы можете редактировать его и восстановить.
</div>
@endif

<form id="productForm" method="POST" action="{{ route('admin.shop.products.update', $product->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Основная информация</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Название товара <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', $product->name) }}"
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
                               value="{{ old('slug', $product->slug) }}"
                               required>
                        <div class="form-text">URL товара: /shop/<strong id="slug-preview">{{ $product->slug }}</strong></div>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="short_description" class="form-label">Краткое описание</label>
                        <textarea class="form-control @error('short_description') is-invalid @enderror"
                                  id="short_description"
                                  name="short_description"
                                  rows="2"
                                  maxlength="500">{{ old('short_description', $product->short_description) }}</textarea>
                        <div class="form-text">Максимум 500 символов. Отображается в списке товаров.</div>
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Полное описание</label>
                        <div id="editorjs"></div>
                        <textarea class="d-none" id="description" name="description">{{ old('description', is_array($product->description) ? json_encode($product->description) : $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">Категория и цена</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Категория <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror"
                                id="category_id"
                                name="category_id"
                                required>
                            <option value="">Выберите категорию</option>
                            @foreach($categories as $category)
                                <optgroup label="{{ $category->name }}">
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @foreach($category->children as $child)
                                        <option value="{{ $child->id }}" {{ old('category_id', $product->category_id) == $child->id ? 'selected' : '' }}>
                                            -- {{ $child->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Цена (руб) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number"
                                   class="form-control @error('price') is-invalid @enderror"
                                   id="price"
                                   name="price"
                                   value="{{ old('price', $product->price) }}"
                                   step="0.01"
                                   min="0"
                                   required>
                            <span class="input-group-text">₽</span>
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox"
                               class="form-check-input"
                               id="is_active"
                               name="is_active"
                               {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Товар активен
                        </label>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Изображение</div>
                <div class="card-body">
                    @if($product->image)
                    <div class="mb-2" id="current-image">
                        <img src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->name }}"
                             class="img-thumbnail"
                             style="max-width: 100%;">
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
            </div>

            <div class="card mb-3 bg-light">
                <div class="card-header">Статистика</div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li><i class="bi bi-eye me-2"></i>Просмотров: <strong>{{ $product->views_count }}</strong></li>
                        <li><i class="bi bi-cart-check me-2"></i>Покупок: <strong>{{ $product->purchases_count }}</strong></li>
                        @if($product->creator)
                        <li><i class="bi bi-person me-2"></i>Создал: {{ $product->creator->name }}</li>
                        @endif
                        <li><i class="bi bi-calendar me-2"></i>Создан: {{ $product->created_at->format('d.m.Y H:i') }}</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <button type="button" id="submitBtn" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-check-lg me-1"></i>Сохранить
                    </button>
                    @if($product->trashed())
                    <form method="POST" action="{{ route('admin.shop.products.restore', $product->id) }}" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Восстановить товар
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('admin.shop.products.index') }}" class="btn btn-secondary w-100">Отмена</a>
                </div>
            </div>
        </div>
    </div>
</form>

@push('styles')
<style>
    #editorjs {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        min-height: 300px;
        background: #fff;
    }
    .ce-block__content,
    .ce-toolbar__content {
        max-width: 100%;
    }
    .codex-editor__redactor {
        padding-bottom: 100px !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/image@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/checklist@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/table@latest"></script>
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
                imagePreview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 100%;"><div class="text-muted small mt-1">Новое изображение</div>';
            };
            reader.readAsDataURL(this.files[0]);
            if (currentImage) currentImage.style.display = 'none';
        } else {
            if (currentImage) currentImage.style.display = 'block';
        }
    });

    if (deleteCheckbox) {
        deleteCheckbox.addEventListener('change', function() {
            if (currentImage) {
                currentImage.querySelector('img').style.opacity = this.checked ? '0.3' : '1';
            }
        });
    }

    // Initialize Editor.js
    let editor;

    if (typeof EditorJS !== 'undefined') {
        editor = new EditorJS({
            holder: 'editorjs',
            placeholder: 'Введите описание товара...',
            tools: {
                header: {
                    class: Header,
                    config: {
                        levels: [2, 3, 4],
                        defaultLevel: 2
                    }
                },
                list: {
                    class: EditorjsList,
                    inlineToolbar: true
                },
                image: {
                    class: ImageTool,
                    config: {
                        endpoints: {
                            byFile: '{{ route('shop.image.upload') }}',
                            byUrl: '{{ route('image.upload.url') }}'
                        },
                        additionalRequestHeaders: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    }
                },
                quote: {
                    class: Quote,
                    inlineToolbar: true
                },
                delimiter: Delimiter,
                table: {
                    class: Table,
                    inlineToolbar: true
                },
                checklist: {
                    class: Checklist,
                    inlineToolbar: true
                }
            },
            data: (() => {
                const oldContent = document.getElementById('description').value;
                if (oldContent) {
                    try {
                        return JSON.parse(oldContent);
                    } catch (e) {
                        return {};
                    }
                }
                return {};
            })()
        });
    } else {
        document.getElementById('editorjs').style.display = 'none';
        const textarea = document.getElementById('description');
        textarea.classList.remove('d-none');
        textarea.rows = 10;
    }

    // Form submission
    const form = document.getElementById('productForm');
    const submitBtn = document.getElementById('submitBtn');

    submitBtn.addEventListener('click', async function(e) {
        e.preventDefault();

        if (!editor) {
            form.submit();
            return;
        }

        try {
            const outputData = await editor.save();
            document.getElementById('description').value = JSON.stringify(outputData);
            form.submit();
        } catch (error) {
            console.error('Error saving editor data:', error);
            alert('Ошибка сохранения. Попробуйте ещё раз.');
        }
    });
});
</script>
@endpush
</x-app-layout>
