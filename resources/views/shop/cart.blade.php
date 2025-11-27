<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Магазин</a></li>
                <li class="breadcrumb-item active">Корзина</li>
            </ol>
        </nav>
        <h2><i class="bi bi-cart3 me-2"></i>Корзина</h2>
    </div>
</div>

@if($cart && $cart->items->count() > 0)
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-box me-1"></i>Товары в корзине</span>
                <form id="clear-cart-form" method="POST" action="{{ route('shop.cart.clear') }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                </form>
                <button type="button" class="btn btn-sm btn-outline-danger"
                        x-data
                        @click="$dispatch('confirm', {
                            title: 'Очистить корзину?',
                            message: 'Все товары будут удалены из корзины',
                            type: 'danger',
                            confirmText: 'Очистить',
                            form: 'clear-cart-form'
                        })">
                    <i class="bi bi-trash"></i> Очистить
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;"></th>
                                <th>Товар</th>
                                <th class="text-end" style="width: 120px;">Цена</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart->items as $item)
                            <tr>
                                <td>
                                    @if($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                         alt="{{ $item->product->name }}"
                                         class="rounded"
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                    <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                         style="width: 60px; height: 60px;">
                                        <i class="bi bi-box text-muted"></i>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('shop.show', $item->product->slug) }}" class="text-decoration-none">
                                        <strong>{{ $item->product->name }}</strong>
                                    </a>
                                    @if($item->product->category)
                                    <br><small class="text-muted">{{ $item->product->category->name }}</small>
                                    @endif
                                    @if($item->product->attachment && $item->product->formatted_attachment_size)
                                    <br><small class="text-info"><i class="bi bi-file-earmark-arrow-down"></i> {{ $item->product->formatted_attachment_size }}</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">{{ $item->product->formatted_price }}</strong>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('shop.cart.remove', $item->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-receipt me-1"></i>Итого
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Товаров:</span>
                    <span>{{ $cart->item_count }} шт.</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span class="h5 mb-0">К оплате:</span>
                    <span class="h5 mb-0 text-success">{{ $cart->formatted_total }}</span>
                </div>

                <div class="d-flex justify-content-between mb-3 text-muted small">
                    <span>Ваш баланс:</span>
                    <span>{{ number_format(auth()->user()->balance, 0, '.', ' ') }} ₽</span>
                </div>

                @if(auth()->user()->balance >= $cart->total)
                <form id="checkout-form" method="POST" action="{{ route('shop.cart.checkout') }}">
                    @csrf
                </form>
                <button type="button" class="btn btn-success w-100 btn-lg"
                        x-data
                        @click="$dispatch('confirm', {
                            title: 'Оформить заказ?',
                            message: 'Сумма к оплате: {{ $cart->formatted_total }}',
                            type: 'success',
                            confirmText: 'Оформить',
                            form: 'checkout-form'
                        })">
                    <i class="bi bi-check-circle me-1"></i>Оформить заказ
                </button>
                @else
                <div class="alert alert-warning mb-3">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Недостаточно средств. Пополните баланс.
                </div>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary w-100">
                    <i class="bi bi-wallet2 me-1"></i>Пополнить баланс
                </a>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <a href="{{ route('shop.index') }}" class="btn btn-outline-primary w-100">
                    <i class="bi bi-arrow-left me-1"></i>Продолжить покупки
                </a>
            </div>
        </div>
    </div>
</div>
@else
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-cart-x display-1 text-muted"></i>
        <h4 class="mt-3">Корзина пуста</h4>
        <p class="text-muted">Добавьте товары из магазина</p>
        <a href="{{ route('shop.index') }}" class="btn btn-primary">
            <i class="bi bi-shop me-1"></i>Перейти в магазин
        </a>
    </div>
</div>
@endif
</x-app-layout>
