<x-app-layout>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Карточка новости -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Новость</h5>
                        <div>
                            @auth
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('news.edit', $news) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Редактировать
                                    </a>
                                    <form id="delete-news-form" action="{{ route('news.destroy', $news) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            x-data
                                            @click="$dispatch('confirm', {
                                                title: 'Удалить новость?',
                                                message: 'Новость будет удалена без возможности восстановления',
                                                type: 'danger',
                                                confirmText: 'Удалить',
                                                form: 'delete-news-form'
                                            })">
                                        <i class="bi bi-trash"></i> Удалить
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h2 class="card-title mb-3">{{ $news->title }}</h2>

                    <div class="mb-3">
                        <span class="text-muted">
                            <i class="bi bi-calendar3"></i>
                            {{ $news->published_at ? $news->published_at->format('d.m.Y H:i') : $news->created_at->format('d.m.Y H:i') }}
                        </span>
                    </div>

                    <hr>

                    @if($news->images && count($news->images) > 0)
                        <div class="mb-4">
                            <div class="row g-2 justify-content-center">
                                @foreach($news->images as $index => $imageUrl)
                                    <div class="col-12 col-md-4">
                                        <img src="{{ $imageUrl }}"
                                             alt="Изображение"
                                             class="img-thumbnail gallery-image"
                                             data-index="{{ $index }}"
                                             style="width: 100%; height: 200px; object-fit: cover; cursor: pointer;">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Lightbox Modal -->
                        <div class="modal fade" id="imageLightbox" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-xl">
                                <div class="modal-content bg-transparent border-0">
                                    <div class="modal-body p-0 text-center position-relative">
                                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" style="z-index: 1;"></button>
                                        @if(count($news->images) > 1)
                                        <button type="button" class="btn btn-dark position-absolute start-0 top-50 translate-middle-y ms-2" id="prevImage">
                                            <i class="bi bi-chevron-left"></i>
                                        </button>
                                        <button type="button" class="btn btn-dark position-absolute end-0 top-50 translate-middle-y me-2" id="nextImage">
                                            <i class="bi bi-chevron-right"></i>
                                        </button>
                                        @endif
                                        <img src="" id="lightboxImage" class="img-fluid" style="max-height: 85vh;">
                                        <div class="text-white mt-2" id="imageCounter"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="news-content mt-4">
                        @editorJsRender($news->content)
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <a href="{{ route('news.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Назад к списку
                </a>
            </div>
        </div>
    </div>
</div>

@if($news->images && count($news->images) > 0)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const images = @json($news->images);
    let currentIndex = 0;
    const lightbox = new bootstrap.Modal(document.getElementById('imageLightbox'));
    const lightboxImage = document.getElementById('lightboxImage');
    const imageCounter = document.getElementById('imageCounter');

    function showImage(index) {
        currentIndex = index;
        lightboxImage.src = images[index];
        if (images.length > 1) {
            imageCounter.textContent = (index + 1) + ' / ' + images.length;
        }
    }

    document.querySelectorAll('.gallery-image').forEach(img => {
        img.addEventListener('click', function() {
            showImage(parseInt(this.dataset.index));
            lightbox.show();
        });
    });

    const prevBtn = document.getElementById('prevImage');
    const nextBtn = document.getElementById('nextImage');

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            showImage((currentIndex - 1 + images.length) % images.length);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            showImage((currentIndex + 1) % images.length);
        });
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!document.getElementById('imageLightbox').classList.contains('show')) return;
        if (e.key === 'ArrowLeft' && prevBtn) prevBtn.click();
        if (e.key === 'ArrowRight' && nextBtn) nextBtn.click();
        if (e.key === 'Escape') lightbox.hide();
    });
});
</script>
@endpush
@endif

@push('styles')
<style>
    .news-content {
        line-height: 1.6;
        font-size: 1.05rem;
    }
    .news-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 20px 0;
    }
    .news-content h1 {
        font-size: 2rem;
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    .news-content h2 {
        font-size: 1.75rem;
        margin-top: 1.75rem;
        margin-bottom: 0.875rem;
        font-weight: 600;
    }
    .news-content h3 {
        font-size: 1.5rem;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }
    .news-content h4 {
        font-size: 1.25rem;
        margin-top: 1.25rem;
        margin-bottom: 0.625rem;
        font-weight: 600;
    }
    .news-content p {
        margin-bottom: 1.2rem;
    }
    .news-content a {
        color: #0d6efd;
        text-decoration: underline;
    }
    .news-content blockquote {
        border-left: 4px solid #dee2e6;
        padding-left: 1.5rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: #6c757d;
    }
    .news-content ul, .news-content ol {
        padding-left: 2rem;
        margin-bottom: 1.2rem;
    }
    .news-content li {
        margin-bottom: 0.5rem;
    }
    .news-content pre {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.375rem;
        overflow-x: auto;
        margin: 1rem 0;
    }
    .news-content code {
        background-color: #f8f9fa;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-family: 'Courier New', monospace;
        font-size: 0.9em;
    }
    .news-content table {
        width: 100%;
        margin: 1.5rem 0;
        border-collapse: collapse;
    }
    .news-content table th,
    .news-content table td {
        border: 1px solid #dee2e6;
        padding: 0.75rem;
    }
    .news-content table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
</style>
@endpush
</x-app-layout>
