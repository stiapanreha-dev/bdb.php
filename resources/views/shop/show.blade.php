<x-app-layout>

<div class="row">
    <div class="col-md-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Магазин</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop.index', ['category' => $product->category_id]) }}">{{ $product->category->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <!-- Заголовок с кнопками покупки -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="mb-0">{{ $product->name }}</h2>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <h3 class="mb-0" style="color: #27ae60; font-weight: bold;">
                    {{ $product->formatted_price }}
                </h3>
                @auth
                    {{-- Добавить в корзину --}}
                    <form method="POST" action="{{ route('shop.cart.add', $product->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-success">
                            <i class="bi bi-cart-plus"></i> В корзину
                        </button>
                    </form>
                    {{-- Купить сейчас --}}
                    <form id="buy-now-form" method="POST" action="{{ route('shop.purchase', $product->id) }}" class="d-inline">
                        @csrf
                    </form>
                    <button type="button"
                            class="btn btn-lg"
                            style="background-color: #3598db; color: white;"
                            x-data
                            @click="$dispatch('confirm', {
                                title: 'Купить сейчас?',
                                message: '{{ $product->name }} за {{ $product->formatted_price }}',
                                type: 'success',
                                confirmText: 'Купить',
                                form: 'buy-now-form'
                            })">
                        <i class="bi bi-cart-check"></i> Купить сейчас
                    </button>
                @else
                    <a href="{{ route('login') }}"
                       class="btn btn-lg"
                       style="background-color: #3598db; color: white;">
                        <i class="bi bi-box-arrow-in-right"></i> Войти для покупки
                    </a>
                @endauth
            </div>
        </div>

        <!-- Статистика -->
        <div class="mb-4">
            <span class="badge bg-secondary me-2">
                <i class="bi bi-eye"></i> Просмотры: {{ $product->views_count }}
            </span>
            <span class="badge bg-success me-2">
                <i class="bi bi-cart-check"></i> Продажи: {{ $product->purchases_count }}
            </span>
            @if($product->attachment && $product->formatted_attachment_size)
            <span class="badge bg-info">
                <i class="bi bi-file-earmark-arrow-down"></i> Файл: {{ $product->formatted_attachment_size }}
            </span>
            @endif
        </div>

        <!-- Основная карточка -->
        <div class="card shadow-sm">
            <div class="card-header" style="background-color: #f8f9fa;">
                <h4 class="mb-0">Описание</h4>
            </div>
            <div class="card-body">
                <!-- Картинка товара -->
                @if($product->image)
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->name }}"
                             class="img-fluid rounded"
                             style="max-height: 400px;">
                    </div>
                @endif

                <!-- Полное описание (Editor.js) -->
                @if($product->description)
                    <div class="product-description">
                        @editorJsRender(is_array($product->description) ? json_encode($product->description) : $product->description)
                    </div>
                @else
                    <p class="text-muted">Описание товара отсутствует.</p>
                @endif
            </div>
        </div>

        <!-- Информация о категории -->
        <div class="mt-4">
            <p class="text-muted">
                <i class="bi bi-tag"></i> Категория:
                <a href="{{ route('shop.index', ['category' => $product->category_id]) }}">
                    {{ $product->category->name }}
                </a>
            </p>
        </div>

        <!-- Кнопка назад -->
        <div class="mt-4">
            <a href="{{ route('shop.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Вернуться в магазин
            </a>
        </div>
    </div>
</div>

</x-app-layout>
