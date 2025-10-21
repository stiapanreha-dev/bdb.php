<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>SQL Запросы</h2>
    </div>
</div>

<div class="alert alert-warning">
    <strong>В разработке</strong> - Данный функционал находится в разработке
</div>

<div class="card">
    <div class="card-body">
        <form>
            <div class="mb-3">
                <label for="query" class="form-label">SQL запрос</label>
                <textarea class="form-control" id="query" rows="10" disabled placeholder="SELECT * FROM ..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" disabled>Выполнить</button>
        </form>
    </div>
</div>
</x-app-layout>
