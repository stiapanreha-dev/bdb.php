<x-app-layout>
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">Редактировать объявление</h2>

            <div class="card">
                <div class="card-body">
                    <form id="announcementEditForm" method="POST" action="{{ route('announcements.update', $announcement->id) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Тип объявления -->
                        <div class="mb-3">
                            <label class="form-label">Тип объявления <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_supplier" value="supplier" {{ old('type', $announcement->type) === 'supplier' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="type_supplier">
                                        Я поставщик
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_buyer" value="buyer" {{ old('type', $announcement->type) === 'buyer' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="type_buyer">
                                        Я покупатель
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_dealer" value="dealer" {{ old('type', $announcement->type) === 'dealer' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="type_dealer">
                                        Ищу дилера
                                    </label>
                                </div>
                            </div>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Заголовок -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Заголовок <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $announcement->title) }}" required placeholder="Краткое описание вашего объявления">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Дата публикации (только для админа) -->
                        @if(Auth::user()->isAdmin())
                        <div class="mb-3">
                            <label for="published_at" class="form-label">Дата публикации</label>
                            <input type="datetime-local"
                                   class="form-control @error('published_at') is-invalid @enderror"
                                   id="published_at"
                                   name="published_at"
                                   value="{{ old('published_at', $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                            @error('published_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Оставьте пустым для использования текущей даты</small>
                        </div>
                        @endif

                        <!-- Описание -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание <span class="text-danger">*</span></label>
                            <div id="editorjs"></div>
                            <textarea class="d-none @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $announcement->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Дополнительные изображения (до 5 штук) -->
                        <div class="mb-3">
                            <label for="announcement_images" class="form-label">Дополнительные изображения</label>
                            <input type="file" class="form-control @error('images') is-invalid @enderror" id="announcement_images" accept="image/*" multiple>
                            <input type="hidden" id="images_urls" name="images" value="{{ old('images', $announcement->images ? json_encode($announcement->images) : '') }}">
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Максимум 5 изображений, до 5 МБ каждое</small>

                            <!-- Превью загруженных изображений -->
                            <div id="images_preview" class="mt-2 row g-2"></div>
                        </div>

                        <!-- Чекбокс "Зарегистрировать в закупках" (показывается только для типа "Я покупатель") -->
                        <div class="mb-3" id="register_purchase_container" style="display: none;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="register_as_purchase" id="register_as_purchase" value="1" {{ old('register_as_purchase', $announcement->register_as_purchase) ? 'checked' : '' }} disabled>
                                <label class="form-check-label" for="register_as_purchase">
                                    Зарегистрировать в закупках
                                </label>
                            </div>
                            <small class="text-muted">Эта опция не может быть изменена после создания объявления</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('announcements.show', $announcement->id) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Отменить
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Сохранить изменения
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
        min-height: 300px;
    }
    .ce-block__content,
    .ce-toolbar__content {
        max-width: 100%;
    }
    .codex-editor__redactor {
        padding-bottom: 150px !important;
    }
    .image-preview-item {
        position: relative;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.5rem;
    }
    .image-preview-item img {
        width: 100%;
        height: 150px;
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
        width: 28px;
        height: 28px;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        padding: 0;
    }
    .image-preview-item .remove-btn:hover {
        background: rgba(220, 53, 69, 1);
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
<script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/table@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Проверяем что Editor.js загрузился
    if (typeof EditorJS === 'undefined') {
        console.error('Editor.js не загрузился с CDN. Используется fallback.');
        // Показываем обычное textarea вместо Editor.js
        document.getElementById('editorjs').style.display = 'none';
        const textarea = document.getElementById('description');
        textarea.classList.remove('d-none');
        textarea.rows = 10;
        return;
    }

    let editor;

    try {
        // Инициализация Editor.js
        editor = new EditorJS({
            holder: 'editorjs',
            placeholder: 'Начните писать описание объявления...',
            tools: {
                header: {
                    class: Header,
                    config: {
                        levels: [1, 2, 3, 4],
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
                            byFile: '{{ route('image.upload.file') }}',
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
                },
                embed: {
                    class: Embed,
                    config: {
                        services: {
                            youtube: true,
                            vimeo: true
                        }
                    }
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

        console.log('Editor.js успешно инициализирован');

    } catch (error) {
        console.error('Ошибка инициализации Editor.js:', error);
        // Показываем обычное textarea при ошибке инициализации
        document.getElementById('editorjs').style.display = 'none';
        const textarea = document.getElementById('description');
        textarea.classList.remove('d-none');
        textarea.rows = 10;
        alert('Редактор не загрузился. Используйте обычное текстовое поле.');
        return;
    }

    // Обработка загрузки изображений
    const imageInput = document.getElementById('announcement_images');
    const imagesPreview = document.getElementById('images_preview');
    const imagesUrlsInput = document.getElementById('images_urls');
    let uploadedImages = [];

    // Загружаем существующие изображения из hidden input
    const existingImagesJson = imagesUrlsInput.value;
    if (existingImagesJson) {
        try {
            const existingUrls = JSON.parse(existingImagesJson);
            if (Array.isArray(existingUrls)) {
                uploadedImages = existingUrls.map(url => ({ url: url }));
                renderImagePreviews();
            }
        } catch (e) {
            console.error('Failed to parse existing images:', e);
        }
    }

    imageInput.addEventListener('change', async function(e) {
        const files = Array.from(e.target.files);

        console.log('[IMAGES] Files selected:', files.length);

        if (files.length > 5) {
            alert('Максимум 5 изображений');
            imageInput.value = '';
            return;
        }

        // Проверка размера каждого файла (макс 5МБ)
        for (let file of files) {
            if (file.size > 5 * 1024 * 1024) {
                alert(`Файл ${file.name} слишком большой. Максимум 5 МБ.`);
                imageInput.value = '';
                return;
            }
        }

        // Загружаем файлы на сервер
        const formData = new FormData();
        files.forEach((file, index) => {
            formData.append(`images[${index}]`, file);
        });

        try {
            console.log('[IMAGES] Uploading images to server...');

            const response = await fetch('{{ route('announcement.images.upload') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const result = await response.json();
            console.log('[IMAGES] Upload response:', result);

            if (result.success) {
                uploadedImages = result.images;
                imagesUrlsInput.value = JSON.stringify(uploadedImages.map(img => img.url));

                console.log('[IMAGES] Images uploaded successfully:', uploadedImages.length);

                // Отображаем превью
                renderImagePreviews();
            } else {
                console.error('[IMAGES] Upload failed:', result.message);
                alert('Ошибка загрузки: ' + result.message);
                imageInput.value = '';
            }
        } catch (error) {
            console.error('[IMAGES] Upload error:', error);
            alert('Ошибка при загрузке изображений');
            imageInput.value = '';
        }
    });

    function renderImagePreviews() {
        imagesPreview.innerHTML = '';

        uploadedImages.forEach((image, index) => {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4';

            col.innerHTML = `
                <div class="image-preview-item">
                    <img src="${image.url}" alt="Preview ${index + 1}">
                    <button type="button" class="remove-btn" data-index="${index}" title="Удалить">&times;</button>
                </div>
            `;

            imagesPreview.appendChild(col);
        });

        // Добавляем обработчики удаления
        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                removeImage(index);
            });
        });
    }

    function removeImage(index) {
        console.log('[IMAGES] Removing image:', index);
        uploadedImages.splice(index, 1);
        imagesUrlsInput.value = JSON.stringify(uploadedImages.map(img => img.url));
        renderImagePreviews();

        if (uploadedImages.length === 0) {
            imageInput.value = '';
        }
    }

    // Сохранение данных перед отправкой формы
    const form = document.getElementById('announcementEditForm');

    if (!form) {
        console.error('Форма announcementEditForm не найдена!');
        return;
    }

    console.log('Form found:', form.action, form.method);

    form.addEventListener('submit', async function(e) {
        console.log('=== Edit form submit ===');
        e.preventDefault();

        // Если editor не определен (fallback режим), проверяем textarea напрямую
        if (!editor) {
            console.log('Fallback mode - using textarea');
            const textareaValue = document.getElementById('description').value.trim();
            if (!textareaValue) {
                alert('Пожалуйста, введите описание объявления');
                return false;
            }
            console.log('Submitting form (fallback)');
            form.submit();
            return;
        }

        try {
            console.log('Saving editor data...');
            const outputData = await editor.save();
            const jsonData = JSON.stringify(outputData);

            console.log('Editor data saved:', outputData.blocks ? outputData.blocks.length : 0, 'blocks');

            document.getElementById('description').value = jsonData;

            if (!outputData.blocks || outputData.blocks.length === 0) {
                alert('Пожалуйста, введите описание объявления');
                return false;
            }

            console.log('Form action:', form.action);
            console.log('Form method:', form.method);
            console.log('Description length:', jsonData.length);
            console.log('Submitting form...');

            form.submit();

            console.log('Form submitted successfully');
        } catch (error) {
            console.error('Ошибка сохранения:', error);
            alert('Произошла ошибка при сохранении. Попробуйте еще раз.');
        }
    });

    // Показываем чекбокс "Зарегистрировать в закупках" только для типа "Я покупатель"
    const typeInputs = document.querySelectorAll('input[name="type"]');
    const registerPurchaseContainer = document.getElementById('register_purchase_container');

    function toggleRegisterPurchase() {
        const selectedType = document.querySelector('input[name="type"]:checked');
        if (selectedType && selectedType.value === 'buyer') {
            registerPurchaseContainer.style.display = 'block';
        } else {
            registerPurchaseContainer.style.display = 'none';
        }
    }

    typeInputs.forEach(input => {
        input.addEventListener('change', toggleRegisterPurchase);
    });

    toggleRegisterPurchase();
});
</script>
@endpush
</x-app-layout>
