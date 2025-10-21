<x-app-layout>

@guest
<div class="alert alert-warning">
    <strong>Ограниченный доступ:</strong> Отображаются только последние 50 записей.
    <a href="{{ route('login') }}" class="alert-link">Войдите</a> для полного доступа.
</div>
@else
    @if($show_masked_email || $show_masked_phone)
    <div class="alert alert-info">
        <strong>Обратите внимание:</strong> Email адреса и телефоны частично скрыты.
        <a href="#" class="alert-link" data-bs-toggle="modal" data-bs-target="#balanceModal">Пополните баланс</a> для полного доступа.
    </div>
    @endif
@endguest

<!-- Database Selector -->
<div class="row mb-4">
    <div class="col-md-4">
        <label for="dbTypeSelect" class="form-label fw-bold fs-5">Выберите базу данных:</label>
        <select class="form-select form-select-lg" id="dbTypeSelect" onchange="switchDatabase(this.value)" style="border: 2px solid #2c5aa0; font-weight: 500;">
            <option value="zakupki" selected>1. Закупки</option>
            <option value="companies">2. Предприятия России</option>
        </select>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-md-12">
        <h4>За период</h4>
        <form method="GET" action="{{ route('zakupki.index') }}" class="row g-3 align-items-end">
            <input type="hidden" name="per_page" value="{{ $per_page }}">
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_from" value="{{ $date_from }}">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_to" value="{{ $date_to }}">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" name="search_text"
                       placeholder="Товар/услуга" value="{{ $search_text }}">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    Поиск
                </button>
            </div>
            <div class="col-md-1">
                <a href="{{ route('zakupki.index') }}" class="btn btn-secondary w-100" title="Сбросить фильтр">
                    Сброс
                </a>
            </div>
            @auth
            <div class="col-md-2">
                <a href="{{ route('zakupki.export', request()->query()) }}" class="btn btn-success w-100">
                    Экспорт
                </a>
            </div>
            @endauth
        </form>
    </div>
</div>

<!-- Table -->
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Дата запроса</th>
                        <th>Товар/услуга</th>
                        <th>Цена контракта</th>
                        <th>Покупатель</th>
                        <th>Email</th>
                        <th>Телефон</th>
                        <th>Адрес</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($zakupki as $item)
                    <tr style="cursor: pointer;" onclick="window.location='{{ route('zakupki.show', ['id' => $item['id'], 'date_from' => $date_from, 'date_to' => $date_to, 'search_text' => $search_text, 'page' => $page, 'per_page' => $per_page]) }}'">
                        <td>{{ isset($item['date_request']) ? (is_object($item['date_request']) ? $item['date_request']->format('d.m.Y') : date('d.m.Y', strtotime($item['date_request']))) : '' }}</td>
                        <td>{{ $item['purchase_object'] ?? '' }}</td>
                        <td>{{ $item['start_cost_var'] ?? ($item['start_cost'] ? number_format($item['start_cost'], 2) : '') }}</td>
                        <td>{{ $item['customer'] ?? '' }}</td>
                        <td>{{ $item['email'] ?? '' }}</td>
                        <td>{{ $item['phone'] ?? '' }}</td>
                        <td>{{ $item['address'] ?? '' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Нет данных</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @php
            $total_pages = ceil($total / $per_page);
            $start_record = ($page - 1) * $per_page + 1;
            $end_record = min($page * $per_page, $total);
        @endphp

        <div class="row mt-3 align-items-center">
            <div class="col-md-6 mb-2 mb-md-0">
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <div>
                        <strong>Всего записей:</strong> {{ number_format($total, 0, '.', ' ') }}
                    </div>
                    <div class="text-muted">
                        Показаны {{ number_format($start_record, 0, '.', ' ') }}–{{ number_format($end_record, 0, '.', ' ') }}
                    </div>
                    <div class="d-flex align-items-center">
                        <label for="perPageSelect" class="me-2 text-nowrap mb-0">На странице:</label>
                        <select id="perPageSelect" class="form-select form-select-sm" onchange="changePerPage(this.value)" style="width: 80px;">
                            <option value="20" {{ $per_page == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ $per_page == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $per_page == 100 ? 'selected' : '' }}>100</option>
                            <option value="500" {{ $per_page == 500 ? 'selected' : '' }}>500</option>
                        </select>
                    </div>
                </div>
            </div>

            @if($total > $per_page)
            <div class="col-md-6">
                <nav>
                    <ul class="pagination justify-content-md-end justify-content-center mb-0">
                        <!-- Previous -->
                        @if($page > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ route('zakupki.index', array_merge(request()->query(), ['page' => $page - 1])) }}">&laquo;</a>
                            </li>
                        @else
                            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                        @endif

                        <!-- Pages -->
                        @for($p = 1; $p <= $total_pages; $p++)
                            @if($p == $page)
                                <li class="page-item active"><span class="page-link">{{ $p }}</span></li>
                            @elseif($p == 1 || $p == $total_pages || ($p >= $page - 2 && $p <= $page + 2))
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('zakupki.index', array_merge(request()->query(), ['page' => $p])) }}">{{ $p }}</a>
                                </li>
                            @elseif($p == $page - 3 || $p == $page + 3)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endfor

                        <!-- Next -->
                        @if($page < $total_pages)
                            <li class="page-item">
                                <a class="page-link" href="{{ route('zakupki.index', array_merge(request()->query(), ['page' => $page + 1])) }}">&raquo;</a>
                            </li>
                        @else
                            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                        @endif
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchDatabase(dbType) {
    if (dbType === 'companies') {
        window.location.href = '{{ route('companies.index') }}';
    }
}

function changePerPage(newPerPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', newPerPage);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}
</script>
@endpush

</x-app-layout>
