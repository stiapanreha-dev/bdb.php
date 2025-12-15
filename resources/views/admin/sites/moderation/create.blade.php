<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.sites.moderation.index') }}">Модерация сайтов</a></li>
                <li class="breadcrumb-item active">Добавление сайта</li>
            </ol>
        </nav>
        <h2>Добавить сайт (админ)</h2>
        <p class="text-muted">Сайт будет добавлен сразу со статусом "Одобрен"</p>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <strong>Ошибки валидации:</strong>
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
    <div class="card-body">
        <form id="siteForm" method="POST" action="{{ route('admin.sites.moderation.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Название сайта <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Категория <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror"
                                id="category_id" name="category_id" required>
                            <option value="">-- Выберите категорию --</option>
                            @foreach($categories as $category)
                                <optgroup label="{{ $category->name }}">
                                    @if($category->children->count() > 0)
                                        @foreach($category->children->sortBy('name') as $child)
                                            <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>
                                                {{ $child->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endif
                                </optgroup>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="url" class="form-label">URL сайта <span class="text-danger">*</span></label>
                        <input type="url" class="form-control @error('url') is-invalid @enderror"
                               id="url" name="url" value="{{ old('url') }}" required
                               placeholder="https://example.com">
                        @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="contact_email" class="form-label">Email для связи <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('contact_email') is-invalid @enderror"
                               id="contact_email" name="contact_email"
                               value="{{ old('contact_email', Auth::user()->email) }}" required>
                        @error('contact_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Описание сайта</label>
                        <div id="editorjs"></div>
                        <textarea class="d-none" id="description" name="description">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="logo" class="form-label">Логотип сайта</label>
                        <input type="file" class="form-control" id="logo" accept="image/*">
                        <input type="hidden" id="logo_path" name="logo_path" value="">
                        <div class="form-text">Квадратное изображение, до 2 МБ</div>
                        <div id="logo_preview" class="mt-2"></div>
                    </div>

                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Сайт будет сразу опубликован без модерации
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.sites.moderation.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Назад
                </a>
                <button type="button" id="submitBtn" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i>Добавить сайт
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    #editorjs {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        min-height: 150px;
    }
    .ce-block__content, .ce-toolbar__content { max-width: 100%; }
    #logo_preview img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let editor = null;

    if (typeof EditorJS !== 'undefined') {
        editor = new EditorJS({
            holder: 'editorjs',
            placeholder: 'Описание сайта...',
            tools: {
                header: { class: Header, config: { levels: [2, 3, 4], defaultLevel: 3 } },
                list: { class: EditorjsList, inlineToolbar: true },
                quote: Quote
            }
        });
    }

    // Logo upload
    document.getElementById('logo').addEventListener('change', async function() {
        if (!this.files || !this.files[0]) return;
        const file = this.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('Файл слишком большой. Максимум 2 МБ.');
            return;
        }

        const formData = new FormData();
        formData.append('logo', file);

        try {
            const response = await fetch('{{ route("site.logo.upload") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                document.getElementById('logo_path').value = data.path;
                document.getElementById('logo_preview').innerHTML = '<img src="' + data.url + '">';
            } else {
                alert('Ошибка загрузки: ' + (data.message || 'Неизвестная ошибка'));
            }
        } catch (error) {
            alert('Ошибка загрузки логотипа');
        }
    });

    // Form submit
    document.getElementById('submitBtn').addEventListener('click', async function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Сохранение...';

        try {
            if (editor) {
                await editor.isReady;
                const outputData = await editor.save();
                document.getElementById('description').value = JSON.stringify(outputData);
            }
            document.getElementById('siteForm').submit();
        } catch (error) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Добавить сайт';
            alert('Ошибка: ' + error.message);
        }
    });
});
</script>
@endpush
</x-app-layout>
