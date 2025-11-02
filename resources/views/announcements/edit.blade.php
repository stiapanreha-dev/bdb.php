<x-app-layout>
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">Редактировать объявление</h2>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('announcements.update', $announcement->id) }}">
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

                        <!-- Категория -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Категория</label>
                            <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', $announcement->category) }}" placeholder="Например: Строительство, IT, Продукты">
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Необязательное поле</small>
                        </div>

                        <!-- Заголовок -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Заголовок <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $announcement->title) }}" required placeholder="Краткое описание вашего объявления">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Описание -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание <span class="text-danger">*</span></label>
                            <div id="editor" style="min-height: 300px;">{!! old('description', $announcement->description) !!}</div>
                            <textarea class="d-none @error('description') is-invalid @enderror" id="description" name="description" required>{{ old('description', $announcement->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
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
        placeholder: 'Введите описание объявления... Вы можете вставить изображение из буфера обмена (Ctrl+V)'
    });

    // Обработка вставки изображений из буфера обмена с сжатием
    quill.root.addEventListener('paste', function(e) {
        var clipboardData = e.clipboardData || window.clipboardData;
        var items = clipboardData.items;

        if (items) {
            for (var i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    e.preventDefault();

                    var blob = items[i].getAsFile();

                    // Сжатие изображения перед вставкой
                    compressImage(blob, function(compressedBase64) {
                        var range = quill.getSelection(true);
                        quill.insertEmbed(range.index, 'image', compressedBase64);
                        quill.setSelection(range.index + 1);
                    });
                }
            }
        }
    });

    // Функция сжатия изображения
    function compressImage(file, callback) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = new Image();
            img.onload = function() {
                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');

                // Максимальные размеры
                var maxWidth = 800;
                var maxHeight = 800;
                var width = img.width;
                var height = img.height;

                // Рассчитываем новые размеры с сохранением пропорций
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

                // Заливаем белый фон (для прозрачных PNG)
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, width, height);

                // Рисуем изображение
                ctx.drawImage(img, 0, 0, width, height);

                // Конвертируем в base64 с качеством 0.6 (JPEG)
                var compressedBase64 = canvas.toDataURL('image/jpeg', 0.6);

                callback(compressedBase64);
            };
            img.onerror = function() {
                alert('Ошибка загрузки изображения. Попробуйте другое изображение.');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // Синхронизация содержимого редактора с textarea перед отправкой формы
    var form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        var content = document.getElementById('description');
        content.value = quill.root.innerHTML;

        // Проверка на пустое содержимое
        if (quill.getText().trim().length === 0) {
            e.preventDefault();
            alert('Пожалуйста, введите описание объявления');
            return false;
        }
    });

    // Загрузка содержимого при загрузке страницы
    var oldContent = document.getElementById('description').value;
    if (oldContent) {
        quill.root.innerHTML = oldContent;
    }

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

    // Вызываем при загрузке для установки начального состояния
    toggleRegisterPurchase();
});
</script>
@endpush
</x-app-layout>
