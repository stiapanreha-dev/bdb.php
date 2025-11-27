<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Управление тарифами</h2>
            <a href="{{ route('admin.tariffs.create') }}" class="btn btn-primary">
                Создать тариф
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
                        <th>ID</th>
                        <th>Название</th>
                        <th>Длительность</th>
                        <th>Цена</th>
                        <th>Статус</th>
                        <th>Активных подписок</th>
                        <th>Создан</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tariffs as $tariff)
                    <tr>
                        <td>{{ $tariff->id }}</td>
                        <td>{{ $tariff->name }}</td>
                        <td>{{ $tariff->duration_days }} дней</td>
                        <td>{{ number_format($tariff->price, 2, '.', ' ') }} руб</td>
                        <td>
                            @if($tariff->is_active)
                                <span class="badge bg-success">Активен</span>
                            @else
                                <span class="badge bg-secondary">Неактивен</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">
                                {{ $tariff->subscriptions->count() }}
                            </span>
                        </td>
                        <td>{{ $tariff->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.tariffs.show', $tariff) }}"
                                   class="btn btn-sm btn-info">
                                    История
                                </a>
                                <a href="{{ route('admin.tariffs.edit', $tariff) }}"
                                   class="btn btn-sm btn-warning">
                                    Изменить
                                </a>
                                <form id="delete-tariff-{{ $tariff->id }}" method="POST"
                                      action="{{ route('admin.tariffs.destroy', $tariff) }}" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button" class="btn btn-sm btn-danger"
                                        x-data
                                        @click="$dispatch('confirm', {
                                            title: 'Удалить тариф?',
                                            message: '{{ $tariff->name }}',
                                            type: 'danger',
                                            confirmText: 'Удалить',
                                            form: 'delete-tariff-{{ $tariff->id }}'
                                        })">
                                    Удалить
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Тарифов нет</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
