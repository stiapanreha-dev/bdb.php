<x-app-layout>
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('sites.index') }}">Каталог сайтов</a></li>
                @if($category->parent)
                    <li class="breadcrumb-item"><a href="{{ route('sites.category', $category->parent->slug) }}">{{ $category->parent->name }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ $category->name }}</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>{{ $category->name }}</h2>
                @if($category->description)
                    <p class="text-muted">{{ $category->description }}</p>
                @endif
            </div>
            @auth
            <a href="{{ route('sites.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Добавить сайт
            </a>
            @endauth
        </div>
    </div>
</div>

<div class="row">
    {{-- Sidebar with categories --}}
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Категории</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('sites.index') }}"
                   class="list-group-item list-group-item-action">
                    Все категории
                </a>
            </div>
            <div class="accordion accordion-flush" id="categoriesAccordion">
                @foreach($categories->sortBy('name') as $cat)
                    @php
                        $isCurrentParent = $category->id == $cat->id || ($category->parent_id && $category->parent_id == $cat->id);
                    @endphp
                    <div class="accordion-item">
                        @if($cat->children->count() > 0)
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $isCurrentParent ? '' : 'collapsed' }} py-2 px-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#cat-{{ $cat->id }}">
                                    <span class="me-auto">{{ $cat->name }}</span>
                                    <span class="badge {{ $category->id == $cat->id ? 'bg-light text-dark' : 'bg-primary' }} rounded-pill me-2">{{ $cat->approvedSitesCount() }}</span>
                                </button>
                            </h2>
                            <div id="cat-{{ $cat->id }}" class="accordion-collapse collapse {{ $isCurrentParent ? 'show' : '' }}">
                                <div class="list-group list-group-flush">
                                    @foreach($cat->children->sortBy('name') as $child)
                                        <a href="{{ route('sites.category', $child->slug) }}"
                                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center ps-4 py-2 {{ $category->id == $child->id ? 'active' : '' }}">
                                            {{ $child->name }}
                                            <span class="badge rounded-pill {{ $category->id == $child->id ? 'bg-light text-dark' : 'bg-secondary' }}">{{ $child->approvedSitesCount() }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ route('sites.category', $cat->slug) }}"
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 {{ $category->id == $cat->id ? 'active' : '' }}">
                                {{ $cat->name }}
                                <span class="badge rounded-pill {{ $category->id == $cat->id ? 'bg-light text-dark' : 'bg-primary' }}">{{ $cat->approvedSitesCount() }}</span>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="col-md-9">
        {{-- Sites grid --}}
        <div class="row g-4">
            @forelse($sites as $site)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            @if($site->logo)
                                <img src="{{ asset('storage/' . $site->logo) }}"
                                     alt="{{ $site->name }}"
                                     class="rounded-circle me-3"
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width: 50px; height: 50px;">
                                    <i class="bi bi-globe text-white"></i>
                                </div>
                            @endif
                            <div>
                                <h5 class="card-title mb-0">
                                    <a href="{{ route('sites.show', $site->slug) }}" class="text-decoration-none">
                                        {{ $site->name }}
                                    </a>
                                </h5>
                                @if($site->category)
                                    <small class="text-muted">{{ $site->category->name }}</small>
                                @endif
                            </div>
                        </div>

                        <a href="{{ $site->url }}" target="_blank" class="text-muted small d-block mb-2">
                            {{ Str::limit($site->url, 35) }}
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>

                        @if($site->description)
                            <p class="card-text small text-muted">
                                {{ Str::limit(strip_tags(json_decode($site->description, true)['blocks'][0]['data']['text'] ?? ''), 80) }}
                            </p>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-eye me-1"></i>{{ $site->views_count }}
                            </small>
                            <a href="{{ route('sites.show', $site->slug) }}" class="btn btn-sm btn-outline-primary">
                                Подробнее
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-globe fs-1 text-muted d-block mb-3"></i>
                    <p class="text-muted">В категории "{{ $category->name }}" пока нет сайтов</p>
                    @auth
                        <a href="{{ route('sites.create') }}" class="btn btn-primary">Добавить сайт</a>
                    @endauth
                </div>
            </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $sites->links() }}
        </div>
    </div>
</div>
</x-app-layout>
