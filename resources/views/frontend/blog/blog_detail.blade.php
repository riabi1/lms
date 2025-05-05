@extends('frontend.master')

@section('title')
    {{ $post->title ?? 'Blog Details' }} | Easy Learning
@endsection

@section('home')
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

<section class="blog-area pt-5 pb-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-item mb-4 shadow-sm">
                    <div class="card-body p-4">
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="img-fluid rounded mb-4" style="max-height: 400px; object-fit: cover; width: 100%;">
                        <div class="post-content">{!! $post->content !!}</div>
                        <div class="section-block my-4"></div>
                        <h4 class="fs-18 font-weight-bold mb-3">Tags</h4>
                        <ul class="generic-list-item generic-list-item-boxed d-flex flex-wrap fs-14">
                            <li class="mr-2 mb-2"><a href="#" class="btn btn-sm btn-outline-secondary">{{ $category->name ?? 'Uncategorized' }}</a></li>
                        </ul>
                    </div>
                </div>

                <div class="instructor-wrap mb-4">
                    <h4 class="fs-20 font-weight-bold mb-3">About the Author</h4>
                    <div class="media media-card align-items-center p-3 bg-light rounded">
                        <div class="media-img mr-3">
                            <img src="{{ $author->photo ? asset('upload/instructor_images/' . $author->photo) : asset('upload/no_image.jpg') }}" alt="{{ $author->name ?? 'Unknown Author' }}'s Profile" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <div class="media-body">
                            <h5 class="fs-16 font-weight-bold mb-1">{{ $author->name ?? 'Unknown Author' }}</h5>
                            <span class="d-block fs-14 text-muted mb-2">{{ $author->email ?? 'No email provided' }}</span>
                            <p class="fs-14 text-gray">{{ $author->bio ?? 'No bio available' }}</p>
                        </div>
                    </div>
                </div>

                <div class="comments-wrap mb-5" id="comments">
                    <h4 class="fs-20 font-weight-bold mb-4">Comments ({{ $comments->count() }})</h4>
                    <div class="comment-list">
                        @forelse($comments as $comment)
                            <div class="comment mb-4 border-bottom pb-3" id="comment-{{ $comment->id }}">
                                <div class="media">
                                    <div class="media-img mr-3">
                                        <img src="{{ $comment->user->photo ? Storage::url('upload/user_images/' . $comment->user->photo) : asset('upload/no_image.jpg') }}" alt="{{ $comment->user->name }}'s Profile" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
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
                                                <div class="reply mb-3">
                                                    <div class="media">
                                                        <div class="media-img mr-3">
                                                            <img src="{{ $reply->user->photo ? Storage::url('upload/user_images/' . $reply->user->photo) : asset('upload/no_image.jpg') }}" alt="{{ $reply->user->name }}'s Profile" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
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
                                                <button type="submit" class="btn btn-sm btn-primary">Submit Reply</button>
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

                <div class="add-comment-wrap mb-5">
                    <h4 class="fs-20 font-weight-bold mb-4">Leave a Comment</h4>
                    @auth
                        @if(session('success'))
                            <div class="alert alert-success mb-3">{{ session('success') }}</div>
                        @endif
                        <form method="post" class="card card-item p-4 shadow-sm" action="{{ route('comments.store', $post->slug) }}">
                            @csrf
                            <div class="form-group mb-3">
                                <textarea class="form-control form--control" name="message" placeholder="Write your comment here..." rows="4" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <span class="text-danger fs-13">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button class="btn theme-btn" type="submit">Submit Comment</button>
                            </div>
                        </form>
                    @else
                        <p class="fs-14 text-muted">Please <a href="{{ route('login') }}" class="text-primary">log in</a> to leave a comment.</p>
                    @endauth
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sidebar mb-5">
                    <h4 class="fs-20 font-weight-bold mb-4">Recent Posts</h4>
                    @forelse($otherBlogs as $blog)
                        <div class="media media-card mb-3 shadow-sm">
                            <a href="{{ route('blog.detail', $blog->slug) }}" class="media-img mr-3">
                                <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
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

<script>
    function toggleReplyForm(commentId) {
        const form = document.getElementById(`reply-form-${commentId}`);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
</script>

<style>
    .hover-text-primary:hover {
        color: #007bff !important;
        text-decoration: none;
    }
    .media-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
</style>
@endsection
