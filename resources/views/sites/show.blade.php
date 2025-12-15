<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('sites.index') }}">Каталог сайтов</a></li>
                @if($site->category)
                    @if($site->category->parent)
                        <li class="breadcrumb-item"><a href="{{ route('sites.category', $site->category->parent->slug) }}">{{ $site->category->parent->name }}</a></li>
                    @endif
                    <li class="breadcrumb-item"><a href="{{ route('sites.category', $site->category->slug) }}">{{ $site->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ $site->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                {{-- Header with logo and title --}}
                <div class="d-flex align-items-start mb-4">
                    @if($site->logo)
                        <img src="{{ asset('storage/' . $site->logo) }}"
                             alt="{{ $site->name }}"
                             class="rounded-circle me-4"
                             style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-4"
                             style="width: 100px; height: 100px;">
                            <i class="bi bi-globe text-white fs-1"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="h3 mb-2">{{ $site->name }}</h1>
                        <a href="{{ $site->url }}" target="_blank" class="btn btn-primary">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Перейти на сайт
                        </a>
                        @if($site->category)
                            <span class="badge bg-info ms-2 fs-6">{{ $site->category->name }}</span>
                        @endif
                    </div>
                </div>

                <hr>

                {{-- Description --}}
                <h5>Описание</h5>
                <div class="site-description">
                    @if($site->description)
                        @editorJsRender($site->description)
                    @else
                        <p class="text-muted">Описание не указано</p>
                    @endif
                </div>

                {{-- Additional images --}}
                @if($site->images && count($site->images) > 0)
                <hr>
                <h5>Изображения</h5>
                <div class="row g-2">
                    @foreach($site->images as $image)
                    <div class="col-md-4">
                        <a href="{{ $image['url'] ?? asset('storage/' . $image['path']) }}" target="_blank">
                            <img src="{{ $image['url'] ?? asset('storage/' . $image['path']) }}"
                                 class="img-thumbnail"
                                 style="width: 100%; height: 150px; object-fit: cover;">
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        {{-- Contact info --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Контакты</h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <i class="bi bi-envelope me-2"></i>
                    <a href="mailto:{{ $site->contact_email }}">{{ $site->contact_email }}</a>
                </p>
                <p class="mb-0">
                    <i class="bi bi-link-45deg me-2"></i>
                    <a href="{{ $site->url }}" target="_blank">{{ Str::limit($site->url, 30) }}</a>
                </p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Статистика</h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <i class="bi bi-eye me-2"></i>Просмотров: <strong>{{ $site->views_count }}</strong>
                </p>
                <p class="mb-0">
                    <i class="bi bi-calendar me-2"></i>Добавлен: {{ $site->created_at->format('d.m.Y') }}
                </p>
            </div>
        </div>

        {{-- Owner actions --}}
        @auth
            @if(Auth::id() == $site->user_id || Auth::user()->isAdmin())
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Управление</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('sites.edit', $site->id) }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-pencil me-1"></i>Редактировать
                    </a>
                    <form id="delete-site-form" method="POST" action="{{ route('sites.destroy', $site->id) }}" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button type="button" class="btn btn-outline-danger w-100"
                            x-data
                            @click="$dispatch('confirm', {
                                title: 'Удалить сайт?',
                                message: '{{ $site->name }}',
                                type: 'danger',
                                confirmText: 'Удалить',
                                form: 'delete-site-form'
                            })">
                        <i class="bi bi-trash me-1"></i>Удалить
                    </button>
                </div>
            </div>
            @endif
        @endauth
    </div>
</div>

{{-- Related sites --}}
@if($relatedSites->count() > 0)
<hr>
<h4>Похожие сайты</h4>
<div class="row g-4">
    @foreach($relatedSites as $related)
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    @if($related->logo)
                        <img src="{{ asset('storage/' . $related->logo) }}"
                             alt="{{ $related->name }}"
                             class="rounded-circle me-2"
                             style="width: 40px; height: 40px; object-fit: cover;">
                    @else
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2"
                             style="width: 40px; height: 40px;">
                            <i class="bi bi-globe text-white"></i>
                        </div>
                    @endif
                    <h6 class="mb-0">
                        <a href="{{ route('sites.show', $related->slug) }}" class="text-decoration-none">
                            {{ Str::limit($related->name, 20) }}
                        </a>
                    </h6>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@push('styles')
<style>
    /* Editor.js content styles */
    .site-description img {
        max-width: 100%;
        height: auto;
        border-radius: 0.375rem;
        margin: 0.5rem 0;
    }
    .site-description .image-tool {
        max-width: 100%;
    }
    .site-description .image-tool__image {
        max-width: 100%;
        border-radius: 0.375rem;
    }
    .site-description .image-tool__image-picture {
        max-width: 100%;
        height: auto;
    }
    .site-description .cdx-block {
        margin-bottom: 1rem;
    }
</style>
@endpush
</x-app-layout>
