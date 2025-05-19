@extends('frontend.master')

@section('title')
    {{ $post->title ?? 'Blog Details' }} | Easy Learning
@endsection

@section('home')
<!-- ================================
    START HERO AREA
================================= -->
<section class="hero-area pt-100px pb-60px pattern-bg position-relative">
    <div class="container">
        <div class="hero-content text-center">
            <h1 class="section__title mb-3 animate__animated animate__fadeInDown">{{ $post->title }}</h1>
            <ul class="generic-list-item generic-list-item-arrow d-flex justify-content-center flex-wrap fs-15 mb-3 animate__animated animate__fadeInUp">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ route('blog.list') }}">Blog</a></li>
                <li>{{ Str::limit($post->title, 30) }}</li>
            </ul>
            <ul class="generic-list-item generic-list-item-bullet d-flex justify-content-center flex-wrap fs-14 text-muted">
                <li class="d-flex align-items-center mr-4"><i class="la la-user mr-1"></i> <a href="{{ route('instructor.details', $post->instructor_id) }}" class="text-primary">{{ $author->name ?? 'Unknown Author' }}</a></li>
                <li class="d-flex align-items-center mr-4"><i class="la la-calendar mr-1"></i> {{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y') }}</li>
                <li class="d-flex align-items-center"><i class="la la-comment mr-1"></i> <a href="#comments" class="text-primary">{{ $comments->count() }} Comment{{ $comments->count() == 1 ? '' : 's' }}</a></li>
            </ul>
        </div>
    </div>
    <div class="hero-overlay"></div>
</section>

<!-- ================================
    START BLOG AREA
================================= -->
<section class="blog-detail-area pt-5 pb-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Media Section -->
                <div class="media-section mb-5">
                    @if($post->video)
                        <div class="video-container mb-4 position-relative">
                            <video class="img-fluid rounded shadow-sm" controls style="width: 100%; max-height: 450px;" preload="metadata">
                                <source src="{{ asset('upload/blog-posts/' . $post->video) }}" type="video/mp4">
                                Your browser does not support the video tag or the file format.
                            </video>
                            <div class="media-overlay"></div>
                            <!-- Circular Image Overlay -->
                            @if($post->image)
                                <div class="image-overlay position-absolute">
                                    <img loading="lazy" src="{{ $post->image ? asset('upload/blog-posts/' . $post->image) : asset('upload/no_image.jpg') }}" alt="{{ $post->title }}" class="rounded-circle shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                </div>
                            @endif
                        </div>
                    @endif
                    <!-- Image Only (No Video) -->
                    @if(!$post->video && $post->image)
                        <div class="image-container position-relative mb-4">
                            <img loading="lazy" src="{{ $post->image ? asset('upload/blog-posts/' . $post->image) : asset('upload/no_image.jpg') }}" alt="{{ $post->title }}" class="img-fluid rounded shadow-sm" style="max-height: 400px; object-fit: cover; width: 100%;">
                            <div class="media-overlay"></div>
                        </div>
                    @endif
                </div>

                <!-- Content Section -->
                <div class="card card-item mb-5 shadow-sm animate__animated animate__fadeInUp" style="background-color: #ffffff;">
                    <div class="card-body p-5">
                        <div class="post-content fs-16 text-gray-800">{!! $post->content !!}</div>
                        <hr class="section-divider my-4">
                        <!-- Tags -->
                        <div class="tags-section">
                            <h4 class="fs-18 font-weight-bold mb-3"><i class="la la-tags mr-2"></i> Tags</h4>
                            <ul class="generic-list-item generic-list-item-boxed d-flex flex-wrap fs-14">
                                <li class="mr-2 mb-2"><a href="#" class="btn btn-sm btn-outline-secondary hover-bg-primary">{{ $category->name ?? 'Uncategorized' }}</a></li>
                            </ul>
                        </div>
                        <!-- Share Buttons -->
                        <div class="share-section">
                            <h4 class="fs-18 font-weight-bold mb-3"><i class="la la-share mr-2"></i> Share This Post</h4>
                            <div class="social-share d-flex align-items-center gap-3">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.detail', $post->slug)) }}" target="_blank" class="btn btn-sm btn-social facebook">
                                    <i class="la la-facebook mr-1"></i> Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.detail', $post->slug)) }}&text={{ urlencode($post->title) }}" target="_blank" class="btn btn-sm btn-social twitter">
                                    <i class="la la-twitter mr-1"></i> Twitter
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('blog.detail', $post->slug)) }}&title={{ urlencode($post->title) }}" target="_blank" class="btn btn-sm btn-social linkedin">
                                    <i class="la la-linkedin mr-1"></i> LinkedIn
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Author Section -->
                <div class="instructor-wrap mb-5 animate__animated animate__fadeInUp">
                    <h4 class="fs-20 font-weight-bold mb-4"><i class="la la-user-circle mr-2"></i> About the Author</h4>
                    <div class="media media-card align-items-center p-4 bg-light rounded shadow-sm hover-card">
                        <div class="media-img mr-4">
                            <img src="{{ $author->photo ? asset('upload/instructor_images/' . $author->photo) : asset('upload/no_image.jpg') }}" alt="{{ $author->name ?? 'Unknown Author' }}'s Profile" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <div class="media-body">
                            <h5 class="fs-17 font-weight-bold mb-2">{{ $author->name ?? 'Unknown Author' }}</h5>
                            <span class="d-block fs-14 text-muted mb-2">{{ $author->email ?? 'No email provided' }}</span>
                            <p class="fs-15 text-gray">{{ $author->bio ?? 'No bio available' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="comments-wrap mb-5 animate__animated animate__fadeInUp" id="comments">
                    <h4 class="fs-20 font-weight-bold mb-4"><i class="la la-comments mr-2"></i> Comments ({{ $comments->count() }})</h4>
                    <div class="comment-list">
                        @forelse($comments as $comment)
                            <div class="comment mb-4 border-bottom pb-4 shadow-sm rounded hover-card" id="comment-{{ $comment->id }}">
                                <div class="media">
                                    <div class="media-img mr-4">
                                        <img src="{{ $comment->user->photo ? Storage::url('upload/user_images/' . $comment->user->photo) : asset('upload/no_image.jpg') }}" alt="{{ $comment->user->name }}'s Profile" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                    <div class="media-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="fs-16 font-weight-medium">{{ $comment->user->name }}</h5>
                                            <span class="fs-13 text-muted">{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</span>
                                        </div>
                                        <p class="fs-15 text-gray mb-3 comment-text" id="comment-text-{{ $comment->id }}">{{ $comment->message }}</p>
                                        @auth
                                            <div class="d-flex gap-2">
                                                @if(Auth::id() !== $comment->user_id)
                                                    <button class="btn btn-sm btn-link text-primary p-0" onclick="toggleReplyForm({{ $comment->id }})">Reply</button>
                                                @endif
                                            </div>
                                        @endauth
                                    </div>
                                </div>

                                @if($comment->replies->count() > 0)
                                    <div class="replies ml-5 mt-3">
                                        @foreach($comment->replies as $reply)
                                            @if($reply->approved)
                                                <div class="reply mb-3 shadow-sm rounded hover-card">
                                                    <div class="media">
                                                        <div class="media-img mr-3">
                                                            <img src="{{ $reply->user->photo ? Storage::url('upload/user_images/' . $reply->user->photo) : asset('upload/no_image.jpg') }}" alt="{{ $reply->user->name }}'s Profile" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                                        </div>
                                                        <div class="media-body">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <h6 class="fs-15 font-weight-medium">{{ $reply->user->name }} {{ $reply->user_type === 'App\\Models\\Instructor' ? '(Instructor)' : '' }}</h6>
                                                                <span class="fs-12 text-muted">{{ \Carbon\Carbon::parse($reply->created_at)->diffForHumans() }}</span>
                                                            </div>
                                                            <p class="fs-14 text-gray mb-0">{{ $reply->message }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                @auth
                                    @if(Auth::id() !== $comment->user_id)
                                        <div class="reply-form mt-3 ml-5" id="reply-form-{{ $comment->id }}" style="display: none;">
                                            <form action="{{ route('comments.reply', [$post->slug, $comment->id]) }}" method="POST">
                                                @csrf
                                                <div class="form-group mb-2">
                                                    <textarea class="form-control" name="message" placeholder="Write your reply..." rows="2" required></textarea>
                                                    @error('message')
                                                        <span class="text-danger fs-13">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-primary">Submit Reply</button>
                                            </form>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        @empty
                            <p class="fs-15 text-muted">No comments yet. Be the first to comment!</p>
                        @endforelse
                    </div>
                </div>

                <!-- Comment Form -->
                <div class="add-comment-wrap mb-5 animate__animated animate__fadeInUp">
                    <h4 class="fs-20 font-weight-bold mb-4"><i class="la la-comment-alt mr-2"></i> Leave a Comment</h4>
                    @auth
                        @if(session('success'))
                            <div class="alert alert-success mb-3 animate__animated animate__fadeIn">{{ session('success') }}</div>
                        @endif
                        <form method="post" class="card card-item p-4 shadow-sm" action="{{ route('comments.store', $post->slug) }}" style="background-color: #ffffff;">
                            @csrf
                            <div class="form-group mb-3">
                                <textarea class="form-control form--control" name="message" placeholder="Write your comment here..." rows="4" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <span class="text-danger fs-13">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button class="btn theme-btn" type="submit" style="background-color: #EC5252; color: #ffffff;">Submit Comment</button>
                            </div>
                        </form>
                    @else
                        <p class="fs-15 text-muted">Please <a href="{{ route('login') }}" class="text-primary">log in</a> to leave a comment.</p>
                    @endauth
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar mb-5 animate__animated animate__fadeInRight">
                    <h4 class="fs-20 font-weight-bold mb-4"><i class="la la-bookmark mr-2"></i> Recent Posts</h4>
                    @forelse($otherBlogs as $blog)
                        <div class="media media-card mb-3 shadow-sm rounded hover-card" style="background-color: #ffffff;">
                            <a href="{{ route('blog.detail', $blog->slug) }}" class="media-img mr-3">
                                <img loading="lazy" src="{{ $blog->image ? asset('upload/blog-posts/' . $blog->image) : asset('upload/no_image.jpg') }}" alt="{{ $blog->title }}" class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                            </a>
                            <div class="media-body">
                                <h5 class="fs-15 font-weight-bold mb-1">
                                    <a href="{{ route('blog.detail', $blog->slug) }}" class="text-dark hover-text-primary">{{ Str::limit($blog->title, 25) }}</a>
                                </h5>
                                <p class="fs-12 text-muted mb-1">{{ \Carbon\Carbon::parse($blog->created_at)->format('M d, Y') }}</p>
                                <span class="fs-13 text-gray">{{ $blog->author_name }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="fs-15 text-muted">No other posts available.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Scripts -->
<script>
    function toggleReplyForm(commentId) {
        const form = document.getElementById(`reply-form-${commentId}`);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
</script>

<!-- Styles (Scoped to this page) -->
<style>
    /* Hero Area */
    .hero-area {
        background: linear-gradient(135deg, #e6f0fa 0%, #f0f8ff 100%);
        padding: 100px 0 60px;
        position: relative;
        overflow: hidden;
    }
    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.2), transparent);
        z-index: 1;
    }
    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        margin: 0 auto;
    }
    .hero-content .section__title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #333333;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Media Section */
    .blog-detail-area .media-section {
        margin-bottom: 3rem;
    }
    .blog-detail-area .video-container, .blog-detail-area .image-container {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        background-color: #f8f9fa;
        padding: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .blog-detail-area .video-container:hover, .blog-detail-area .image-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }
    .blog-detail-area .video-container video, .blog-detail-area .image-container img {
        transition: transform 0.3s ease;
    }
    .blog-detail-area .video-container:hover video, .blog-detail-area .image-container:hover img {
        transform: scale(1.03);
    }
    .blog-detail-area .media-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.15), transparent);
        z-index: 1;
    }
    .blog-detail-area .image-overlay {
        top: 15px;
        right: 15px;
        z-index: 2;
    }
    .blog-detail-area .image-overlay img {
        border: 2px solid #ffffff;
        transition: transform 0.3s ease;
    }
    .blog-detail-area .image-overlay img:hover {
        transform: scale(1.1);
    }

    /* Card Styling */
    .blog-detail-area .card-item {
        border: none;
        border-radius: 12px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .blog-detail-area .card-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
    .blog-detail-area .card-body {
        padding: 2.5rem !important;
    }
    .blog-detail-area .section-divider {
        border-color: #e9ecef;
        margin: 2rem 0;
    }

    /* Tags and Share Sections */
    .blog-detail-area .tags-section, .blog-detail-area .share-section {
        margin-top: 2rem;
    }
    .blog-detail-area .btn-outline-secondary {
        border-color: #e9ecef;
        color: #6c757d;
        transition: all 0.3s ease;
    }
    .blog-detail-area .hover-bg-primary:hover {
        background-color: #EC5252 !important;
        color: #ffffff !important;
        border-color: #EC5252 !important;
    }
    .blog-detail-area .btn-social {
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 14px;
        transition: all 0.3s ease;
        background-color: #e6f0fa;
    }
    .blog-detail-area .btn-social.facebook { color: #3b5998; }
    .blog-detail-area .btn-social.twitter { color: #1da1f2; }
    .blog-detail-area .btn-social.linkedin { color: #0077b5; }
    .blog-detail-area .btn-social:hover {
        transform: scale(1.1);
        opacity: 0.9;
    }

    /* Author and Comments */
    .blog-detail-area .hover-card {
        transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
    }
    .blog-detail-area .hover-card:hover {
        transform: translateY(-3px);
        background-color: #f0f8ff !important;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08) !important;
    }
    .blog-detail-area .comment, .blog-detail-area .reply {
        background-color: #ffffff;
        border-radius: 8px;
        padding: 1.5rem;
    }
    .blog-detail-area .comment:hover, .blog-detail-area .reply:hover {
        background-color: #f0f8ff;
    }

    /* Comment Form */
    .blog-detail-area .theme-btn {
        background-color: #EC5252;
        color: #ffffff;
        border-radius: 8px;
        padding: 10px 20px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .blog-detail-area .theme-btn:hover {
        background-color: #D94444;
        transform: scale(1.05);
    }

    /* Sidebar */
    .blog-detail-area .sidebar .media-card {
        padding: 1rem;
        border-radius: 10px;
    }
    .blog-detail-area .sidebar .media-card:hover {
        background-color: #f0f8ff !important;
    }
    .blog-detail-area .text-primary, .blog-detail-area .hover-text-primary:hover {
        color: #EC5252 !important;
        text-decoration: none;
    }

    /* Animations */
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInRight {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* Responsive */
    @media (max-width: 767px) {
        .hero-area {
            padding: 60px 0 40px;
        }
        .hero-content .section__title {
            font-size: 1.8rem;
        }
        .blog-detail-area .video-container video {
            max-height: 350px;
        }
        .blog-detail-area .image-container img {
            max-height: 300px;
        }
        .blog-detail-area .image-overlay img {
            width: 60px;
            height: 60px;
        }
        .blog-detail-area .card-body {
            padding: 1.5rem !important;
        }
        .blog-detail-area .btn-social {
            font-size: 13px;
            padding: 6px 12px;
        }
        .blog-detail-area .comment, .blog-detail-area .reply {
            padding: 1rem;
        }
        .blog-detail-area .sidebar .media-card {
            padding: 0.8rem;
        }
    }
</style>
@endsection