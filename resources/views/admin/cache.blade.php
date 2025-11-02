<x-app-layout>
<div class="row">
    <div class="col-md-12">
        <h2>#?@02;5=85 :5H5<</h2>

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

        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> =D>@<0F8O
            </div>
            <div class="card-body">
                <p>0 MB>9 AB@0=8F5 2K <>65B5 >G8AB8BL @07;8G=K5 B8?K :5H0 Laravel.</p>
                <ul>
                    <li><strong>5AL :5H</strong> - >G8I05B 2A5 B8?K :5H0 (config, route, view, cache, opcache)</li>
                    <li><strong>>=D83C@0F8O</strong> - >G8I05B :5H D09;>2 :>=D83C@0F88 (.env, config/*.php)</li>
                    <li><strong>0@H@CBK</strong> - >G8I05B :5H <0@H@CB>2 (routes/*.php)</li>
                    <li><strong>@54AB02;5=8O</strong> - >G8I05B A:><?8;8@>20==K5 Blade H01;>=K (resources/views/*.blade.php)</li>
                    <li><strong>@8;>65=85</strong> - >G8I05B :5H 40==KE ?@8;>65=8O (cache facade)</li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h4 class="mb-3">G8AB:0 :5H0</h4>
            </div>
        </div>

        <div class="row g-3">
            <!-- G8AB8BL 25AL :5H -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-arrow-clockwise text-danger"></i> 5AL :5H
                        </h5>
                        <p class="card-text text-muted">
                            G8AB8BL 2A5 B8?K :5H0 >4=>2@5<5==> (optimize:clear)
                        </p>
                        <form method="POST" action="{{ route('admin.cache.clear') }}" onsubmit="return confirm('K C25@5=K, GB> E>B8B5 >G8AB8BL 25AL :5H?')">
                            @csrf
                            <input type="hidden" name="type" value="all">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash"></i> G8AB8BL 25AL :5H
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 5H :>=D83C@0F88 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-gear text-primary"></i> >=D83C@0F8O
                        </h5>
                        <p class="card-text text-muted">
                            G8AB8BL :5H D09;>2 :>=D83C@0F88 (config:clear)
                        </p>
                        <form method="POST" action="{{ route('admin.cache.clear') }}">
                            @csrf
                            <input type="hidden" name="type" value="config">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-trash"></i> G8AB8BL config
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 5H <0@H@CB>2 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-signpost text-success"></i> 0@H@CBK
                        </h5>
                        <p class="card-text text-muted">
                            G8AB8BL :5H <0@H@CB>2 (route:clear)
                        </p>
                        <form method="POST" action="{{ route('admin.cache.clear') }}">
                            @csrf
                            <input type="hidden" name="type" value="route">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-trash"></i> G8AB8BL routes
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 5H ?@54AB02;5=89 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-file-earmark-code text-warning"></i> @54AB02;5=8O
                        </h5>
                        <p class="card-text text-muted">
                            G8AB8BL A:><?8;8@>20==K5 Blade H01;>=K (view:clear)
                        </p>
                        <form method="POST" action="{{ route('admin.cache.clear') }}">
                            @csrf
                            <input type="hidden" name="type" value="view">
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-trash"></i> G8AB8BL views
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 5H ?@8;>65=8O -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-database text-info"></i> @8;>65=85
                        </h5>
                        <p class="card-text text-muted">
                            G8AB8BL :5H 40==KE ?@8;>65=8O (cache:clear)
                        </p>
                        <form method="POST" action="{{ route('admin.cache.clear') }}">
                            @csrf
                            <input type="hidden" name="type" value="cache">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bi bi-trash"></i> G8AB8BL cache
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-terminal"></i> -:2820;5=B=K5 :><0=4K
            </div>
            <div class="card-body">
                <p>-B8 459AB28O M:2820;5=B=K 2K?>;=5=8N A;54CNI8E :><0=4 2 B5@<8=0;5:</p>
                <pre class="bg-dark text-light p-3 rounded"><code>php artisan optimize:clear  # G8AB8BL 25AL :5H
php artisan config:clear    # G8AB8BL :5H :>=D83C@0F88
php artisan route:clear     # G8AB8BL :5H <0@H@CB>2
php artisan view:clear      # G8AB8BL :5H ?@54AB02;5=89
php artisan cache:clear     # G8AB8BL :5H ?@8;>65=8O</code></pre>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
