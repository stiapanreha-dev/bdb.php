<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>!B0B8AB8:0 @0AAK;>:</h2>
        <p class="text-muted">#?@02;5=85 8 <>=8B>@8=3 2A5E @0AAK;>: ?>;L7>20B5;59</p>
    </div>
</div>

<!-- 1I0O AB0B8AB8:0 -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">A53> @0AAK;>:</h6>
                <h3 class="card-title mb-0">{{ $stats['total_newsletters'] }}</h3>
                <small class="text-white-50">:B82=KE: {{ $stats['active_newsletters'] }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">B?@02;5=> A53>4=O</h6>
                <h3 class="card-title mb-0">{{ $stats['total_sent_today'] }}</h3>
                <small class="text-white-50">@0AAK;>:</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">A53> >B?@02;5=></h6>
                <h3 class="card-title mb-0">{{ $stats['total_logs'] }}</h3>
                <small class="text-white-50">0:C?>:: {{ number_format($stats['total_zakupki_sent']) }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">AB5:H85 ?>4?8A:8</h6>
                <h3 class="card-title mb-0">{{ $stats['expired_subscriptions'] }}</h3>
                <small class="text-white-50">H81>:: {{ $stats['failed_logs'] }}</small>
            </div>
        </div>
    </div>
</div>

<!-- $8;LB@K -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.newsletters') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">!B0BCA @0AAK;:8</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">A5 @0AAK;:8</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>:B82=K5</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>50:B82=K5</option>
                        <option value="valid" {{ request('status') === 'valid' ? 'selected' : '' }}>! 459AB2CNI59 ?>4?8A:>9</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>! 8AB5:H59 ?>4?8A:>9</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="user_search" class="form-label">>;L7>20B5;L</label>
                    <input type="text"
                           name="user_search"
                           id="user_search"
                           class="form-control"
                           placeholder="<O 8;8 email"
                           value="{{ request('user_search') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">@8<5=8BL</button>
                    <a href="{{ route('admin.newsletters') }}" class="btn btn-secondary">!1@>A8BL</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- "01;8F0 @0AAK;>: -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-3">!?8A>: @0AAK;>:</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>>;L7>20B5;L</th>
                        <th>Email @0AAK;:8</th>
                        <th>!B0BCA</th>
                        <th>>4?8A:0 4></th>
                        <th>;NG52K5 A;>20</th>
                        <th>>A;54=OO >B?@02:0</th>
                        <th>!>740=0</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($newsletters as $newsletter)
                    <tr>
                        <td>{{ $newsletter->id }}</td>
                        <td>
                            <strong>{{ $newsletter->user->name }}</strong><br>
                            <small class="text-muted">{{ $newsletter->user->email }}</small>
                        </td>
                        <td>
                            @if($newsletter->email)
                                <code>{{ $newsletter->email }}</code>
                            @else
                                <span class="text-muted">> C<>;G0=8N</span>
                            @endif
                        </td>
                        <td>
                            @if($newsletter->is_active)
                                <span class="badge bg-success">:B82=0</span>
                            @else
                                <span class="badge bg-secondary">50:B82=0</span>
                            @endif
                        </td>
                        <td>
                            @if($newsletter->subscription_ends_at)
                                @if($newsletter->subscription_ends_at->isPast())
                                    <span class="badge bg-danger">{{ $newsletter->subscription_ends_at->format('d.m.Y') }}</span>
                                @else
                                    <span class="badge bg-success">{{ $newsletter->subscription_ends_at->format('d.m.Y') }}</span>
                                @endif
                            @else
                                <span class="text-muted">5 C:070=0</span>
                            @endif
                        </td>
                        <td>
                            @if($newsletter->keywords->count() > 0)
                                <small>
                                    @foreach($newsletter->keywords->take(3) as $keyword)
                                        <span class="badge bg-info">{{ $keyword->keyword }}</span>
                                    @endforeach
                                    @if($newsletter->keywords->count() > 3)
                                        <span class="text-muted">+{{ $newsletter->keywords->count() - 3 }}</span>
                                    @endif
                                </small>
                            @else
                                <span class="text-muted">5B</span>
                            @endif
                        </td>
                        <td>
                            @if($newsletter->last_sent_at)
                                {{ $newsletter->last_sent_at->format('d.m.Y H:i') }}
                            @else
                                <span class="text-muted">5 >B?@02;O;0AL</span>
                            @endif
                        </td>
                        <td>{{ $newsletter->created_at->format('d.m.Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <p class="text-muted mb-0"> 0AAK;>: =5 =0945=></p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $newsletters->links() }}
        </div>
    </div>
</div>

<!-- >A;54=85 >B?@02:8 -->
<div class="card">
    <div class="card-body">
        <h5 class="mb-3">>A;54=85 >B?@02:8 (30 4=59)</h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>0B0 >B?@02:8</th>
                        <th>>;L7>20B5;L</th>
                        <th>Email</th>
                        <th>0:C?>: 2 ?8AL<5</th>
                        <th>!B0BCA</th>
                        <th>H81:0</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLogs as $log)
                    <tr>
                        <td>{{ $log->sent_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <strong>{{ $log->newsletter->user->name }}</strong>
                        </td>
                        <td>
                            <small>{{ $log->newsletter->getEmailAddress() }}</small>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $log->zakupki_count }}</span>
                        </td>
                        <td>
                            @if($log->status === 'sent')
                                <span class="badge bg-success">B?@02;5=></span>
                            @elseif($log->status === 'failed')
                                <span class="badge bg-danger">H81:0</span>
                            @else
                                <span class="badge bg-secondary">{{ $log->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($log->error_message)
                                <small class="text-danger">{{ Str::limit($log->error_message, 50) }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <p class="text-muted mb-0">5B >B?@02>: 70 ?>A;54=85 30 4=59</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
