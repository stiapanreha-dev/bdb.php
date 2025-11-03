<x-app-layout>
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">Добавить объявление</h2>

            <div class="card">
                <div class="card-body">
                    <form id="announcementForm" method="POST" action="{{ route('announcements.store') }}">
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
                            <div id="editorjs"></div>
                            <textarea class="d-none @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Дополнительные изображения (до 5 штук) -->
                        <div class="mb-3">
                            <label for="announcement_images" class="form-label">Дополнительные изображения</label>
                            <input type="file" class="form-control @error('images') is-invalid @enderror" id="announcement_images" accept="image/*" multiple>
                            <input type="hidden" id="images_urls" name="images" value="">
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Максимум 5 изображений, до 5 МБ каждое</small>

                            <!-- Превью загруженных изображений -->
                            <div id="images_preview" class="mt-2 row g-2"></div>
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
                            <button type="button" id="submitBtn" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Создать объявление
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
// Функция для отправки логов на сервер
function sendLog(level, message, context = {}) {
    console.log(`[${level.toUpperCase()}] ${message}`, context);
    fetch('{{ route('api.log') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ level, message, context })
    }).catch(err => console.error('Failed to send log:', err));
}

document.addEventListener('DOMContentLoaded', function() {
    sendLog('info', 'DOMContentLoaded event fired');

    // Проверяем что Editor.js загрузился
    if (typeof EditorJS === 'undefined') {
        sendLog('error', 'Editor.js не загрузился с CDN. Используется fallback.');
        // Показываем обычное textarea вместо Editor.js
        document.getElementById('editorjs').style.display = 'none';
        const textarea = document.getElementById('description');
        textarea.classList.remove('d-none');
        textarea.rows = 10;
        return;
    }

    sendLog('info', 'EditorJS class available');

    let editor;

    try {
        sendLog('info', 'Starting EditorJS initialization');
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

        sendLog('info', 'Editor.js успешно инициализирован');

    } catch (error) {
        sendLog('error', 'Ошибка инициализации Editor.js', { error: error.message, stack: error.stack });
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

    imageInput.addEventListener('change', async function(e) {
        const files = Array.from(e.target.files);

        sendLog('info', '[IMAGES] Files selected', { count: files.length });

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
            sendLog('info', '[IMAGES] Uploading images to server...');

            const response = await fetch('{{ route('announcement.images.upload') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const result = await response.json();
            sendLog('info', '[IMAGES] Upload response', result);

            if (result.success) {
                uploadedImages = result.images;
                imagesUrlsInput.value = JSON.stringify(uploadedImages.map(img => img.url));

                sendLog('info', '[IMAGES] Images uploaded successfully', {
                    count: uploadedImages.length,
                    urls: uploadedImages.map(img => img.url)
                });

                // Отображаем превью
                renderImagePreviews();
            } else {
                sendLog('error', '[IMAGES] Upload failed', { message: result.message });
                alert('Ошибка загрузки: ' + result.message);
                imageInput.value = '';
            }
        } catch (error) {
            sendLog('error', '[IMAGES] Upload error', { error: error.message });
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
        sendLog('info', '[IMAGES] Removing image', { index });
        uploadedImages.splice(index, 1);
        imagesUrlsInput.value = JSON.stringify(uploadedImages.map(img => img.url));
        renderImagePreviews();

        if (uploadedImages.length === 0) {
            imageInput.value = '';
        }
    }

    // Сохранение данных перед отправкой формы
    const form = document.getElementById('announcementForm');
    const submitBtn = document.getElementById('submitBtn');

    sendLog('info', 'Form and button elements', {
        formFound: !!form,
        buttonFound: !!submitBtn,
        initialAction: form ? form.action : 'N/A',
        initialMethod: form ? form.method : 'N/A'
    });

    if (!form || !submitBtn) {
        sendLog('error', 'Форма или кнопка не найдены!');
        return;
    }

    sendLog('info', 'Adding click event listener to submit button');

    submitBtn.addEventListener('click', async function(e) {
        sendLog('info', '=== Submit button clicked ===');
        e.preventDefault();

        // Если editor не определен (fallback режим), проверяем textarea напрямую
        if (!editor) {
            sendLog('info', 'Fallback mode - using textarea');
            const textareaValue = document.getElementById('description').value.trim();
            sendLog('info', 'Textarea value', { length: textareaValue.length });
            if (!textareaValue) {
                sendLog('warning', 'Textarea is empty');
                alert('Пожалуйста, введите описание объявления');
                return false;
            }
            sendLog('info', 'Submitting form (fallback mode)');
            form.submit();
            return;
        }

        try {
            sendLog('info', 'Saving editor data...');
            const outputData = await editor.save();
            const jsonData = JSON.stringify(outputData);

            sendLog('info', 'Editor data saved', {
                blocksCount: outputData.blocks ? outputData.blocks.length : 0,
                jsonLength: jsonData.length
            });

            document.getElementById('description').value = jsonData;

            if (!outputData.blocks || outputData.blocks.length === 0) {
                sendLog('warning', 'No blocks in editor data');
                alert('Пожалуйста, введите описание объявления');
                return false;
            }

            // Проверяем что данные действительно сохранились
            const savedValue = document.getElementById('description').value;
            sendLog('info', 'Textarea value after save', {
                length: savedValue.length,
                preview: savedValue.substring(0, 100)
            });

            if (!savedValue || savedValue === '{}' || savedValue === '{"blocks":[]}') {
                sendLog('warning', 'Saved value is empty or invalid');
                alert('Описание пустое. Пожалуйста, добавьте текст.');
                return false;
            }

            sendLog('info', 'Submitting form...');

            // Проверяем CSRF токен перед отправкой
            const csrfToken = document.querySelector('input[name="_token"]');
            if (csrfToken) {
                sendLog('info', 'CSRF token present', { tokenLength: csrfToken.value.length });
            } else {
                sendLog('error', 'CSRF token missing!');
            }

            // Логируем параметры формы
            sendLog('info', 'Form details', {
                method: form.method,
                action: form.action,
                descriptionLength: document.getElementById('description').value.length
            });

            // Отправляем форму
            form.submit();

            sendLog('info', 'Form.submit() called successfully');
        } catch (error) {
            sendLog('error', 'Ошибка при сохранении данных', {
                error: error.message,
                stack: error.stack
            });
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
            document.getElementById('register_as_purchase').checked = false;
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
