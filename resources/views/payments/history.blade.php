<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>История платежей</h2>
    </div>
</div>

@if($payments->isEmpty())
<div class="alert alert-info">
    У вас пока нет платежей. <a href="{{ route('subscriptions.index') }}" class="alert-link">Пополнить баланс</a>
</div>
@else
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                        <th>Способ оплаты</th>
                        <th>Описание</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                        <td>{{ number_format($payment->amount, 2, '.', ' ') }} {{ $payment->currency }}</td>
                        <td>
                            @if($payment->status === 'succeeded')
                                <span class="badge bg-success">Оплачен</span>
                            @elseif($payment->status === 'pending')
                                <span class="badge bg-warning">Ожидание</span>
                            @elseif($payment->status === 'canceled')
                                <span class="badge bg-danger">Отменен</span>
                            @else
                                <span class="badge bg-secondary">{{ $payment->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->payment_method)
                                {{ ucfirst($payment->payment_method) }}
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $payment->description ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $payments->links() }}
    </div>
</div>
@endif

<div class="mt-3">
    <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-secondary">
        Вернуться к тарифам
    </a>
</div>
</x-app-layout>
