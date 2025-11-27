@auth
    @php
        $cartCount = auth()->user()->cart?->item_count ?? 0;
    @endphp
    <a href="{{ route('shop.cart') }}" {{ $attributes->merge(['class' => 'nav-link position-relative text-white']) }} title="Корзина">
        <i class="bi bi-cart3"></i>
        @if($cartCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                {{ $cartCount > 99 ? '99+' : $cartCount }}
            </span>
        @endif
    </a>
@endauth
