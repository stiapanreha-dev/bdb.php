<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.shop.products.index') }}">Товары</a></li>
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <h2>{{ $product->name }}</h2>
            <div>
                <a href="{{ route('admin.shop.products.edit', $product) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i>Редактировать
                </a>
                <a href="{{ route('shop.show', $product->slug) }}" class="btn btn-outline-secondary" target="_blank">
                    <i class="bi bi-eye me-1"></i>На сайте
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        {{-- Product Info --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $product->name }}"
                                 class="img-fluid rounded">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-box text-secondary fs-1"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <h4 class="text-success mb-3">{{ number_format($product->price, 0, '.', ' ') }} ₽</h4>

                        @if($product->short_description)
                            <p class="text-muted">{{ $product->short_description }}</p>
                        @endif

                        <div class="row mt-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Категория</small>
                                <span class="badge bg-secondary">{{ $product->category->name ?? 'Без категории' }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Статус</small>
                                @if($product->is_active)
                                    <span class="badge bg-success">Активен</span>
                                @else
                                    <span class="badge bg-secondary">Неактивен</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-4">
                                <small class="text-muted d-block">Просмотров</small>
                                <strong>{{ $product->views_count }}</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Покупок</small>
                                <strong>{{ $product->purchases_count }}</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Конверсия</small>
                                <strong>
                                    @if($product->views_count > 0)
                                        {{ number_format(($product->purchases_count / $product->views_count) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Description --}}
        @if($product->description)
        <div class="card mb-3">
            <div class="card-header">Описание</div>
            <div class="card-body">
                @editorJsRender($product->description)
            </div>
        </div>
        @endif

        {{-- Purchase History --}}
        <div class="card">
            <div class="card-header">
                История покупок
                <span class="badge bg-info float-end">{{ $purchases->total() }}</span>
            </div>
            <div class="card-body">
                @if($purchases->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Покупатель</th>
                                <th>Цена</th>
                                <th>Статус</th>
                                <th>Дата</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->id }}</td>
                                <td>
                                    @if($purchase->user)
                                        {{ $purchase->user->name }}
                                        <br><small class="text-muted">{{ $purchase->user->email }}</small>
                                    @else
                                        <span class="text-muted">Удалён</span>
                                    @endif
                                </td>
                                <td>{{ number_format($purchase->price, 0, '.', ' ') }} ₽</td>
                                <td>
                                    @if($purchase->status === 'completed')
                                        <span class="badge bg-success">Завершена</span>
                                    @elseif($purchase->status === 'pending')
                                        <span class="badge bg-warning">Ожидает</span>
                                    @else
                                        <span class="badge bg-danger">Отменена</span>
                                    @endif
                                </td>
                                <td>{{ $purchase->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($purchases->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $purchases->links() }}
                </div>
                @endif
                @else
                <p class="text-muted text-center mb-0">Покупок пока нет</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        {{-- Meta Info --}}
        <div class="card mb-3">
            <div class="card-header">Информация</div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt>ID</dt>
                    <dd>{{ $product->id }}</dd>

                    <dt>Slug</dt>
                    <dd><code>{{ $product->slug }}</code></dd>

                    <dt>Создатель</dt>
                    <dd>{{ $product->creator->name ?? 'Система' }}</dd>

                    <dt>Создан</dt>
                    <dd>{{ $product->created_at->format('d.m.Y H:i') }}</dd>

                    <dt>Обновлён</dt>
                    <dd>{{ $product->updated_at->format('d.m.Y H:i') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Revenue Stats --}}
        <div class="card mb-3 bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Выручка с товара</h6>
                <h2 class="mb-0">
                    {{ number_format($product->purchases()->where('status', 'completed')->sum('price'), 0, '.', ' ') }} ₽
                </h2>
            </div>
        </div>

        {{-- Actions --}}
        <div class="card">
            <div class="card-body">
                <a href="{{ route('admin.shop.products.edit', $product) }}" class="btn btn-primary w-100 mb-2">
                    <i class="bi bi-pencil me-1"></i>Редактировать
                </a>
                <a href="{{ route('admin.shop.products.index') }}" class="btn btn-secondary w-100">
                    Назад к списку
                </a>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
