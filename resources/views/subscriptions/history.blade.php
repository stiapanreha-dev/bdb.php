<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>История подписок</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Тариф</th>
                        <th>Начало</th>
                        <th>Окончание</th>
                        <th>Стоимость</th>
                        <th>Статус</th>
                        <th>Оформлена</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                    <tr>
                        <td>{{ $subscription->tariff->name }}</td>
                        <td>{{ $subscription->starts_at->format('d.m.Y H:i') }}</td>
                        <td>{{ $subscription->expires_at->format('d.m.Y H:i') }}</td>
                        <td>{{ number_format($subscription->paid_amount, 2, '.', ' ') }} руб</td>
                        <td>
                            @if($subscription->isValid())
                                <span class="badge bg-success">Активна</span>
                            @elseif($subscription->starts_at->isFuture())
                                <span class="badge bg-info">Ожидает активации</span>
                            @else
                                <span class="badge bg-secondary">Истекла</span>
                            @endif
                        </td>
                        <td>{{ $subscription->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">У вас пока нет подписок</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $subscriptions->links() }}
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('subscriptions.index') }}" class="btn btn-primary">
        Выбрать тариф
    </a>
</div>
</x-app-layout>
