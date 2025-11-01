<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>#?@02;5=85 ?;0B560<8 .Kassa</h2>
        <p class="text-muted">AB>@8O ?;0B5659 8 B@0=70:F89 ?>;L7>20B5;59</p>
    </div>
</div>

<!-- !B0B8AB8:0 -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-success">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">#A?5H=K5 ?;0B568</h6>
                <h3 class="card-title mb-0">{{ number_format($stats['total_amount'], 2, '.', ' ') }} ½</h3>
                <small class="text-white-50">A53>: {{ $stats['total_count'] }} HB.</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50"> >6840=88</h6>
                <h3 class="card-title mb-0">{{ $stats['pending_count'] }}</h3>
                <small class="text-white-50">?;0B5659</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-secondary">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">B<5=5=></h6>
                <h3 class="card-title mb-0">{{ $stats['canceled_count'] }}</h3>
                <small class="text-white-50">?;0B5659</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">A53> ?;0B5659</h6>
                <h3 class="card-title mb-0">{{ $stats['total_count'] + $stats['pending_count'] + $stats['canceled_count'] }}</h3>
                <small class="text-white-50">70 2AQ 2@5<O</small>
            </div>
        </div>
    </div>
</div>

<!-- $8;LB@K -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.payments') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">!B0BCA</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">A5 AB0BCAK</option>
                        <option value="succeeded" {{ request('status') === 'succeeded' ? 'selected' : '' }}>#A?5H=K5</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}> >6840=88</option>
                        <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>B<5=Q==K5</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="user_search" class="form-label">>;L7>20B5;L</label>
                    <input type="text"
                           name="user_search"
                           id="user_search"
                           class="form-control"
                           placeholder="<O 8;8 email"
                           value="{{ request('user_search') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">0B0 >B</label>
                    <input type="date"
                           name="date_from"
                           id="date_from"
                           class="form-control"
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">0B0 4></label>
                    <input type="date"
                           name="date_to"
                           id="date_to"
                           class="form-control"
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">@8<5=8BL</button>
                    <a href="{{ route('admin.payments') }}" class="btn btn-secondary">!1@>A8BL</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- "01;8F0 ?;0B5659 -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>0B0 A>740=8O</th>
                        <th>>;L7>20B5;L</th>
                        <th>!C<<0</th>
                        <th>!B0BCA</th>
                        <th>!?>A>1 >?;0BK</th>
                        <th>0B0 >?;0BK</th>
                        <th>ID .Kassa</th>
                        <th>?8A0=85</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <strong>{{ $payment->user->name }}</strong><br>
                            <small class="text-muted">{{ $payment->user->email }}</small>
                        </td>
                        <td>
                            <strong>{{ number_format($payment->amount, 2, '.', ' ') }} {{ $payment->currency }}</strong>
                        </td>
                        <td>
                            @if($payment->status === 'succeeded')
                                <span class="badge bg-success">?;0G5=></span>
                            @elseif($payment->status === 'pending')
                                <span class="badge bg-warning">6840=85</span>
                            @elseif($payment->status === 'canceled')
                                <span class="badge bg-secondary">B<5=5=></span>
                            @else
                                <span class="badge bg-secondary">{{ $payment->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->payment_method)
                                <span class="badge bg-info">{{ $payment->payment_method }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->paid_at)
                                {{ $payment->paid_at->format('d.m.Y H:i') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <code class="small">{{ Str::limit($payment->yookassa_payment_id, 20) }}</code>
                        </td>
                        <td>
                            @if($payment->description)
                                {{ Str::limit($payment->description, 30) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <p class="text-muted mb-0">;0B5659 =5 =0945=></p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $payments->links() }}
        </div>
    </div>
</div>

<!-- >?>;=8B5;L=0O 8=D>@<0F8O -->
<div class="card mt-3">
    <div class="card-body">
        <h5 class="mb-3"> ?;0B560E</h5>
        <ul class="mb-0">
            <li>;0B568 >1@010BK20NBAO G5@57 ?;0BQ6=CN A8AB5<C <strong>.Kassa</strong></li>
            <li>>A;5 CA?5H=>9 >?;0BK 10;0=A ?>;L7>20B5;O 02B><0B8G5A:8 ?>?>;=O5BAO</li>
            <li>Webhook >1@010BK205B C254><;5=8O >B .Kassa 2 @50;L=>< 2@5<5=8</li>
            <li>!B0BCA ?;0B560 >1=>2;O5BAO 02B><0B8G5A:8 ?@8 ?>;CG5=88 C254><;5=8O</li>
            <li>AB>@8O ?;0B5659 4>ABC?=0 ?>;L7>20B5;O< 2 @0745;5 <a href="{{ route('payment.history') }}">AB>@8O ?;0B5659</a></li>
        </ul>
    </div>
</div>
</x-app-layout>
