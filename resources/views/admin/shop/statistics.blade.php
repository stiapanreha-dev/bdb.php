<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>Статистика продаж магазина</h2>
    </div>
</div>

{{-- Stats Cards --}}
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-bg-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-title opacity-75">Выручка</h6>
                        <h2 class="mb-0">{{ number_format($totalRevenue, 0, '.', ' ') }} ₽</h2>
                    </div>
                    <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-title opacity-75">Продаж</h6>
                        <h2 class="mb-0">{{ number_format($totalSales, 0, '.', ' ') }}</h2>
                    </div>
                    <i class="bi bi-cart-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-bg-info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-title opacity-75">Средний чек</h6>
                        <h2 class="mb-0">{{ number_format($averageCheck, 0, '.', ' ') }} ₽</h2>
                    </div>
                    <i class="bi bi-receipt fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-bg-warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-title opacity-75">Конверсия</h6>
                        <h2 class="mb-0">{{ number_format($conversionRate, 2) }}%</h2>
                        <small class="opacity-75">{{ number_format($totalViews, 0, '.', ' ') }} просмотров</small>
                    </div>
                    <i class="bi bi-graph-up fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Sales Chart --}}
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up me-2"></i>Продажи за последние 30 дней
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Top Products --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-trophy me-2"></i>Топ-10 товаров по продажам
            </div>
            <div class="card-body">
                @if($topProducts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Товар</th>
                                <th class="text-end">Продаж</th>
                                <th class="text-end">Выручка</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $index => $product)
                            <tr>
                                <td>
                                    @if($index < 3)
                                        <span class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'danger') }}">{{ $index + 1 }}</span>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.shop.products.show', $product) }}" class="text-decoration-none">
                                        {{ Str::limit($product->name, 30) }}
                                    </a>
                                </td>
                                <td class="text-end">{{ $product->purchases_count }}</td>
                                <td class="text-end text-success">{{ number_format($product->purchases_sum_price ?? 0, 0, '.', ' ') }} ₽</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">Продаж пока нет</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Best Conversion --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-bullseye me-2"></i>Топ-10 по конверсии
            </div>
            <div class="card-body">
                @if($productsWithConversion->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Товар</th>
                                <th class="text-end">Просм.</th>
                                <th class="text-end">Покуп.</th>
                                <th class="text-end">Конв.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productsWithConversion as $index => $product)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.shop.products.show', $product) }}" class="text-decoration-none">
                                        {{ Str::limit($product->name, 25) }}
                                    </a>
                                </td>
                                <td class="text-end text-muted">{{ $product->views_count }}</td>
                                <td class="text-end">{{ $product->purchases_count }}</td>
                                <td class="text-end">
                                    <span class="badge bg-{{ $product->conversion_rate > 5 ? 'success' : ($product->conversion_rate > 1 ? 'warning' : 'secondary') }}">
                                        {{ $product->conversion_rate }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">Недостаточно данных</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Quick Links --}}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.shop.products.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-box me-1"></i>Все товары
                    </a>
                    <a href="{{ route('admin.shop.categories.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-folder me-1"></i>Категории
                    </a>
                    <a href="{{ route('admin.shop.purchases') }}" class="btn btn-outline-primary">
                        <i class="bi bi-cart-check me-1"></i>История покупок
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');

    // Prepare data for the last 30 days
    const salesData = @json($salesByDay);

    // Create labels for the last 30 days
    const labels = [];
    const revenueData = [];
    const countData = [];

    // Generate all dates for the last 30 days
    const today = new Date();
    for (let i = 29; i >= 0; i--) {
        const date = new Date(today);
        date.setDate(date.getDate() - i);
        const dateStr = date.toISOString().split('T')[0];
        labels.push(date.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit' }));

        // Find matching data
        const found = salesData.find(item => item.date === dateStr);
        revenueData.push(found ? parseFloat(found.revenue) : 0);
        countData.push(found ? parseInt(found.count) : 0);
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Выручка (₽)',
                    data: revenueData,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true,
                    tension: 0.3,
                    yAxisID: 'y'
                },
                {
                    label: 'Количество продаж',
                    data: countData,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: false,
                    tension: 0.3,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return 'Выручка: ' + context.parsed.y.toLocaleString('ru-RU') + ' ₽';
                            }
                            return 'Продаж: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Выручка (₽)'
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('ru-RU') + ' ₽';
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Количество'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
});
</script>
@endpush
</x-app-layout>
