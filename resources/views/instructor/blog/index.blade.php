@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">All Blog Posts</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('instructor.blog.create') }}" class="btn btn-primary px-5">Add Post</a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Post Image</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Action</th>
                            <th>Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($posts as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <img src="{{ $item->image ? asset('upload/blog-posts/' . $item->image) : asset('upload/no_image.jpg') }}" 
                                         alt="{{ $item->title }}" 
                                         class="img-fluid rounded post-img">
                                </td>
                                <td>{{ $item->title }}</td>
                                <td>{!! Str::limit(strip_tags($item->content), 50) !!}</td>
                                <td>
                                    @if ($item->category)
                                        {{ $item->category->name }}
                                    @elseif ($item->blog_category_id)
                                        Invalid Category ID: {{ $item->blog_category_id }}
                                    @else
                                        No Category Assigned
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status == 'active')
                                        <span class="badge bg-primary">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('instructor.blog.edit', $item->id) }}" class="btn btn-info px-3">Edit</a>
                                    <form action="{{ route('instructor.blog.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger px-3">Delete</button>
                                    </form>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#comments-{{ $item->id }}" 
                                            aria-expanded="{{ request()->query('post_id') == $item->id ? 'true' : 'false' }}" 
                                            aria-controls="comments-{{ $item->id }}">
                                        View Comments ({{ $item->comments->count() }})
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="8" class="p-0">
                                    <div class="collapse {{ request()->query('post_id') == $item->id ? 'show' : '' }}" id="comments-{{ $item->id }}">
                                        <div class="card card-body comment-section">
                                            @if($item->comments->count() > 0)
                                                @foreach($item->comments as $comment)
                                                    <div class="comment {{ request()->query('comment_id') == $comment->id ? 'bg-light' : '' }}">
                                                        <div class="d-flex align-items-start">
                                                            <img src="{{ $comment->user && $comment->user->photo ? asset('upload/user_images/' . $comment->user->photo) : asset('upload/no_image.jpg') }}" 
                                                                 alt="{{ $comment->user->name ?? 'Anonymous' }}'s Profile" 
                                                                 class="comment-user-img">
                                                            <div class="comment-content">
                                                                <h5 class="comment-name">{{ $comment->user->name ?? 'Anonymous' }}</h5>
                                                                <p class="comment-text">{{ $comment->message }}</p>
                                                                <span class="comment-date">{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</span>
                                                                <form action="{{ route('instructor.comments.reply', $comment->id) }}" method="POST" class="reply-form">
                                                                    @csrf
                                                                    <div class="form-group">
                                                                        <textarea class="form-control" name="message" placeholder="Write your reply..." rows="2" required></textarea>
                                                                        @error('message')
                                                                            <span class="text-danger comment-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                    <button type="submit" class="btn btn-sm btn-primary">Reply</button>
                                                                </form>
                                                                @if($comment->replies->count() > 0)
                                                                    <div class="replies">
                                                                        @foreach($comment->replies as $reply)
                                                                            @if($reply->approved)
                                                                                <div class="reply">
                                                                                    <div class="d-flex align-items-start">
                                                                                        <img src="{{ $reply->user && $reply->user->photo ? asset('upload/' . ($reply->user_type === 'App\\Models\\Instructor' ? 'instructor_images' : 'user_images') . '/' . $reply->user->photo) : asset('upload/no_image.jpg') }}" 
                                                                                             alt="{{ $reply->user->name ?? 'Anonymous' }}'s Profile" 
                                                                                             class="reply-user-img">
                                                                                        <div class="reply-content">
                                                                                            <h6 class="reply-name">{{ $reply->user->name ?? 'Anonymous' }} {{ $reply->user_type === 'App\\Models\\Instructor' ? '(Instructor)' : '' }}</h6>
                                                                                            <p class="reply-text">{{ $reply->message }}</p>
                                                                                            <span class="reply-date">{{ \Carbon\Carbon::parse($reply->created_at)->diffForHumans() }}</span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="comment-empty">No comments yet.</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No blog posts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery and DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            "order": [[0, "asc"]],
            "pageLength": 10
        });

        @if (request()->query('comment_id'))
            $('html, body').animate({
                scrollTop: $('.bg-light').offset().top - 100
            }, 500);
        @endif
    });
</script>

<style>
/* Post Image */
.post-img {
    width: 100px;
    height: 60px;
    object-fit: cover;
}

/* Comment Section */
.comment-section {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
}

.comment {
    padding: 15px;
    margin-bottom: 15px;
    border-bottom: 1px solid #e0e0e0;
}

.comment:last-child {
    border-bottom: none;
}

.comment.bg-light {
    background: #e9ecef;
    border-radius: 5px;
}

.comment-user-img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Fallback for shadow-sm */
    margin-right: 15px;
}

.comment-content {
    flex: 1;
}

.comment-name {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.comment-text {
    font-size: 14px;
    color: #555;
    margin-bottom: 5px;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 4; /* Limit to 4 lines */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.comment-date {
    font-size: 12px;
    color: #888;
}

.comment-error {
    font-size: 12px;
    display: block;
    margin-top: 5px;
}

.reply-form {
    margin-top: 10px;
}

.reply-form .form-control {
    font-size: 14px;
    resize: none;
}

.reply {
    padding: 10px 0 10px 65px; /* Indent to align with comment image */
    border-top: 1px solid #e9ecef;
    margin-top: 10px;
}

.reply-user-img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Fallback for shadow-sm */
    margin-right: 15px;
}

.reply-content {
    flex: 1;
}

.reply-name {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.reply-text {
    font-size: 13px;
    color: #555;
    margin-bottom: 5px;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 3; /* Limit to 3 lines */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.reply-date {
    font-size: 12px;
    color: #888;
}

.comment-empty {
    font-size: 14px;
    color: #888;
    text-align: center;
    margin: 0;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .post-img {
        width: 80px;
        height: 50px;
    }

    .comment-user-img {
        width: 40px;
        height: 40px;
    }

    .reply-user-img {
        width: 30px;
        height: 30px;
    }

    .comment {
        padding: 10px;
    }

    .comment-name {
        font-size: 14px;
    }

    .comment-text {
        font-size: 13px;
        -webkit-line-clamp: 3; /* Reduce to 3 lines on mobile */
    }

    .reply {
        padding-left: 50px; /* Adjust indentation for smaller images */
    }

    .reply-name {
        font-size: 13px;
    }

    .reply-text {
        font-size: 12px;
        -webkit-line-clamp: 2; /* Reduce to 2 lines on mobile */
    }
}
</style>

@endsection