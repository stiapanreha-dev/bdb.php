@extends('layouts.app')

@section('title', 'Детали закупки - Business database')

@section('content')
            <!-- Back button -->
            <div class="mb-3">
                <a href="{{ route('zakupki.index', ['date_from' => $date_from, 'date_to' => $date_to, 'search_text' => $search_text, 'page' => $page, 'per_page' => $per_page]) }}"
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
                    <h4 class="mb-0">Информация о закупке</h4>
                </div>
                <div class="card-body">
                    <!-- Description -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5>Описание закупки</h5>
                            <p class="lead">{{ $zakupka['purchase_object'] }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Left column -->
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width: 40%;">Дата запроса:</th>
                                        <td>{{ $zakupka['date_request'] ? \Carbon\Carbon::parse($zakupka['date_request'])->format('d.m.Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Цена контракта:</th>
                                        <td>
                                            <strong class="text-success">
                                                @if($zakupka['start_cost_var'])
                                                    {{ $zakupka['start_cost_var'] }}
                                                @elseif($zakupka['start_cost'])
                                                    {{ number_format($zakupka['start_cost'], 2, '.', ',') }}
                                                @else
                                                    Не указана
                                                @endif
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>ID закупки:</th>
                                        <td><code class="fs-5">#{{ $zakupka['id'] }}</code></td>
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
                                        <th style="width: 40%;">Покупатель:</th>
                                        <td style="word-break: break-word; white-space: normal;">{{ $zakupka['customer'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>
                                            @if($zakupka['email'])
                                                @if($show_masked_email)
                                                    {{ $zakupka['email'] }}
                                                    <small class="text-muted">(частично скрыт)</small>
                                                @else
                                                    <a href="mailto:{{ $zakupka['email'] }}">{{ $zakupka['email'] }}</a>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Телефон:</th>
                                        <td>
                                            @if($zakupka['phone'])
                                                @if($show_masked_phone)
                                                    {{ $zakupka['phone'] }}
                                                    <small class="text-muted">(частично скрыт)</small>
                                                @else
                                                    <a href="tel:{{ $zakupka['phone'] }}">{{ $zakupka['phone'] }}</a>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Адрес:</th>
                                        <td style="word-break: break-word; white-space: normal;">{{ $zakupka['address'] ?? '-' }}</td>
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

            <!-- Specifications -->
            @if(!empty($specifications))
            <div class="card mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        Спецификация
                        <span class="badge bg-light text-dark">{{ count($specifications) }} поз.</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Товар/услуга</th>
                                    <th>Спецификация</th>
                                    <th>Количество</th>
                                    <th>Цена с НДС</th>
                                    <th>Условия оплаты</th>
                                    <th>Срок поставки</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($specifications as $index => $spec)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $spec['product'] ?? '-' }}</td>
                                    <td>{{ $spec['product_specification'] ?? '-' }}</td>
                                    <td>{{ $spec['quantity'] ?? '-' }}</td>
                                    <td>
                                        @if(isset($spec['price_vat']) && $spec['price_vat'])
                                            {{ number_format($spec['price_vat'], 2, '.', ',') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $spec['terms_of_payment'] ?? '-' }}</td>
                                    <td>{{ $spec['delivery_time'] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-secondary mt-4">
                <strong>Спецификация:</strong> Для данной закупки спецификация отсутствует.
            </div>
            @endif

            <!-- Actions -->
            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('zakupki.index', ['date_from' => $date_from, 'date_to' => $date_to, 'search_text' => $search_text, 'page' => $page, 'per_page' => $per_page]) }}"
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

<style>
    @media print {
        .btn, nav, footer, .alert {
            display: none !important;
        }
    }
</style>
@endsection
