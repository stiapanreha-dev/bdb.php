<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>SQL Запросы</h2>
        <p class="text-muted">Инструмент для выполнения SELECT запросов к базам данных</p>
    </div>
</div>

@if(isset($results))
<div class="card mb-3">
    <div class="card-header bg-success text-white">
        <strong>Результат выполнения</strong>
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-6">
                <strong>База данных:</strong> {{ $connection }}
            </div>
            <div class="col-md-6 text-end">
                <strong>Время выполнения:</strong> {{ $executionTime }} мс
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <strong>Запрос:</strong>
                <pre class="bg-light p-2 rounded"><code>{{ $query }}</code></pre>
            </div>
        </div>
        @if($limitWarning)
        <div class="alert alert-warning">
            {{ $limitWarning }}
        </div>
        @endif
        <div class="mb-2">
            <strong>Найдено строк:</strong> {{ $rowCount }}
        </div>
        @if(count($results) > 0)
        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
            <table class="table table-sm table-bordered table-striped">
                <thead class="sticky-top bg-white">
                    <tr>
                        @foreach(array_keys($results[0]) as $column)
                        <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $row)
                    <tr>
                        @foreach($row as $value)
                        <td>{{ is_null($value) ? 'NULL' : $value }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-info">
            Запрос выполнен успешно, но не вернул результатов
        </div>
        @endif
    </div>
</div>
@endif

<div class="card">
    <div class="card-header">
        <strong>Выполнить запрос</strong>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.sql.execute') }}">
            @csrf
            <div class="mb-3">
                <label for="connection" class="form-label">База данных</label>
                <select class="form-select" id="connection" name="connection" required>
                    <option value="pgsql" {{ (old('connection', $connection ?? '') === 'pgsql') ? 'selected' : '' }}>
                        PostgreSQL (users, news, ideas, transactions)
                    </option>
                    <option value="mssql" {{ (old('connection', $connection ?? '') === 'mssql') ? 'selected' : '' }}>
                        MSSQL - business2025 (current, companies + zakupki)
                    </option>
                    <option value="mssql_2026" {{ (old('connection', $connection ?? '') === 'mssql_2026') ? 'selected' : '' }}>
                        MSSQL - business2026 (zakupki)
                    </option>
                    <option value="mssql_2024" {{ (old('connection', $connection ?? '') === 'mssql_2024') ? 'selected' : '' }}>
                        MSSQL - business2024 (zakupki)
                    </option>
                    <option value="mssql_2023" {{ (old('connection', $connection ?? '') === 'mssql_2023') ? 'selected' : '' }}>
                        MSSQL - business2023 (zakupki)
                    </option>
                    <option value="mssql_2022" {{ (old('connection', $connection ?? '') === 'mssql_2022') ? 'selected' : '' }}>
                        MSSQL - business2022 (zakupki)
                    </option>
                    <option value="mssql_2021" {{ (old('connection', $connection ?? '') === 'mssql_2021') ? 'selected' : '' }}>
                        MSSQL - business2021 (zakupki)
                    </option>
                    <option value="mssql_2020" {{ (old('connection', $connection ?? '') === 'mssql_2020') ? 'selected' : '' }}>
                        MSSQL - business2020 (zakupki)
                    </option>
                    <option value="mssql_cp1251" {{ (old('connection', $connection ?? '') === 'mssql_cp1251') ? 'selected' : '' }}>
                        MSSQL CP1251 (для VARCHAR полей на русском)
                    </option>
                </select>
                <small class="form-text text-muted">
                    Выберите подключение к базе данных
                </small>
            </div>

            <div class="mb-3">
                <label for="query" class="form-label">SQL запрос</label>
                <textarea
                    class="form-control font-monospace"
                    id="query"
                    name="query"
                    rows="12"
                    required
                    placeholder="SELECT * FROM table_name LIMIT 10"
                >{{ old('query', $query ?? '') }}</textarea>
                <small class="form-text text-muted">
                    Разрешены только SELECT запросы. Максимум 1000 строк результата.
                </small>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-play-fill"></i> Выполнить
            </button>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('query').value = '';">
                <i class="bi bi-x-circle"></i> Очистить
            </button>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <strong>Полезные запросы</strong>
    </div>
    <div class="card-body">
        <div class="accordion" id="queryExamples">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#example1">
                        PostgreSQL - Пользователи с балансом
                    </button>
                </h2>
                <div id="example1" class="accordion-collapse collapse" data-bs-parent="#queryExamples">
                    <div class="accordion-body">
                        <pre class="bg-light p-2 rounded mb-0"><code>SELECT id, name, email, balance, role, created_at
FROM users
WHERE balance > 0
ORDER BY balance DESC
LIMIT 20;</code></pre>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#example2">
                        MSSQL - Последние закупки
                    </button>
                </h2>
                <div id="example2" class="accordion-collapse collapse" data-bs-parent="#queryExamples">
                    <div class="accordion-body">
                        <pre class="bg-light p-2 rounded mb-0"><code>SELECT TOP 20
    id,
    created,
    purchase_object,
    customer,
    start_cost
FROM zakupki
ORDER BY id DESC;</code></pre>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#example3">
                        MSSQL - Компании (только business2025)
                    </button>
                </h2>
                <div id="example3" class="accordion-collapse collapse" data-bs-parent="#queryExamples">
                    <div class="accordion-body">
                        <pre class="bg-light p-2 rounded mb-0"><code>SELECT TOP 20
    id,
    name,
    city,
    phone,
    email,
    site
FROM db_companies
ORDER BY id DESC;</code></pre>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#example4">
                        MSSQL - Проверка индексов
                    </button>
                </h2>
                <div id="example4" class="accordion-collapse collapse" data-bs-parent="#queryExamples">
                    <div class="accordion-body">
                        <pre class="bg-light p-2 rounded mb-0"><code>SELECT
    i.name AS IndexName,
    i.type_desc AS IndexType,
    c.name AS ColumnName
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE i.object_id = OBJECT_ID('zakupki')
ORDER BY i.name, ic.index_column_id;</code></pre>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#example5">
                        MSSQL - Количество записей по годам
                    </button>
                </h2>
                <div id="example5" class="accordion-collapse collapse" data-bs-parent="#queryExamples">
                    <div class="accordion-body">
                        <pre class="bg-light p-2 rounded mb-0"><code>SELECT COUNT(*) as total_records
FROM zakupki;</code></pre>
                        <small class="text-muted">Выполните этот запрос для каждой базы business2020-2026</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-app-layout>
