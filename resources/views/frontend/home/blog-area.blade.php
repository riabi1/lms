<section class="blog-area section--padding bg-gray overflow-hidden">
    <div class="container">
        <!-- Section Heading -->
        <div class="section-heading text-center mb-5">
            <h5 class="ribbon ribbon-lg mb-3 text-uppercase text-primary">News Feeds</h5>
            <h2 class="section__title fw-bold mb-2">Latest News & Articles</h2>
            <span class="section-divider mx-auto" style="width: 60px; height: 3px; background-color: #007bff;"></span>
        </div><!-- end section-heading -->

        <!-- Blog Post Carousel -->
        <div class="blog-post-carousel owl-action-styled half-shape mt-30px">
            @php
                $blogPosts = DB::table('blog_posts')
                    ->join('instructors', 'blog_posts.instructor_id', '=', 'instructors.id')
                    ->join('blog_categories', 'blog_posts.blog_category_id', '=', 'blog_categories.id')
                    ->select('blog_posts.*', 'instructors.name as author_name', 'blog_categories.name as category_name', 'instructors.id as instructor_id')
                    ->where('blog_posts.status', 'active')
                    ->orderBy('blog_posts.created_at', 'desc')
                    ->limit(6) // Limite à 6 articles pour le carrousel
                    ->get();
            @endphp

            @forelse($blogPosts as $post)
                <div class="card card-item border-0 shadow-sm">
                    <!-- Card Image -->
                    <div class="card-image position-relative">
                        <a href="{{ route('blog.detail', $post->slug) }}">
                            <img class="card-img-top" src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" style="height: 220px; object-fit: cover;">
                        </a>
                        <div class="course-badge-labels position-absolute top-0 start-0 m-3">
                            <div class="course-badge bg-primary text-white px-2 py-1 rounded fs-13">
                                {{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y') }}
                            </div>
                        </div>
                    </div><!-- end card-image -->

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <h5 class="card-title fs-18 fw-bold mb-3">
                            <a href="{{ route('blog.detail', $post->slug) }}" class="text-dark hover-text-primary">
                                {{ Str::limit($post->title, 45) }}
                            </a>
                        </h5>
                        <ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet d-flex align-items-center flex-wrap fs-14 text-muted pt-2 mb-3">
                            <li class="me-3 d-flex align-items-center">
                                By <a href="{{ route('instructor.details', $post->instructor_id) }}" class="text-primary ms-1">{{ $post->author_name }}</a>
                            </li>
                           
                        
                        </ul>
                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <a href="{{ route('blog.detail', $post->slug) }}" class="btn theme-btn theme-btn-sm theme-btn-white px-3 py-1">
                                Read More <i class="la la-arrow-right icon ms-1"></i>
                            </a>
                          
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            @empty
                <div class="text-center py-5">
                    <p class="text-muted fs-16">No blog posts available at the moment.</p>
                </div>
            @endforelse
        </div><!-- end blog-post-carousel -->
    </div><!-- end container -->
</section><!-- end blog-area -->

<style>
    .hover-text-primary:hover {
        color: #007bff !important;
        text-decoration: none;
    }
    .share-toggle + .social-icons {
        display: none;
    }
    .share-toggle:hover + .social-icons,
    .social-icons:hover {
        display: flex !important;
    }
    .social-icons-styled a {
        width: 28px;
        height: 28px;
        line-height: 28px;
        text-align: center;
        border-radius: 50%;
        color: #fff;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    .social-icons-styled a:hover {
        opacity: 0.8;
    }
   
    .card-item {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
</style>

<!-- Owl Carousel Scripts (assurez-vous qu'ils sont dans votre layout ou ajoutez-les ici) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script>
    $(document).ready(function() {
        $('.blog-post-carousel').owlCarousel({
            loop: true,
            margin: 20,
            nav: true,
            dots: true,
            responsive: {
                0: { items: 1 },
                768: { items: 2 },
                992: { items: 3 }
            }
        });
    });
</script>