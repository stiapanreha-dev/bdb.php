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

        <!-- Header with title, price and buttons -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="mb-0">{{ $product->name }}</h2>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <h3 class="mb-0" style="color: #27ae60; font-weight: bold;">
                    {{ $product->formatted_price }}
                </h3>
                @auth
                    @if($hasPurchased)
                        <span class="badge bg-success fs-6 py-2 px-3">
                            <i class="bi bi-check-circle"></i> Куплено
                        </span>
                    @else
                        {{-- Add to cart --}}
                        <form method="POST" action="{{ route('shop.cart.add', $product->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-success">
                                <i class="bi bi-cart-plus"></i> В корзину
                            </button>
                        </form>
                        {{-- Buy now --}}
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
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="btn btn-lg"
                       style="background-color: #3598db; color: white;">
                        <i class="bi bi-box-arrow-in-right"></i> Войти для покупки
                    </a>
                @endauth
            </div>
        </div>

        <!-- Statistics -->
        <div class="mb-4">
            <span class="badge bg-secondary me-2">
                <i class="bi bi-eye"></i> Просмотры: {{ $product->views_count }}
            </span>
            <span class="badge bg-success me-2">
                <i class="bi bi-cart-check"></i> Продажи: {{ $product->purchases_count }}
            </span>
            @if($product->files->count() > 0)
            <span class="badge bg-info">
                <i class="bi bi-file-earmark-arrow-down"></i>
                {{ $product->files->count() }} {{ trans_choice('файл|файла|файлов', $product->files->count()) }},
                {{ $product->formatted_total_files_size }}
            </span>
            @endif
        </div>

        <!-- Main card with image left, description right -->
        <div class="card shadow-sm mb-4">
            <div class="card-header" style="background-color: #f8f9fa;">
                <h4 class="mb-0">Описание</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($product->image)
                    <div class="col-md-4 mb-3 mb-md-0">
                        <img src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->name }}"
                             class="img-fluid rounded"
                             style="max-height: 400px; width: 100%; object-fit: contain;">
                    </div>
                    <div class="col-md-8">
                    @else
                    <div class="col-md-12">
                    @endif
                        @if($product->description)
                            <div class="product-description">
                                @php
                                    $descJson = $product->description;
                                    if (is_array($descJson) || is_object($descJson)) {
                                        $descJson = json_encode($descJson);
                                    }
                                @endphp
                                @editorJsRender($descJson)
                            </div>
                        @else
                            <p class="text-muted">Описание товара отсутствует.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Files section -->
        @if($product->files->count() > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fa;">
                <h5 class="mb-0">
                    <i class="bi bi-folder2-open me-2"></i>Файлы к покупке
                </h5>
                <span class="badge bg-primary">
                    {{ $product->files->count() }} {{ trans_choice('файл|файла|файлов', $product->files->count()) }},
                    всего {{ $product->formatted_total_files_size }}
                </span>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($product->files as $file)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="{{ $file->icon_class }} me-2 text-primary"></i>
                        <span>{{ $file->original_name }}</span>
                        <small class="text-muted ms-2">({{ $file->formatted_size }})</small>
                    </div>
                    @auth
                        @if($hasPurchased)
                            <a href="{{ route('shop.download', ['slug' => $product->slug, 'file' => $file->id]) }}"
                               class="btn btn-sm btn-success">
                                <i class="bi bi-download"></i> Скачать
                            </a>
                        @else
                            <button class="btn btn-sm btn-secondary" disabled
                                    title="Доступно после покупки">
                                <i class="bi bi-lock"></i> Скачать
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-secondary"
                           title="Войдите для скачивания">
                            <i class="bi bi-lock"></i> Скачать
                        </a>
                    @endauth
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Category info -->
        <div class="mt-4">
            <p class="text-muted">
                <i class="bi bi-tag"></i> Категория:
                <a href="{{ route('shop.index', ['category' => $product->category_id]) }}">
                    {{ $product->category->name }}
                </a>
            </p>
        </div>

        <!-- Back button -->
        <div class="mt-4">
            <a href="{{ route('shop.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Вернуться в магазин
            </a>
        </div>
    </div>
</div>

</x-app-layout>
