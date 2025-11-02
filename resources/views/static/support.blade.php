<x-app-layout>
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Техническая поддержка</h2>
        <p class="text-muted">Мы всегда готовы помочь вам с вопросами и проблемами</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Связаться с нами</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6><i class="bi bi-telegram"></i> Telegram</h6>
                    <p class="mb-2">Самый быстрый способ связи:</p>
                    <a href="https://t.me/cdvks" class="btn btn-primary" target="_blank">
                        <i class="bi bi-telegram"></i> @cdvks
                    </a>
                </div>

                <div class="mb-4">
                    <h6><i class="bi bi-envelope"></i> Email</h6>
                    <p class="mb-2">Напишите нам на электронную почту:</p>
                    <a href="mailto:support@businessdb.ru" class="text-decoration-none">
                        support@businessdb.ru
                    </a>
                </div>

                <div class="mb-0">
                    <h6><i class="bi bi-clock"></i> Время работы</h6>
                    <p class="mb-0">
                        Понедельник - Пятница: 9:00 - 18:00 (МСК)<br>
                        Суббота, Воскресенье: выходной
                    </p>
                    <small class="text-muted">Среднее время ответа: до 2 часов в рабочее время</small>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Часто задаваемые вопросы</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Как получить доступ к полным данным?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Для доступа к полным данным о компаниях и закупках необходимо приобрести подписку. 
                                Перейдите на страницу <a href="{{ route('subscriptions.index') }}">Тарифы</a>, выберите подходящий тариф 
                                и пополните баланс.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Как пополнить баланс?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                После авторизации нажмите кнопку "Баланс" в верхнем меню, укажите сумму пополнения 
                                и следуйте инструкциям платежной системы.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Как экспортировать данные?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                После применения фильтров в разделе "Закупки" или "Компании" нажмите кнопку 
                                "Экспорт в Excel". Будет сформирован файл .xlsx со всеми отфильтрованными данными 
                                (максимум 10 000 записей).
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Как часто обновляются данные?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                База данных закупок обновляется ежедневно. Информация о компаниях актуализируется 
                                по мере поступления новых данных из официальных источников.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                Не могу найти нужные данные
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Попробуйте использовать разные варианты поиска (часть названия, ИНН, регион). 
                                Если данные все равно не находятся, свяжитесь с нами через Telegram или email - 
                                мы поможем найти нужную информацию.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Полезные ссылки</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <a href="{{ route('subscriptions.index') }}" class="text-decoration-none">
                            <i class="bi bi-credit-card"></i> Тарифы и подписки
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('profile.edit') }}" class="text-decoration-none">
                            <i class="bi bi-person"></i> Личный кабинет
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('privacy-policy') }}" class="text-decoration-none">
                            <i class="bi bi-shield-check"></i> Политика конфиденциальности
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('terms-of-service') }}" class="text-decoration-none">
                            <i class="bi bi-file-text"></i> Пользовательское соглашение
                        </a>
                    </li>
<!--
                    <li class="mb-0">
                        <a href="{{ route('offer') }}" class="text-decoration-none">
                            <i class="bi bi-file-earmark-text"></i> Публичная оферта
                        </a>
                    </li>
-->
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">О сервисе</h5>
            </div>
            <div class="card-body">
                <p class="small">
                    <strong>Business Database</strong> - это база данных компаний и государственных закупок России.
                </p>
                <p class="small mb-0">
                    Мы предоставляем актуальную информацию для поиска партнеров, клиентов и участия в тендерах.
                </p>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
