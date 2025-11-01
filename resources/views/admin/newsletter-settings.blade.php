<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>0AB@>9:8 @0AAK;:8</h2>
        <p class="text-muted">#?@02;5=85 02B><0B8G5A:>9 @0AAK;:>9 =>2>AB59 8 ?@>4;5=85< ?>4?8A>:</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.newsletter-settings.update') }}">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">0AB@>9:8 >B?@02:8 @0AAK;:8</h5>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   role="switch"
                                   name="send_enabled"
                                   id="send_enabled"
                                   value="1"
                                   {{ old('send_enabled', $settings->where('key', 'send_enabled')->first()?->value ?? 'true') === 'true' ? 'checked' : '' }}>
                            <label class="form-check-label" for="send_enabled">
                                <strong>:;NG8BL 02B><0B8G5A:CN @0AAK;:C</strong>
                            </label>
                        </div>
                        <small class="text-muted">
                            A;8 2K:;NG5=>, @0AAK;:0 =5 1C45B >B?@02;OBLAO 02B><0B8G5A:8
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="send_interval_minutes" class="form-label">
                            <strong>=B5@20; >B?@02:8 (2 <8=CB0E)</strong>
                        </label>
                        <input type="number"
                               class="form-control"
                               name="send_interval_minutes"
                               id="send_interval_minutes"
                               value="{{ old('send_interval_minutes', $settings->where('key', 'send_interval_minutes')->first()?->value ?? '180') }}"
                               min="10"
                               max="1440"
                               required>
                        <small class="text-muted">
                            8=8<0;L=>5 2@5<O <564C @0AAK;:0<8 (10-1440 <8=CB). > C<>;G0=8N: 180 <8=CB (3 G0A0)
                        </small>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="mb-3">0AB@>9:8 ?@>4;5=8O ?>4?8A>:</h5>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   role="switch"
                                   name="renew_enabled"
                                   id="renew_enabled"
                                   value="1"
                                   {{ old('renew_enabled', $settings->where('key', 'renew_enabled')->first()?->value ?? 'true') === 'true' ? 'checked' : '' }}>
                            <label class="form-check-label" for="renew_enabled">
                                <strong>:;NG8BL 02B><0B8G5A:>5 ?@>4;5=85 ?>4?8A>:</strong>
                            </label>
                        </div>
                        <small class="text-muted">
                            A;8 2K:;NG5=>, ?>4?8A:8 =5 1C4CB ?@>4;520BLAO 02B><0B8G5A:8
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="renew_time" class="form-label">
                            <strong>@5<O ?@>4;5=8O ?>4?8A>: (UTC+3)</strong>
                        </label>
                        <input type="time"
                               class="form-control"
                               name="renew_time"
                               id="renew_time"
                               value="{{ old('renew_time', $settings->where('key', 'renew_time')->first()?->value ?? '00:00') }}"
                               required>
                        <small class="text-muted">
                            654=52=>5 2@5<O 4;O ?@>25@:8 8 ?@>4;5=8O 8AB5:H8E ?>4?8A>:. > C<>;G0=8N: 00:00
                        </small>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="alert alert-info" role="alert">
                <strong>0: MB> @01>B05B:</strong>
                <ul class="mb-0 mt-2">
                    <li>Cron 70?CA:05BAO :064K9 G0A (157>?0A=K9 107>2K9 8=B5@20;)</li>
                    <li>><0=40 @0AAK;:8 ?@>25@O5B =0AB@>9:8 8 2K?>;=O5BAO B>;L:> 5A;8 ?@>H;> 4>AB0B>G=> 2@5<5=8 A ?>A;54=59 >B?@02:8</li>
                    <li>><0=40 ?@>4;5=8O ?@>25@O5B =0AB@>9:8 8 2K?>;=O5BAO B>;L:> 2 C:070==K9 G0A</li>
                    <li>7<5=5=85 =0AB@>5: =5 B@51C5B ?5@570?CA:0 cron 8;8 A5@25@0</li>
                </ul>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                    0704 : ?0=5;8 04<8=8AB@0B>@0
                </a>
                <button type="submit" class="btn btn-primary">
                    !>E@0=8BL =0AB@>9:8
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h5 class="mb-3">"5:CI85 7=0G5=8O =0AB@>5:</h5>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>;NG</th>
                        <th>=0G5=85</th>
                        <th>"8?</th>
                        <th>?8A0=85</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($settings as $setting)
                    <tr>
                        <td><code>{{ $setting->key }}</code></td>
                        <td>
                            @if($setting->type === 'boolean')
                                <span class="badge {{ $setting->value === 'true' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $setting->value === 'true' ? ':;NG5=>' : 'K:;NG5=>' }}
                                </span>
                            @else
                                <strong>{{ $setting->value }}</strong>
                            @endif
                        </td>
                        <td><code>{{ $setting->type }}</code></td>
                        <td>{{ $setting->description }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
