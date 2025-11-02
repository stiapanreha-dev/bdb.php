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
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/simple-image@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/checklist@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/code@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/table@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
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
                class: EditorjsList,
                inlineToolbar: true
            },
            image: SimpleImage,
            quote: {
                class: Quote,
                inlineToolbar: true
            },
            code: Code,
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
