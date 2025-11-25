<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Товары магазина</h2>
            <a href="{{ route('admin.shop.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Добавить товар
            </a>
        </div>
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

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Категория</label>
                <select name="category" class="form-select">
                    <option value="">Все категории</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="">Все</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Активные</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Неактивные</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Удалённые</label>
                <select name="trashed" class="form-select">
                    <option value="">Только активные</option>
                    <option value="1" {{ request('trashed') == '1' ? 'selected' : '' }}>Только удалённые</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Поиск</label>
                <input type="text" name="search" class="form-control" placeholder="Название, описание..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Фильтр
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
                        <th style="width: 50px;">ID</th>
                        <th style="width: 80px;">Фото</th>
                        <th>Название</th>
                        <th>Категория</th>
                        <th style="width: 100px;">Цена</th>
                        <th style="width: 80px;">Продаж</th>
                        <th style="width: 80px;">Просм.</th>
                        <th style="width: 90px;">Статус</th>
                        <th style="width: 180px;">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="{{ $product->trashed() ? 'table-secondary' : '' }}">
                        <td>{{ $product->id }}</td>
                        <td>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     alt="{{ $product->name }}"
                                     class="rounded"
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                     style="width: 50px; height: 50px;">
                                    <i class="bi bi-box text-secondary"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.shop.products.show', $product) }}" class="text-decoration-none">
                                {{ $product->name }}
                            </a>
                            @if($product->trashed())
                                <span class="badge bg-danger ms-1">Удалён</span>
                            @endif
                            @if($product->short_description)
                                <br><small class="text-muted">{{ Str::limit($product->short_description, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($product->category)
                                <span class="badge bg-secondary">{{ $product->category->name }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <strong class="text-success">{{ number_format($product->price, 0, '.', ' ') }} ₽</strong>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $product->purchases_count }}</span>
                        </td>
                        <td>
                            <span class="text-muted">{{ $product->views_count }}</span>
                        </td>
                        <td>
                            @if($product->is_active)
                                <span class="badge bg-success">Активен</span>
                            @else
                                <span class="badge bg-secondary">Неактивен</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @if($product->trashed())
                                    <form method="POST"
                                          action="{{ route('admin.shop.products.restore', $product->id) }}"
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Восстановить">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.shop.products.show', $product) }}"
                                       class="btn btn-sm btn-outline-info" title="Просмотр">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.shop.products.edit', $product) }}"
                                       class="btn btn-sm btn-outline-primary" title="Редактировать">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.shop.products.destroy', $product) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Удалить товар {{ $product->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
                                @if(request('trashed'))
                                    Удалённых товаров нет
                                @else
                                    Товаров пока нет
                                @endif
                            </div>
                            @if(!request('trashed'))
                            <a href="{{ route('admin.shop.products.create') }}" class="btn btn-primary mt-2">
                                Добавить первый товар
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $products->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
</x-app-layout>
