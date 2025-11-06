<x-app-layout>
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">Создать статью</h2>

            <div class="card">
                <div class="card-body">
                    <form id="articleForm" method="POST" action="{{ route('articles.store') }}">
                        @csrf

                        <!-- Заголовок -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Заголовок <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required placeholder="Введите заголовок статьи">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Содержание -->
                        <div class="mb-3">
                            <label for="content" class="form-label">Содержание <span class="text-danger">*</span></label>
                            <div id="editorjs"></div>
                            <textarea class="d-none @error('content') is-invalid @enderror" id="content" name="content">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Изображения (до 5 штук) -->
                        <div class="mb-3">
                            <label for="article_images" class="form-label">Изображения</label>
                            <input type="file" class="form-control @error('images') is-invalid @enderror" id="article_images" accept="image/*" multiple>
                            <input type="hidden" id="images_urls" name="images" value="">
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Максимум 5 изображений, до 5 МБ каждое. Первое изображение будет использовано как главное.</small>

                            <!-- Превью загруженных изображений -->
                            <div id="images_preview" class="mt-2 row g-2"></div>
                        </div>

                        <!-- Дата публикации -->
                        <div class="mb-3">
                            <label for="published_at" class="form-label">Дата публикации</label>
                            <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" id="published_at" name="published_at" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                            @error('published_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Оставьте текущую дату и время или выберите другую</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('articles.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Назад
                            </a>
                            <button type="button" id="submitBtn" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Создать статью
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
        document.getElementById('editorjs').style.display = 'none';
        const textarea = document.getElementById('content');
        textarea.classList.remove('d-none');
        textarea.rows = 10;
        return;
    }

    let editor;

    try {
        // Инициализация Editor.js
        editor = new EditorJS({
            holder: 'editorjs',
            placeholder: 'Начните писать статью...',
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
                const oldContent = document.getElementById('content').value;
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
        document.getElementById('editorjs').style.display = 'none';
        const textarea = document.getElementById('content');
        textarea.classList.remove('d-none');
        textarea.rows = 10;
        alert('Редактор не загрузился. Используйте обычное текстовое поле.');
        return;
    }

    // Обработка загрузки изображений
    const imageInput = document.getElementById('article_images');
    const imagesPreview = document.getElementById('images_preview');
    const imagesUrlsInput = document.getElementById('images_urls');
    let uploadedImages = [];

    imageInput.addEventListener('change', async function(e) {
        const files = Array.from(e.target.files);

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
            const response = await fetch('{{ route('announcement.images.upload') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                uploadedImages = result.images;
                imagesUrlsInput.value = JSON.stringify(uploadedImages.map(img => img.url));
                renderImagePreviews();
            } else {
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
        uploadedImages.splice(index, 1);
        imagesUrlsInput.value = JSON.stringify(uploadedImages.map(img => img.url));
        renderImagePreviews();

        if (uploadedImages.length === 0) {
            imageInput.value = '';
        }
    }

    // Сохранение данных перед отправкой формы
    const form = document.getElementById('articleForm');
    const submitBtn = document.getElementById('submitBtn');

    if (!form || !submitBtn) {
        console.error('Форма или кнопка не найдены!');
        return;
    }

    submitBtn.addEventListener('click', async function(e) {
        e.preventDefault();

        // Если editor не определен (fallback режим), проверяем textarea напрямую
        if (!editor) {
            const textareaValue = document.getElementById('content').value.trim();
            if (!textareaValue) {
                alert('Пожалуйста, введите содержание статьи');
                return false;
            }
            form.submit();
            return;
        }

        try {
            const outputData = await editor.save();
            const jsonData = JSON.stringify(outputData);

            document.getElementById('content').value = jsonData;

            if (!outputData.blocks || outputData.blocks.length === 0) {
                alert('Пожалуйста, введите содержание статьи');
                return false;
            }

            const savedValue = document.getElementById('content').value;
            if (!savedValue || savedValue === '{}' || savedValue === '{"blocks":[]}') {
                alert('Содержание пустое. Пожалуйста, добавьте текст.');
                return false;
            }

            // Отправляем форму
            form.submit();

        } catch (error) {
            console.error('Ошибка при сохранении данных:', error);
            alert('Произошла ошибка при сохранении. Попробуйте еще раз.');
        }
    });
});
</script>
@endpush
</x-app-layout>
