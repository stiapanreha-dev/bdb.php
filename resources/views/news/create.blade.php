<x-app-layout>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title mb-4">Добавить новость</h3>

                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('news.store') }}" id="newsForm">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">Заголовок</label>
                        <input type="text"
                               class="form-control @error('title') is-invalid @enderror"
                               id="title"
                               name="title"
                               value="{{ old('title') }}"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Содержание</label>
                        <div id="editorjs"></div>
                        <textarea class="d-none @error('content') is-invalid @enderror"
                                  id="content"
                                  name="content">{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Опубликовать</button>
                    <a href="{{ route('news.index') }}" class="btn btn-secondary">Отмена</a>
                </form>
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@2.28.2/dist/editorjs.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@2.7.0/dist/bundle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@1.8.0/dist/bundle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/image@2.8.1/dist/bundle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@2.5.0/dist/bundle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/code@2.8.0/dist/bundle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@1.3.0/dist/bundle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/table@2.2.2/dist/table.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/checklist@1.5.0/dist/bundle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@2.5.3/dist/bundle.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация Editor.js
    const editor = new EditorJS({
        holder: 'editorjs',
        placeholder: 'Начните писать вашу новость...',
        tools: {
            header: {
                class: Header,
                config: {
                    levels: [1, 2, 3, 4],
                    defaultLevel: 2
                }
            },
            list: {
                class: List,
                inlineToolbar: true
            },
            image: {
                class: ImageTool,
                config: {
                    uploader: {
                        uploadByFile(file) {
                            return compressAndConvertImage(file);
                        }
                    }
                }
            },
            quote: {
                class: Quote,
                inlineToolbar: true
            },
            code: {
                class: Code
            },
            delimiter: {
                class: Delimiter
            },
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

    // Функция сжатия и конвертации изображения
    function compressAndConvertImage(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    let width = img.width;
                    let height = img.height;
                    const maxWidth = 1200;
                    const maxHeight = 1200;

                    if (width > height) {
                        if (width > maxWidth) {
                            height *= maxWidth / width;
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width *= maxHeight / height;
                            height = maxHeight;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;
                    ctx.fillStyle = '#FFFFFF';
                    ctx.fillRect(0, 0, width, height);
                    ctx.drawImage(img, 0, 0, width, height);

                    const compressedBase64 = canvas.toDataURL('image/jpeg', 0.7);

                    resolve({
                        success: 1,
                        file: {
                            url: compressedBase64
                        }
                    });
                };
                img.onerror = () => reject(new Error('Ошибка загрузки изображения'));
                img.src = e.target.result;
            };
            reader.onerror = () => reject(new Error('Ошибка чтения файла'));
            reader.readAsDataURL(file);
        });
    }

    // Сохранение данных перед отправкой формы
    const form = document.getElementById('newsForm');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        try {
            const outputData = await editor.save();
            document.getElementById('content').value = JSON.stringify(outputData);

            if (!outputData.blocks || outputData.blocks.length === 0) {
                alert('Пожалуйста, введите содержание новости');
                return false;
            }

            form.submit();
        } catch (error) {
            console.error('Ошибка сохранения:', error);
            alert('Произошла ошибка при сохранении. Попробуйте еще раз.');
        }
    });
});
</script>
@endpush
</x-app-layout>
