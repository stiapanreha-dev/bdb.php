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
                            <div id="editorjs"></div>
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
