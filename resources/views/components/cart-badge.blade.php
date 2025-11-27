@auth
    @php
        $cartCount = auth()->user()->cart?->items()->count() ?? 0;
    @endphp
    <a href="{{ route('shop.cart') }}" {{ $attributes->merge(['class' => 'btn btn-outline-light position-relative']) }} title="Корзина">
        <i class="bi bi-cart3"></i>
        @if($cartCount > 0)
            <span class="position-absolute badge rounded-pill bg-danger" style="font-size: 0.6rem; top: 0; right: -8px;">
                {{ $cartCount > 99 ? '99+' : $cartCount }}
            </span>
        @endif
    </a>
@endauth
