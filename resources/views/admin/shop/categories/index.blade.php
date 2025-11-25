<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Категории магазина</h2>
            <a href="{{ route('admin.shop.categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Создать категорию
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

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 80px;">Фото</th>
                        <th>Название</th>
                        <th>Slug</th>
                        <th style="width: 100px;">Товаров</th>
                        <th style="width: 80px;">Порядок</th>
                        <th style="width: 100px;">Статус</th>
                        <th style="width: 180px;">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    {{-- Parent category --}}
                    <tr class="table-light">
                        <td>{{ $category->id }}</td>
                        <td>
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}"
                                     alt="{{ $category->name }}"
                                     class="rounded"
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                     style="width: 50px; height: 50px;">
                                    <i class="bi bi-folder text-white"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $category->name }}</strong>
                            @if($category->description)
                                <br><small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                            @endif
                        </td>
                        <td><code>{{ $category->slug }}</code></td>
                        <td>
                            <span class="badge bg-info">{{ $category->products->count() }}</span>
                        </td>
                        <td>{{ $category->sort_order }}</td>
                        <td>
                            @if($category->is_active)
                                <span class="badge bg-success">Активна</span>
                            @else
                                <span class="badge bg-secondary">Неактивна</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.shop.categories.edit', $category) }}"
                                   class="btn btn-sm btn-outline-primary" title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.shop.categories.destroy', $category) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Удалить категорию {{ $category->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- Child categories --}}
                    @foreach($category->children as $child)
                    <tr>
                        <td>{{ $child->id }}</td>
                        <td>
                            @if($child->image)
                                <img src="{{ asset('storage/' . $child->image) }}"
                                     alt="{{ $child->name }}"
                                     class="rounded"
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                     style="width: 50px; height: 50px;">
                                    <i class="bi bi-folder text-secondary"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted me-2">|---</span>{{ $child->name }}
                            @if($child->description)
                                <br><small class="text-muted ms-4">{{ Str::limit($child->description, 50) }}</small>
                            @endif
                        </td>
                        <td><code>{{ $child->slug }}</code></td>
                        <td>
                            <span class="badge bg-info">{{ $child->products->count() }}</span>
                        </td>
                        <td>{{ $child->sort_order }}</td>
                        <td>
                            @if($child->is_active)
                                <span class="badge bg-success">Активна</span>
                            @else
                                <span class="badge bg-secondary">Неактивна</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.shop.categories.edit', $child) }}"
                                   class="btn btn-sm btn-outline-primary" title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.shop.categories.destroy', $child) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Удалить категорию {{ $child->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                                Категорий пока нет
                            </div>
                            <a href="{{ route('admin.shop.categories.create') }}" class="btn btn-primary mt-2">
                                Создать первую категорию
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
