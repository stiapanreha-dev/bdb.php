<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ Str::limit($company['company'], 50) }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Back button -->
            <div class="mb-3">
                <a href="{{ route('companies.index', ['id_rubric' => $id_rubric, 'id_subrubric' => $id_subrubric, 'id_city' => $id_city, 'search_text' => $search_text, 'page' => $page, 'per_page' => $per_page]) }}"
                   class="btn btn-outline-secondary btn-sm">
                    <svg width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16" style="display: inline-block; vertical-align: middle;">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                    </svg>
                    Назад к списку
                </a>
            </div>

            <!-- Main info card -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Информация о предприятии</h4>
                </div>
                <div class="card-body">
                    <!-- Company name -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5>Название предприятия</h5>
                            <p class="lead">{{ $company['company'] }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Left column -->
                        <div class="col-md-6">
                            <h6>Основная информация</h6>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width: 40%;">Рубрика:</th>
                                        <td>{{ $company['rubric'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Подрубрика:</th>
                                        <td>{{ $company['subrubric'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Город:</th>
                                        <td>{{ $company['city'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Директор:</th>
                                        <td>{{ $company['director'] ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Right column -->
                        <div class="col-md-6">
                            <h6>Контактная информация</h6>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width: 40%;">Телефон:</th>
                                        <td>
                                            @if($company['phone'])
                                                @if($show_masked_phone)
                                                    {{ $company['phone'] }}
                                                    <small class="text-muted">(частично скрыт)</small>
                                                @else
                                                    <a href="tel:{{ $company['phone'] }}">{{ $company['phone'] }}</a>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Мобильный:</th>
                                        <td>
                                            @if($company['mobile_phone'])
                                                @if($show_masked_phone)
                                                    {{ $company['mobile_phone'] }}
                                                    <small class="text-muted">(частично скрыт)</small>
                                                @else
                                                    <a href="tel:{{ $company['mobile_phone'] }}">{{ $company['mobile_phone'] }}</a>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>
                                            @if($company['Email'])
                                                @if($show_masked_email)
                                                    {{ $company['Email'] }}
                                                    <small class="text-muted">(частично скрыт)</small>
                                                @else
                                                    <a href="mailto:{{ $company['Email'] }}">{{ $company['Email'] }}</a>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Сайт:</th>
                                        <td>
                                            @if($company['site'])
                                                @if($show_masked_email)
                                                    {{ $company['site'] }}
                                                    <small class="text-muted">(частично скрыт)</small>
                                                @else
                                                    <a href="http://{{ $company['site'] }}" target="_blank" rel="noopener noreferrer">{{ $company['site'] }}</a>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h6>Реквизиты</h6>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width: 20%;">ИНН:</th>
                                        <td><code>{{ $company['inn'] ?? '-' }}</code></td>
                                    </tr>
                                    <tr>
                                        <th>ОГРН:</th>
                                        <td><code>{{ $company['ogrn'] ?? '-' }}</code></td>
                                    </tr>
                                    <tr>
                                        <th>ID компании:</th>
                                        <td><code class="fs-5">#{{ $company['id'] }}</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Warning for masked data -->
                    @if($show_masked_email || $show_masked_phone)
                    <div class="alert alert-info mt-3">
                        <strong>Обратите внимание:</strong> Контактные данные частично скрыты.
                        @guest
                            <a href="{{ route('login') }}" class="alert-link">Войдите</a> и
                        @endguest
                        <a href="#" class="alert-link" data-bs-toggle="modal" data-bs-target="#balanceModal">пополните баланс</a> для полного доступа.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('companies.index', ['id_rubric' => $id_rubric, 'id_subrubric' => $id_subrubric, 'id_city' => $id_city, 'search_text' => $search_text, 'page' => $page, 'per_page' => $per_page]) }}"
                   class="btn btn-secondary">
                    Вернуться к списку
                </a>
                @auth
                <button class="btn btn-outline-primary" onclick="window.print()">
                    <svg width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16" style="display: inline-block; vertical-align: middle;">
                        <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                        <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                    </svg>
                    Печать
                </button>
                @endauth
            </div>
        </div>
    </div>

    <style>
        @media print {
            .btn, nav, footer, .alert {
                display: none !important;
            }
        }
    </style>
</x-app-layout>
