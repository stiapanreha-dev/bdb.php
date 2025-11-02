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
                        <div id="editor" style="min-height: 300px;">{!! old('content') !!}</div>
                        <textarea class="d-none @error('content') is-invalid @enderror"
                                  id="content"
                                  name="content"
                                  required>{{ old('content') }}</textarea>
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
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor {
        min-height: 250px;
    }
    .ql-editor img {
        max-width: 100%;
        height: auto;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация Quill
    var quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                ['link', 'image'],
                ['clean']
            ]
        },
        placeholder: 'Введите текст новости... Вы можете вставить изображение из буфера обмена (Ctrl+V)'
    });

    // Обработка вставки изображений из буфера обмена
    quill.root.addEventListener('paste', function(e) {
        var clipboardData = e.clipboardData || window.clipboardData;
        var items = clipboardData.items;

        if (items) {
            for (var i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    e.preventDefault();

                    var blob = items[i].getAsFile();
                    var reader = new FileReader();

                    reader.onload = function(event) {
                        var base64 = event.target.result;
                        var range = quill.getSelection(true);
                        quill.insertEmbed(range.index, 'image', base64);
                        quill.setSelection(range.index + 1);
                    };

                    reader.readAsDataURL(blob);
                }
            }
        }
    });

    // Синхронизация содержимого редактора с textarea перед отправкой формы
    var form = document.getElementById('newsForm');
    form.addEventListener('submit', function(e) {
        var content = document.getElementById('content');
        content.value = quill.root.innerHTML;

        // Проверка на пустое содержимое
        if (quill.getText().trim().length === 0) {
            e.preventDefault();
            alert('Пожалуйста, введите содержание новости');
            return false;
        }
    });

    // Загрузка старого содержимого при ошибках валидации
    var oldContent = document.getElementById('content').value;
    if (oldContent) {
        quill.root.innerHTML = oldContent;
    }
});
</script>
@endpush
</x-app-layout>
