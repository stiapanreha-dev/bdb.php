<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Магазин</a></li>
                <li class="breadcrumb-item active">Мои покупки</li>
            </ol>
        </nav>
        <h2><i class="bi bi-bag-check me-2"></i>Мои покупки</h2>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($purchases->count() > 0)
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Товар</th>
                        <th class="text-end">Цена</th>
                        <th class="text-center">Статус</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchases as $purchase)
                    <tr>
                        <td class="text-muted">
                            {{ $purchase->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td>
                            @if($purchase->product)
                                <div class="d-flex align-items-center">
                                    @if($purchase->product->image)
                                    <img src="{{ asset('storage/' . $purchase->product->image) }}"
                                         alt="{{ $purchase->product->name }}"
                                         class="me-2 rounded"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                    <div class="me-2 rounded bg-light d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-box text-muted"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <strong>{{ $purchase->product->name }}</strong>
                                        @if($purchase->product->category)
                                        <br><small class="text-muted">{{ $purchase->product->category->name }}</small>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">Товар удалён</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <strong class="text-success">{{ number_format($purchase->price, 0, '.', ' ') }} ₽</strong>
                        </td>
                        <td class="text-center">
                            @if($purchase->status === 'completed')
                                <span class="badge bg-success">Завершён</span>
                            @elseif($purchase->status === 'pending')
                                <span class="badge bg-warning">В обработке</span>
                            @else
                                <span class="badge bg-secondary">{{ $purchase->status }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($purchase->product)
                                @if($purchase->status === 'completed' && $purchase->product->attachment)
                                <a href="{{ route('shop.download', $purchase->product->id) }}"
                                   class="btn btn-sm btn-success me-1"
                                   title="Скачать файл">
                                    <i class="bi bi-download"></i>
                                    {{ $purchase->product->attachment_name ?? 'Файл' }}
                                    @if($purchase->product->formatted_attachment_size)
                                    <span class="badge bg-light text-dark ms-1">{{ $purchase->product->formatted_attachment_size }}</span>
                                    @endif
                                </a>
                                @endif
                                @if($purchase->product->is_active)
                                <a href="{{ route('shop.show', $purchase->product->slug) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Просмотр товара">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $purchases->links() }}
    </div>
</div>

{{-- Summary --}}
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h5 class="text-muted mb-1">Всего покупок</h5>
                <h3 class="mb-0">{{ $purchases->total() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h5 class="text-muted mb-1">Потрачено</h5>
                <h3 class="mb-0 text-success">
                    {{ number_format($purchases->sum('price'), 0, '.', ' ') }} ₽
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h5 class="text-muted mb-1">Текущий баланс</h5>
                <h3 class="mb-0 text-primary">
                    {{ number_format(auth()->user()->balance, 0, '.', ' ') }} ₽
                </h3>
            </div>
        </div>
    </div>
</div>
@else
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-bag-x display-1 text-muted"></i>
        <h4 class="mt-3">У вас пока нет покупок</h4>
        <p class="text-muted">Перейдите в магазин, чтобы выбрать товары</p>
        <a href="{{ route('shop.index') }}" class="btn btn-primary">
            <i class="bi bi-shop me-1"></i>Перейти в магазин
        </a>
    </div>
</div>
@endif
</x-app-layout>
