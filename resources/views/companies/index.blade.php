<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Предприятия России
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($show_masked_email || $show_masked_phone)
            <div class="alert alert-info mb-4">
                <strong>Обратите внимание:</strong> Email адреса и телефоны частично скрыты.
                <a href="#" class="alert-link" data-bs-toggle="modal" data-bs-target="#balanceModal">Пополните баланс</a> для полного доступа.
            </div>
            @endif

            <!-- Database switcher -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="dbTypeSelect" class="form-label fw-bold fs-5">Выберите базу данных:</label>
                    <select class="form-select form-select-lg" id="dbTypeSelect" onchange="switchDatabase(this.value)"
                            style="border: 2px solid #2c5aa0; font-weight: 500;">
                        <option value="zakupki">1. Закупки</option>
                        <option value="companies" selected>2. Предприятия России</option>
                    </select>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Фильтр</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('companies.index') }}" class="row g-3 align-items-end">
                        <input type="hidden" name="per_page" value="{{ $per_page }}">

                        <div class="col-md-3">
                            <label for="rubricSelect" class="form-label">Рубрика</label>
                            <select class="form-select" id="rubricSelect" name="id_rubric" onchange="this.form.submit()">
                                <option value="">Все рубрики</option>
                                @foreach($rubrics as $rubric)
                                <option value="{{ $rubric['id'] }}" {{ $id_rubric == $rubric['id'] ? 'selected' : '' }}>
                                    {{ $rubric['rubric'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="subrubricSelect" class="form-label">Подрубрика</label>
                            <select class="form-select" id="subrubricSelect" name="id_subrubric">
                                <option value="">Все подрубрики</option>
                                @foreach($subrubrics as $subrubric)
                                <option value="{{ $subrubric['id'] }}" {{ $id_subrubric == $subrubric['id'] ? 'selected' : '' }}>
                                    {{ $subrubric['subrubric'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="citySelect" class="form-label">Город</label>
                            <select class="form-select" id="citySelect" name="id_city">
                                <option value="">Все города</option>
                                @foreach($cities as $city)
                                <option value="{{ $city['id'] }}" {{ $id_city == $city['id'] ? 'selected' : '' }}>
                                    {{ $city['city'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="searchText" class="form-label">Поиск</label>
                            <input type="text" class="form-control" id="searchText" name="search_text"
                                   placeholder="Компания, ИНН, директор" value="{{ $search_text }}">
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Применить фильтр</button>
                            <a href="{{ route('companies.index') }}" class="btn btn-secondary">Сбросить</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Companies table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Компания</th>
                                    <th>Рубрика</th>
                                    <th>Подрубрика</th>
                                    <th>Город</th>
                                    <th>Телефон</th>
                                    <th>Email</th>
                                    <th>Сайт</th>
                                    <th>ИНН</th>
                                    <th>Директор</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($companies as $company)
                                <tr style="cursor: pointer;"
                                    onclick="window.location='{{ route('companies.show', ['id' => $company['id'], 'id_rubric' => $id_rubric, 'id_subrubric' => $id_subrubric, 'id_city' => $id_city, 'search_text' => $search_text, 'page' => $page, 'per_page' => $per_page]) }}'">
                                    <td>{{ $company['company'] ?? '-' }}</td>
                                    <td>{{ $company['rubric'] ?? '-' }}</td>
                                    <td>{{ $company['subrubric'] ?? '-' }}</td>
                                    <td>{{ $company['city'] ?? '-' }}</td>
                                    <td>{{ $company['phone'] ?? $company['mobile_phone'] ?? '-' }}</td>
                                    <td>{{ $company['Email'] ?? '-' }}</td>
                                    <td>{{ $company['site'] ?? '-' }}</td>
                                    <td>{{ $company['inn'] ?? '-' }}</td>
                                    <td>{{ $company['director'] ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Нет данных</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @php
                        $totalPages = (int) ceil($total / $per_page);
                        $startRecord = ($page - 1) * $per_page + 1;
                        $endRecord = min($page * $per_page, $total);
                    @endphp

                    <div class="row mt-3 align-items-center">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="d-flex align-items-center flex-wrap gap-3">
                                <div>
                                    <strong>Всего записей:</strong> {{ number_format($total, 0, '.', ' ') }}
                                </div>
                                <div class="text-muted">
                                    Показаны {{ number_format($startRecord, 0, '.', ' ') }}–{{ number_format($endRecord, 0, '.', ' ') }}
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
                                <ul class="pagination justify-content-md-end justify-content-center mb-0 flex-wrap">
                                    {{-- First page --}}
                                    @if($page > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ route('companies.index', ['page' => 1, 'id_rubric' => $id_rubric, 'id_subrubric' => $id_subrubric, 'id_city' => $id_city, 'search_text' => $search_text, 'per_page' => $per_page]) }}" title="Первая страница">
                                            &laquo;&laquo;
                                        </a>
                                    </li>
                                    @else
                                    <li class="page-item disabled">
                                        <span class="page-link">&laquo;&laquo;</span>
                                    </li>
                                    @endif

                                    {{-- Previous page --}}
                                    @if($page > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ route('companies.index', ['page' => $page - 1, 'id_rubric' => $id_rubric, 'id_subrubric' => $id_subrubric, 'id_city' => $id_city, 'search_text' => $search_text, 'per_page' => $per_page]) }}">
                                            &laquo;
                                        </a>
                                    </li>
                                    @else
                                    <li class="page-item disabled">
                                        <span class="page-link">&laquo;</span>
                                    </li>
                                    @endif

                                    {{-- Page numbers --}}
                                    @for($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++)
                                        @if($p == $page)
                                        <li class="page-item active">
                                            <span class="page-link">{{ $p }}</span>
                                        </li>
                                        @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ route('companies.index', ['page' => $p, 'id_rubric' => $id_rubric, 'id_subrubric' => $id_subrubric, 'id_city' => $id_city, 'search_text' => $search_text, 'per_page' => $per_page]) }}">
                                                {{ $p }}
                                            </a>
                                        </li>
                                        @endif
                                    @endfor

                                    {{-- Next page --}}
                                    @if($page < $totalPages)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ route('companies.index', ['page' => $page + 1, 'id_rubric' => $id_rubric, 'id_subrubric' => $id_subrubric, 'id_city' => $id_city, 'search_text' => $search_text, 'per_page' => $per_page]) }}">
                                            &raquo;
                                        </a>
                                    </li>
                                    @else
                                    <li class="page-item disabled">
                                        <span class="page-link">&raquo;</span>
                                    </li>
                                    @endif

                                    {{-- Last page --}}
                                    @if($page < $totalPages)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ route('companies.index', ['page' => $totalPages, 'id_rubric' => $id_rubric, 'id_subrubric' => $id_subrubric, 'id_city' => $id_city, 'search_text' => $search_text, 'per_page' => $per_page]) }}" title="Последняя страница">
                                            &raquo;&raquo;
                                        </a>
                                    </li>
                                    @else
                                    <li class="page-item disabled">
                                        <span class="page-link">&raquo;&raquo;</span>
                                    </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function switchDatabase(dbType) {
        if (dbType === 'zakupki') {
            window.location.href = '{{ route('zakupki.index') }}';
        }
    }

    function changePerPage(newPerPage) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', newPerPage);
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    }
    </script>
</x-app-layout>
