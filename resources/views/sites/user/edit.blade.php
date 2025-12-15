<x-app-layout>
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sites.index') }}">Каталог сайтов</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sites.my') }}">Мои сайты</a></li>
                    <li class="breadcrumb-item active">Редактирование</li>
                </ol>
            </nav>
            <h2 class="mb-4">Редактировать сайт</h2>

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
                    <form id="siteForm" method="POST" action="{{ route('sites.update', $site->id) }}">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Название сайта -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Название сайта <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $site->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Категория -->
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Категория <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category_id') is-invalid @enderror"
                                            id="category_id" name="category_id" required>
                                        <option value="">-- Выберите категорию --</option>
                                        @foreach($categories->sortBy('name') as $category)
                                            @if($category->children->count() > 0)
                                                <optgroup label="{{ $category->name }}">
                                                    @foreach($category->children->sortBy('name') as $child)
                                                        <option value="{{ $child->id }}" {{ old('category_id', $site->category_id) == $child->id ? 'selected' : '' }}>
                                                            {{ $child->name }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @else
                                                <option value="{{ $category->id }}" {{ old('category_id', $site->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- URL сайта -->
                                <div class="mb-3">
                                    <label for="url" class="form-label">URL сайта <span class="text-danger">*</span></label>
                                    <input type="url" class="form-control @error('url') is-invalid @enderror"
                                           id="url" name="url" value="{{ old('url', $site->url) }}" required>
                                    @error('url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email для связи -->
                                <div class="mb-3">
                                    <label for="contact_email" class="form-label">Email для связи <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('contact_email') is-invalid @enderror"
                                           id="contact_email" name="contact_email"
                                           value="{{ old('contact_email', $site->contact_email) }}" required>
                                    @error('contact_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Описание -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Описание сайта</label>
                                    <div id="editorjs"></div>
                                    <textarea class="d-none @error('description') is-invalid @enderror"
                                              id="description" name="description">{{ old('description', $site->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Дополнительные изображения -->
                                <div class="mb-3">
                                    <label for="site_images" class="form-label">Дополнительные изображения</label>
                                    <input type="file" class="form-control" id="site_images" accept="image/*" multiple>
                                    <input type="hidden" id="images_urls" name="images" value="{{ old('images', json_encode($site->images ?? [])) }}">
                                    <small class="text-muted">Максимум 10 изображений, до 5 МБ каждое</small>
                                    <div id="images_preview" class="mt-2 row g-2"></div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Логотип -->
                                <div class="mb-3">
                                    <label for="logo" class="form-label">Логотип сайта</label>

                                    @if($site->logo)
                                    <div class="mb-2" id="current-logo">
                                        <img src="{{ asset('storage/' . $site->logo) }}"
                                             alt="{{ $site->name }}"
                                             class="rounded-circle"
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                        <div class="form-check mt-2">
                                            <input type="checkbox" class="form-check-input" id="delete_logo" name="delete_logo">
                                            <label class="form-check-label text-danger" for="delete_logo">
                                                Удалить логотип
                                            </label>
                                        </div>
                                    </div>
                                    @endif

                                    <input type="file" class="form-control" id="logo" accept="image/*">
                                    <input type="hidden" id="logo_path" name="logo_path" value="">
                                    <div class="form-text">Квадратное изображение, до 2 МБ</div>
                                    <div id="logo_preview" class="mt-2"></div>
                                </div>

                                <!-- Status -->
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Статус</h6>
                                        @if($site->isPending())
                                            <span class="badge bg-warning">На модерации</span>
                                        @elseif($site->isApproved())
                                            <span class="badge bg-success">Одобрен</span>
                                        @else
                                            <span class="badge bg-danger">Отклонен</span>
                                        @endif

                                        @if($site->moderation_comment)
                                            <p class="small mt-2 mb-0"><strong>Комментарий:</strong> {{ $site->moderation_comment }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Stats -->
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Статистика</h6>
                                        <ul class="list-unstyled mb-0 small">
                                            <li><i class="bi bi-eye me-1"></i>Просмотров: {{ $site->views_count }}</li>
                                            <li><i class="bi bi-calendar me-1"></i>Создан: {{ $site->created_at->format('d.m.Y') }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('sites.my') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Назад
                            </a>
                            <button type="button" id="submitBtn" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Сохранить
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    #editorjs {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        min-height: 200px;
    }
    .ce-block__content,
    .ce-toolbar__content {
        max-width: 100%;
    }
    .codex-editor__redactor {
        padding-bottom: 100px !important;
    }
    .image-preview-item {
        position: relative;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.5rem;
    }
    .image-preview-item img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 0.25rem;
    }
    .image-preview-item .remove-btn {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        font-size: 14px;
        line-height: 1;
        cursor: pointer;
        padding: 0;
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    let editor = null;
    let uploadedImages = {!! json_encode($site->images ?? []) !!} || [];

    // Parse existing description for Editor.js
    let existingData = null;
    const descriptionValue = document.getElementById('description').value;
    if (descriptionValue) {
        try {
            existingData = JSON.parse(descriptionValue);
        } catch (e) {
            console.error('Failed to parse description:', e);
        }
    }

    // Initialize Editor.js
    if (typeof EditorJS !== 'undefined') {
        editor = new EditorJS({
            holder: 'editorjs',
            placeholder: 'Опишите ваш сайт...',
            data: existingData,
            tools: {
                header: {
                    class: Header,
                    config: {
                        levels: [2, 3, 4],
                        defaultLevel: 3
                    }
                },
                list: {
                    class: EditorjsList,
                    inlineToolbar: true
                },
                checklist: {
                    class: Checklist,
                    inlineToolbar: true
                },
                quote: Quote,
                delimiter: Delimiter,
                image: {
                    class: ImageTool,
                    config: {
                        endpoints: {
                            byFile: '{{ route("site.editor.image.upload") }}'
                        },
                        additionalRequestHeaders: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }
                }
            }
        });
    } else {
        document.getElementById('editorjs').style.display = 'none';
        const textarea = document.getElementById('description');
        textarea.classList.remove('d-none');
        textarea.rows = 10;
    }

    // Additional images - declare early for renderImagesPreview
    const imagesInput = document.getElementById('site_images');
    const imagesPreview = document.getElementById('images_preview');
    const imagesUrls = document.getElementById('images_urls');

    function renderImagesPreview() {
        imagesPreview.innerHTML = '';
        uploadedImages.forEach((img, index) => {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-3';
            const imgUrl = img.url || '/storage/' + img.path;
            col.innerHTML = `
                <div class="image-preview-item">
                    <img src="${imgUrl}" alt="Preview">
                    <button type="button" class="remove-btn" data-index="${index}">&times;</button>
                </div>
            `;
            imagesPreview.appendChild(col);
        });

        imagesPreview.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                uploadedImages.splice(index, 1);
                imagesUrls.value = JSON.stringify(uploadedImages);
                renderImagesPreview();
            });
        });
    }

    // Render existing images
    renderImagesPreview();

    // Logo upload
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logo_preview');
    const logoPath = document.getElementById('logo_path');
    const currentLogo = document.getElementById('current-logo');

    logoInput.addEventListener('change', async function() {
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
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await response.json();
            if (data.success) {
                logoPath.value = data.path;
                logoPreview.innerHTML = '<img src="' + data.url + '" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">';
                if (currentLogo) currentLogo.style.display = 'none';
            } else {
                alert('Ошибка загрузки: ' + (data.message || 'Неизвестная ошибка'));
            }
        } catch (error) {
            console.error('Upload error:', error);
            alert('Ошибка загрузки логотипа');
        }
    });

    // Additional images upload handler
    imagesInput.addEventListener('change', async function() {
        if (!this.files || this.files.length === 0) return;

        if (uploadedImages.length + this.files.length > 10) {
            alert('Максимум 10 изображений');
            return;
        }

        const formData = new FormData();
        for (let i = 0; i < this.files.length; i++) {
            if (this.files[i].size > 5 * 1024 * 1024) {
                alert('Файл ' + this.files[i].name + ' слишком большой. Максимум 5 МБ.');
                continue;
            }
            formData.append('images[]', this.files[i]);
        }

        try {
            const response = await fetch('{{ route("site.images.upload") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await response.json();
            if (data.success) {
                uploadedImages = uploadedImages.concat(data.images);
                imagesUrls.value = JSON.stringify(uploadedImages);
                renderImagesPreview();
            } else {
                alert('Ошибка загрузки: ' + (data.message || 'Неизвестная ошибка'));
            }
        } catch (error) {
            console.error('Upload error:', error);
            alert('Ошибка загрузки изображений');
        }

        this.value = '';
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
            console.error('Save error:', error);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Сохранить';
            alert('Ошибка сохранения: ' + error.message);
        }
    });
});
</script>
@endpush
</x-app-layout>
