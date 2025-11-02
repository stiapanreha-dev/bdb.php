<x-app-layout>
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">Добавить объявление</h2>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('announcements.store') }}">
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
                            <textarea class="d-none @error('description') is-invalid @enderror" id="description" name="description" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
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
                            <button type="submit" class="btn btn-primary">
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
    const form = document.querySelector('form');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        try {
            const outputData = await editor.save();
            document.getElementById('description').value = JSON.stringify(outputData);

            if (!outputData.blocks || outputData.blocks.length === 0) {
                alert('Пожалуйста, введите описание объявления');
                return false;
            }

            form.submit();
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
