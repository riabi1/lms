@extends('frontend.master')

@section('title')
    {{ $post->title ?? 'Blog Details' }} | Easy Learning
@endsection

@section('home')
<!-- ================================
    START BREADCRUMB AREA
================================= -->
<section class="breadcrumb-area pt-80px pb-80px pattern-bg">
    <div class="container">
        <div class="breadcrumb-content">
            <div class="section-heading pb-2">
                <h1 class="section__title mb-0">{{ $post->title }}</h1>
            </div>
            <ul class="generic-list-item generic-list-item-arrow d-flex flex-wrap align-items-center fs-14 mb-2">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ route('blog.list') }}">Blog</a></li>
                <li>{{ Str::limit($post->title, 30) }}</li>
            </ul>
            <ul class="generic-list-item generic-list-item-bullet d-flex align-items-center flex-wrap fs-13 text-muted">
                <li class="d-flex align-items-center mr-3">By <a href="{{ route('instructor.details', $post->instructor_id) }}" class="text-primary ml-1">{{ $author->name ?? 'Unknown Author' }}</a></li>
                <li class="d-flex align-items-center mr-3">{{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y') }}</li>
                <li class="d-flex align-items-center"><a href="#comments" class="text-primary">{{ $comments->count() }} Comment{{ $comments->count() == 1 ? '' : 's' }}</a></li>
            </ul>
        </div>
    </div>
</section>

<!-- ================================
    START BLOG AREA
================================= -->
<section class="blog-area pt-5 pb-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-item mb-4 shadow-sm" style="background-color: #ffffff; border-radius: 12px;">
                    <div class="card-body p-4">
                        <!-- Media Container (Video and Image Side by Side) -->
                        @if($post->video || $post->image)
                            <div class="media-container mb-4 d-flex flex-wrap gap-3">
                                @if($post->video)
                                    <div class="video-wrapper flex-grow-1" style="flex: {{ $post->image ? '1 1 50%' : '1 1 100%' }};">
                                        <div class="video-overlay">
                                            <video class="img-fluid" controls style="width: 100%; max-height: 400px; border-radius: 12px;" preload="metadata">
                                                <source src="{{ asset('upload/blog-posts/' . $post->video) }}" type="video/mp4">
                                                <source src="{{ asset('upload/blog-posts/' . $post->video) }}" type="video/webm">
                                                Your browser does not support the video tag.
                                            </video>
                                            <div class="play-icon">
                                                <i class="la la-play"></i>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($post->image)
                                    <div class="image-wrapper flex-grow-1" style="flex: {{ $post->video ? '1 1 50%' : '1 1 100%' }};">
                                        <div class="image-overlay">
                                            <img loading="lazy" src="{{ $post->image ? asset('upload/blog-posts/' . $post->image) : asset('upload/no_image.jpg') }}" alt="{{ $post->title }}" class="img-fluid" style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 12px;">
                                            <div class="zoom-icon">
                                                <i class="la la-search-plus"></i>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                        <!-- Post Content -->
                        <div class="post-content">{!! $post->content !!}</div>
                        <div class="section-block my-4"></div>
                        <!-- Tags -->
                        <h4 class="fs-18 font-weight-bold mb-3">Tags</h4>
                        <ul class="generic-list-item generic-list-item-boxed d-flex flex-wrap fs-14">
                            <li class="mr-2 mb-2"><a href="#" class="btn btn-sm btn-outline-secondary">{{ $category->name ?? 'Uncategorized' }}</a></li>
                        </ul>
                        <!-- Share Buttons -->
                        <h4 class="fs-18 font-weight-bold mb-3">Share This Post</h4>
                        <div class="social-share d-flex align-items-center gap-2 mb-4">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.detail', $post->slug)) }}" target="_blank" class="btn btn-sm" style="background-color: #f8e9e9; color: #3b5998;">
                                <i class="la la-facebook mr-1"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.detail', $post->slug)) }}&text={{ urlencode($post->title) }}" target="_blank" class="btn btn-sm" style="background-color: #f8e9e9; color: #1da1f2;">
                                <i class="la la-twitter mr-1"></i> Twitter
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('blog.detail', $post->slug)) }}&title={{ urlencode($post->title) }}" target="_blank" class="btn btn-sm" style="background-color: #f8e9e9; color: #0077b5;">
                                <i class="la la-linkedin mr-1"></i> LinkedIn
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Author Section -->
                <div class="instructor-wrap mb-4">
                    <h4 class="fs-20 font-weight-bold mb-3">About the Author</h4>
                    <div class="media media-card align-items-center p-4 bg-light rounded" style="border: 1px solid #f0e4e4;">
                        <div class="media-img mr-3">
                            <img src="{{ $author->photo ? asset('upload/instructor_images/' . $author->photo) : asset('upload/no_image.jpg') }}" alt="{{ $author->name ?? 'Unknown Author' }}'s Profile" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #EC5252;">
                        </div>
                        <div class="media-body">
                            <h5 class="fs-16 font-weight-bold mb-1">{{ $author->name ?? 'Unknown Author' }}</h5>
                            <span class="d-block fs-14 text-muted mb-2">{{ $author->email ?? 'No email provided' }}</span>
                            <p class="fs-14 text-gray">{{ $author->bio ?? 'No bio available' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="comments-wrap mb-5" id="comments">
                    <h4 class="fs-20 font-weight-bold mb-4">Comments ({{ $comments->count() }})</h4>
                    <div class="comment-list">
                        @forelse($comments as $comment)
                            <div class="comment mb-4 border-bottom pb-3 shadow-sm rounded" id="comment-{{ $comment->id }}">
                                <div class="media">
                                    <div class="media-img mr-3">
                                        <img src="{{ $comment->user->photo ? Storage::url('upload/user_images/' . $comment->user->photo) : asset('upload/no_image.jpg') }}" alt="{{ $comment->user->name }}'s Profile" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #EC5252;">
                                    </div>
                                    <div class="media-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="fs-15 font-weight-medium mb-1">{{ $comment->user->name }}</h5>
                                            <span class="fs-12 text-muted">{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</span>
                                        </div>
                                        <p class="fs-14 text-gray mb-2 comment-text" id="comment-text-{{ $comment->id }}">{{ $comment->message }}</p>
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
                                                <div class="reply mb-3 shadow-sm rounded">
                                                    <div class="media">
                                                        <div class="media-img mr-3">
                                                            <img src="{{ $reply->user->photo ? Storage::url('upload/user_images/' . $reply->user->photo) : asset('upload/no_image.jpg') }}" alt="{{ $reply->user->name }}'s Profile" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover; border: 2px solid #EC5252;">
                                                        </div>
                                                        <div class="media-body">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <h6 class="fs-14 font-weight-medium mb-1">{{ $reply->user->name }} {{ $reply->user_type === 'App\\Models\\Instructor' ? '(Instructor)' : '' }}</h6>
                                                                <span class="fs-12 text-muted">{{ \Carbon\Carbon::parse($reply->created_at)->diffForHumans() }}</span>
                                                            </div>
                                                            <p class="fs-13 text-gray mb-0">{{ $reply->message }}</p>
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
                                                <button type="submit" class="btn btn-sm btn-primary" style="background-color: #EC5252; border-color: #EC5252;">Submit Reply</button>
                                            </form>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        @empty
                            <p class="fs-14 text-muted">No comments yet. Be the first to comment!</p>
                        @endforelse
                    </div>
                </div>

                <!-- Comment Form -->
                <div class="add-comment-wrap mb-5">
                    <h4 class="fs-20 font-weight-bold mb-4">Leave a Comment</h4>
                    @auth
                        @if(session('success'))
                            <div class="alert alert-success mb-3">{{ session('success') }}</div>
                        @endif
                        <form method="post" class="card card-item p-4 shadow-sm" action="{{ route('comments.store', $post->slug) }}" style="background-color: #ffffff; border-radius: 12px;">
                            @csrf
                            <div class="form-group mb-3">
                                <textarea class="form-control form--control" name="message" placeholder="Write your comment here..." rows="4" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <span class="text-danger fs-13">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button class="btn theme-btn" type="submit" style="background-color: #EC5252; border-color: #EC5252; color: #ffffff;">Submit Comment</button>
                            </div>
                        </form>
                    @else
                        <p class="fs-14 text-muted">Please <a href="{{ route('login') }}" class="text-primary">log in</a> to leave a comment.</p>
                    @endauth
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar mb-5">
                    <h4 class="fs-20 font-weight-bold mb-4">Recent Posts</h4>
                    @forelse($otherBlogs as $blog)
                        <div class="media media-card mb-3 shadow-sm rounded hover-effect" style="background-color: #ffffff; border: 1px solid #f0e4e4;">
                            <a href="{{ route('blog.detail', $blog->slug) }}" class="media-img mr-3">
                                <img loading="lazy" src="{{ $blog->image ? asset('upload/blog-posts/' . $blog->image) : asset('upload/no_image.jpg') }}" alt="{{ $blog->title }}" class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                            </a>
                            <div class="media-body">
                                <h5 class="fs-14 font-weight-bold mb-1">
                                    <a href="{{ route('blog.detail', $blog->slug) }}" class="text-dark hover-text-primary">{{ Str::limit($blog->title, 25) }}</a>
                                </h5>
                                <p class="fs-12 text-muted mb-1">{{ \Carbon\Carbon::parse($blog->created_at)->format('M d, Y') }}</p>
                                <span class="fs-13 text-gray">{{ $blog->author_name }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="fs-14 text-muted">No other posts available.</p>
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
<style>.breadcrumb-area {
    background: linear-gradient(135deg, #f8e9e9 0%, #fff5f5 100%);
    position: relative;
    overflow: hidden;
    border-bottom: 2px solid #EC5252;
}
.breadcrumb-area::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(236, 82, 82, 0.1);
    z-index: 1;
}
.breadcrumb-content {
    position: relative;
    z-index: 2;
}
.card-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #f0e4e4;
}
.card-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(236, 82, 82, 0.15) !important;
}
.media-container {
    background-color: #fff5f5;
    padding: 12px;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.video-wrapper, .image-wrapper {
    position: relative;
    overflow: hidden;
}
.video-overlay, .image-overlay {
    position: relative;
    display: block;
}
.video-overlay::before, .image-overlay::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(236, 82, 82, 0.2);
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 12px;
}
.video-overlay:hover::before, .image-overlay:hover::before {
    opacity: 1;
}
.play-icon, .zoom-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 2.5rem;
    color: #ffffff;
    opacity: 0;
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.video-overlay:hover .play-icon, .image-overlay:hover .zoom-icon {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1.1);
}
.video-wrapper video, .image-wrapper img {
    transition: transform 0.5s ease, filter 0.3s ease;
}
.video-wrapper:hover video, .image-wrapper:hover img {
    transform: scale(1.05);
    filter: brightness(1.1);
}
.hover-text-primary:hover {
    color: #EC5252 !important;
    text-decoration: none;
}
.media-card:hover, .hover-effect:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(236, 82, 82, 0.1);
    background-color: #fff5f5 !important;
}
.comment, .reply {
    transition: background-color 0.3s ease, transform 0.3s ease;
    background-color: #ffffff;
    border-radius: 8px;
}
.comment:hover, .reply:hover {
    background-color: #fff5f5;
    transform: translateX(5px);
}
.theme-btn {
    background-color: #EC5252;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    transition: background-color 0.3s ease, transform 0.3s ease;
}
.theme-btn:hover {
    background-color: #d43f3f;
    transform: translateY(-2px);
}
.social-share a {
    border-radius: 8px;
    transition: transform 0.3s ease, opacity 0.3s ease;
}
.social-share a:hover {
    transform: scale(1.15);
    opacity: 0.85;
}
.form-control {
    border-radius: 8px;
    border: 1px solid #f0e4e4;
    transition: border-color 0.3s ease;
}
.form-control:focus {
    border-color: #EC5252;
    box-shadow: 0 0 0 0.2rem rgba(236, 82, 82, 0.25);
}

@media (max-width: 767px) {
    .media-container {
        flex-direction: column;
    }
    .video-wrapper, .image-wrapper {
        flex: 1 1 100%;
    }
    .video-wrapper video, .image-wrapper img {
        max-height: 300px;
    }
    .card-body {
        padding: 20px;
    }
    .social-share a {
        font-size: 13px;
        padding: 8px 14px;
    }
    .play-icon, .zoom-icon {
        font-size: 2rem;
    }
}</style>
@endsection