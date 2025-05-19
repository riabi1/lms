<section class="blog-area section--padding bg-light overflow-hidden">
    <div class="container">
        <!-- Section Heading -->
        <div class="section-heading text-center mb-5">
            <h5 class="ribbon ribbon-lg mb-2 bg-primary text-white px-3 py-1">News Feeds</h5>
            <h2 class="section__title fw-bold mb-2">Explore Latest News & Articles</h2>
            <span class="section-divider mx-auto" style="width: 80px; height: 4px; background-color: #007bff;"></span>
        </div>

        <!-- Blog Post Carousel -->
        <div class="blog-post-carousel owl-action-styled half-shape mt-4">
            @php
                $blogPosts = DB::table('blog_posts')
                    ->join('instructors', 'blog_posts.instructor_id', '=', 'instructors.id')
                    ->join('blog_categories', 'blog_posts.blog_category_id', '=', 'blog_categories.id')
                    ->select(
                        'blog_posts.id',
                        'blog_posts.title',
                        'blog_posts.slug',
                        'blog_posts.content',
                        'blog_posts.image',
                        'blog_posts.created_at',
                        'instructors.name as author_name',
                        'instructors.id as instructor_id',
                        'blog_categories.name as category_name'
                    )
                    ->where('blog_posts.status', 'active')
                    ->orderBy('blog_posts.created_at', 'desc')
                    ->limit(6)
                    ->get();
            @endphp

            @forelse($blogPosts as $post)
                <div class="card card-item border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-image position-relative">
                        <a href="{{ route('blog.detail', $post->slug) }}">
                            <img class="card-img-top" src="{{ $post->image ? asset('upload/blog-posts/' . $post->image) : asset('upload/no_image.jpg') }}" alt="{{ $post->title }}" style="height: 220px; object-fit: cover;">
                        </a>
                        <div class="course-badge-labels position-absolute top-0 start-0 m-3">
                            <div class="course-badge bg-primary text-white px-2 py-1 rounded fs-13">
                                {{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y') }}
                            </div>
                            <div class="course-badge bg-success text-white px-2 py-1 rounded fs-13 mt-1">
                                {{ $post->category_name }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="card-title fs-18 fw-bold mb-3">
                            <a href="{{ route('blog.detail', $post->slug) }}" class="text-dark hover-text-primary">
                                {{ Str::limit($post->title, 45) }}
                            </a>
                        </h5>
                        <p class="card-text fs-14 text-muted mb-3">
                            {!! Str::limit(strip_tags($post->content), 100, '...') !!}
                        </p>
                        <ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet d-flex align-items-center flex-wrap fs-14 text-muted mb-3">
                            <li class="me-3 d-flex align-items-center">
                                By <a href="{{ route('instructor.details', $post->instructor_id) }}" class="text-primary ms-1">{{ $post->author_name }}</a>
                            </li>
                        </ul>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('blog.detail', $post->slug) }}" class="btn theme-btn theme-btn-sm theme-btn-primary px-3 py-1">
                                Read More <i class="la la-arrow-right icon ms-1"></i>
                            </a>
                            <div class="social-share d-flex align-items-center">
                                <span class="share-toggle text-muted fs-14 me-2">Share</span>
                                <div class="social-icons social-icons-styled d-flex gap-2">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.detail', $post->slug)) }}" target="_blank" class="bg-primary">
                                        <i class="la la-facebook"></i>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.detail', $post->slug)) }}&text={{ urlencode($post->title) }}" target="_blank" class="bg-info">
                                        <i class="la la-twitter"></i>
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('blog.detail', $post->slug)) }}&title={{ urlencode($post->title) }}" target="_blank" class="bg-dark">
                                        <i class="la la-linkedin"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <p class="text-muted fs-16">No blog posts available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<style>
    .blog-area {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    .blog-area .hover-text-primary:hover {
        color: #007bff !important;
        text-decoration: none;
    }
    .blog-area .card-item {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .blog-area .card-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
    }
    .blog-area .card-img-top {
        transition: transform 0.3s ease;
    }
    .blog-area .card-item:hover .card-img-top {
        transform: scale(1.05);
    }
    .blog-area .social-share .social-icons {
        display: none;
        transition: all 0.3s ease;
    }
    .blog-area .social-share:hover .social-icons,
    .blog-area .social-icons:hover {
        display: flex !important;
    }
    .blog-area .social-icons-styled a {
        width: 32px;
        height: 32px;
        line-height: 32px;
        text-align: center;
        border-radius: 50%;
        color: #fff;
        font-size: 16px;
        transition: all 0.3s ease;
    }
    .blog-area .social-icons-styled a:hover {
        opacity: 0.85;
        transform: scale(1.1);
    }
    .blog-area .theme-btn-primary {
        background-color: #007bff;
        color: #fff;
        border: none;
        transition: background-color 0.3s ease;
    }
    .blog-area .theme-btn-primary:hover {
        background-color: #0056b3;
    }
    .blog-area .owl-carousel {
        z-index: 1;
    }
</style>

<!-- Include Owl Carousel library only -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>