<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.shop.products.index') }}">Товары</a></li>
                <li class="breadcrumb-item active">Создание</li>
            </ol>
        </nav>
        <h2>Добавление товара</h2>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Ошибки валидации:</strong>
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form id="productForm" method="POST" action="{{ route('admin.shop.products.store') }}" enctype="multipart/form-data">
    @csrf

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
                               value="{{ old('name') }}"
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
                               value="{{ old('slug') }}"
                               required>
                        <div class="form-text">URL товара: /shop/<strong id="slug-preview">...</strong></div>
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
                                  maxlength="500">{{ old('short_description') }}</textarea>
                        <div class="form-text">Максимум 500 символов. Отображается в списке товаров.</div>
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Полное описание</label>
                        <div id="editorjs"></div>
                        <textarea class="d-none" id="description" name="description">{{ old('description') }}</textarea>
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
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @foreach($category->children as $child)
                                        <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>
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
                                   value="{{ old('price') }}"
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
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Товар активен
                        </label>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Изображение</div>
                <div class="card-body">
                    <div class="mb-3">
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
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-paperclip me-1"></i>Прикреплённые файлы
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="file"
                               class="form-control @error('files') is-invalid @enderror @error('files.*') is-invalid @enderror"
                               id="files"
                               name="files[]"
                               multiple>
                        <div class="form-text">Файлы, доступные покупателю после оплаты. Максимум 100 МБ на файл. Можно выбрать несколько.</div>
                        @error('files')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('files.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <button type="button" id="submitBtn" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-check-lg me-1"></i>Создать товар
                    </button>
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
    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const slugPreview = document.getElementById('slug-preview');

    const translitMap = {
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo',
        'ж': 'zh', 'з': 'z', 'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm',
        'н': 'n', 'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
        'ф': 'f', 'х': 'h', 'ц': 'ts', 'ч': 'ch', 'ш': 'sh', 'щ': 'sch',
        'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya'
    };

    nameInput.addEventListener('input', function() {
        let slug = this.value.toLowerCase();
        let translitSlug = '';
        for (let char of slug) {
            translitSlug += translitMap[char] || char;
        }
        translitSlug = translitSlug
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
        slugInput.value = translitSlug;
        slugPreview.textContent = translitSlug || '...';
    });

    slugInput.addEventListener('input', function() {
        slugPreview.textContent = this.value || '...';
    });

    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');

    imageInput.addEventListener('change', function() {
        imagePreview.innerHTML = '';
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 100%;">';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

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
        // Fallback to textarea
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
