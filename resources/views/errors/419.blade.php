<x-app-layout>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-warning">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <svg width="80" height="80" fill="orange" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                </div>

                <h2 class="mb-3">Сессия истекла</h2>
                <p class="text-muted mb-4">
                    Ваша сессия устарела из-за длительного бездействия.<br>
                    Для безопасности нужно обновить страницу и повторить действие.
                </p>

                <div class="d-flex flex-column gap-2">
                    <button onclick="window.location.reload()" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise"></i> Обновить страницу
                    </button>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-house"></i> На главную
                    </a>
                </div>

                <div class="alert alert-info mt-4 text-start">
                    <strong>Почему это произошло?</strong>
                    <ul class="mb-0 mt-2">
                        <li>Вы долго не обновляли страницу (более 2 часов)</li>
                        <li>Открыли сайт в новой вкладке после закрытия старой</li>
                        <li>Были проблемы с cookies в браузере</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
