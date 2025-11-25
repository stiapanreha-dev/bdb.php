<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>История покупок магазина</h2>
            <a href="{{ route('admin.shop.statistics') }}" class="btn btn-outline-primary">
                <i class="bi bi-graph-up me-1"></i>Статистика
            </a>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="">Все</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            @if($status === 'completed') Завершена
                            @elseif($status === 'pending') Ожидает
                            @else Отменена
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Товар</label>
                <select name="product_id" class="form-select">
                    <option value="">Все товары</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ Str::limit($product->name, 40) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Дата с</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Дата по</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">ID пользователя</label>
                <input type="number" name="user_id" class="form-control" placeholder="User ID" value="{{ request('user_id') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Товар</th>
                        <th>Покупатель</th>
                        <th>Цена</th>
                        <th>Статус</th>
                        <th>Дата</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->id }}</td>
                        <td>
                            @if($purchase->product)
                                <a href="{{ route('admin.shop.products.show', $purchase->product) }}" class="text-decoration-none">
                                    {{ Str::limit($purchase->product->name, 35) }}
                                </a>
                            @else
                                <span class="text-muted">Товар удалён</span>
                            @endif
                        </td>
                        <td>
                            @if($purchase->user)
                                <strong>{{ $purchase->user->name }}</strong>
                                <br><small class="text-muted">{{ $purchase->user->email }}</small>
                                <br><small class="text-muted">ID: {{ $purchase->user->id }}</small>
                            @else
                                <span class="text-muted">Пользователь удалён</span>
                            @endif
                        </td>
                        <td>
                            <strong class="text-success">{{ number_format($purchase->price, 0, '.', ' ') }} ₽</strong>
                        </td>
                        <td>
                            @if($purchase->status === 'completed')
                                <span class="badge bg-success">Завершена</span>
                            @elseif($purchase->status === 'pending')
                                <span class="badge bg-warning">Ожидает</span>
                            @else
                                <span class="badge bg-danger">Отменена</span>
                            @endif
                        </td>
                        <td>
                            {{ $purchase->created_at->format('d.m.Y') }}
                            <br><small class="text-muted">{{ $purchase->created_at->format('H:i') }}</small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                                Покупок не найдено
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($purchases->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $purchases->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Summary --}}
@if($purchases->total() > 0)
<div class="card mt-3">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-4">
                <h5 class="text-muted">Всего покупок на странице</h5>
                <h3>{{ $purchases->count() }} из {{ $purchases->total() }}</h3>
            </div>
            <div class="col-md-4">
                <h5 class="text-muted">Сумма на странице</h5>
                <h3 class="text-success">{{ number_format($purchases->sum('price'), 0, '.', ' ') }} ₽</h3>
            </div>
            <div class="col-md-4">
                <h5 class="text-muted">Быстрые ссылки</h5>
                <a href="{{ route('admin.shop.products.index') }}" class="btn btn-sm btn-outline-primary me-1">Товары</a>
                <a href="{{ route('admin.shop.statistics') }}" class="btn btn-sm btn-outline-primary">Статистика</a>
            </div>
        </div>
    </div>
</div>
@endif
</x-app-layout>
