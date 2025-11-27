<x-app-layout>

<div class="row">
    <!-- Левая боковая панель с категориями -->
    <div class="col-md-3">
        <div class="card shadow-sm mb-4">
            <div class="card-header" style="background-color: #3598db; color: white;">
                <h5 class="mb-0"><i class="bi bi-grid-3x3-gap"></i> ВСЕ КАТЕГОРИИ</h5>
            </div>
            <div class="list-group list-group-flush">
                @forelse($categories as $category)
                    <a href="{{ route('shop.category', $category->slug) }}"
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $currentCategory && $currentCategory->id == $category->id ? 'active' : '' }}">
                        {{ $category->name }}
                        @if($category->children->count() > 0)
                            <i class="bi bi-chevron-right"></i>
                        @endif
                    </a>

                    @if($category->children->count() > 0 && $currentCategory && ($currentCategory->id == $category->id || $currentCategory->parent_id == $category->id))
                        @foreach($category->children as $child)
                            <a href="{{ route('shop.category', $child->slug) }}"
                               class="list-group-item list-group-item-action ps-4 {{ $currentCategory && $currentCategory->id == $child->id ? 'active' : '' }}">
                                {{ $child->name }}
                            </a>
                        @endforeach
                    @endif
                @empty
                    <div class="list-group-item text-muted">Категорий пока нет</div>
                @endforelse
            </div>
        </div>

{{--        <div class="card shadow-sm">--}}
{{--            <div class="card-body">--}}
{{--                <p class="small text-muted mb-0">--}}
{{--                    <i class="bi bi-info-circle"></i> Можно сделать симпатичный выпадающий список категорий товара для экономии места + поиск товара.--}}
{{--                </p>--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>

    <!-- Основной контент -->
    <div class="col-md-9">
        <!-- Поиск -->
        <div class="card shadow-sm mb-4" style="border-left: 4px solid #3598db;">
            <div class="card-body">
                <form method="GET" action="{{ route('shop.index') }}" class="row g-3">
                    <div class="col-md-10">
                        <input type="text"
                               class="form-control"
                               name="search"
                               placeholder="Искать товары или категории"
                               value="{{ request('search') }}"
                               style="border-color: #2e86c1;">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn w-100" style="background-color: #3598db; color: white;">
                            <i class="bi bi-search"></i> Найти
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Список товаров -->
        @forelse($products as $product)
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="row">
                        <!-- Картинка -->
                        <div class="col-md-2">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     alt="{{ $product->name }}"
                                     class="img-fluid rounded">
                            @else
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="height: 100px;">
                                    <i class="bi bi-image text-white" style="font-size: 2rem;"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Информация о товаре -->
                        <div class="col-md-7">
                            <h5 class="mb-2">
                                <a href="{{ route('shop.show', $product->slug) }}" class="text-decoration-none text-dark">
                                    {{ $product->name }}
                                </a>
                            </h5>
                            <div class="mb-2">
                                <span class="text-muted me-3">
                                    <i class="bi bi-eye"></i> Просмотры: {{ $product->views_count }}
                                </span>
                                <span class="text-muted">
                                    <i class="bi bi-cart-check"></i> Продажи: {{ $product->purchases_count }}
                                </span>
                            </div>
                            @if($product->short_description)
                                <p class="text-muted mb-0">{{ $product->short_description }}</p>
                            @endif
                        </div>

                        <!-- Цена и кнопки -->
                        <div class="col-md-3 text-end d-flex flex-column justify-content-between">
                            <div>
                                <h3 class="mb-0" style="color: #27ae60; font-weight: bold;">
                                    {{ $product->formatted_price }}
                                </h3>
                            </div>
                            <div class="d-flex gap-2 justify-content-end">
                                @auth
                                <form method="POST" action="{{ route('shop.cart.add', $product->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="В корзину">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                </form>
                                @endauth
                                <a href="{{ route('shop.show', $product->slug) }}"
                                   class="btn btn-primary"
                                   style="background-color: #3598db; border-color: #3598db;">
                                    <i class="bi bi-eye"></i> Подробнее
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Товары не найдены.
                @if(request('search') || $currentCategory)
                    <a href="{{ route('shop.index') }}">Сбросить фильтры</a>
                @endif
            </div>
        @endforelse

        <!-- Пагинация -->
        @if($products->hasPages())
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

</x-app-layout>
