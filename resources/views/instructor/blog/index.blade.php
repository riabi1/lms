@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<div class="page-content">
    <!-- Breadcrumb -->
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
    <!-- End Breadcrumb -->

    <div class="card">
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
                                    @if ($item->image)
                                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title }}" style="width: 70px; height: 40px;">
                                    @else
                                        <img src="{{ asset('upload/no_image.jpg') }}" alt="No Image" style="width: 70px; height: 40px;">
                                    @endif
                                </td>
                                <td>{{ $item->title }}</td>
                                <td>{{ Str::limit($item->content, 50) }}</td>
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
                                        <div class="card card-body">
                                            @if($item->comments->count() > 0)
                                                @foreach($item->comments as $comment)
                                                    <div class="comment mb-3 border-bottom pb-3 {{ request()->query('comment_id') == $comment->id ? 'bg-light' : '' }}">
                                                        <div class="media">
                                                            <div class="mr-3">
                                                                <img src="{{ $comment->user->photo ? asset('storage/upload/user_images/' . $comment->user->photo) : asset('images/default-avatar.jpg') }}" alt="{{ $comment->user->name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                                            </div>
                                                            <div class="media-body">
                                                                <h5 class="fs-15 font-weight-medium mb-1">{{ $comment->user->name }}</h5>
                                                                <p class="fs-14 text-gray mb-2">{{ $comment->message }}</p>
                                                                <span class="fs-12 text-muted">{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</span>
                                                                <!-- Reply Form -->
                                                                <form action="{{ route('instructor.comments.reply', $comment->id) }}" method="POST" class="mt-2">
                                                                    @csrf
                                                                    <div class="form-group mb-2">
                                                                        <textarea class="form-control" name="message" placeholder="Write your reply..." rows="2" required></textarea>
                                                                        @error('message')
                                                                            <span class="text-danger fs-13">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                    <button type="submit" class="btn btn-sm btn-primary">Reply</button>
                                                                </form>
                                                                <!-- Display Existing Replies -->
                                                                @if($comment->replies->count() > 0)
                                                                    <div class="replies mt-3">
                                                                        @foreach($comment->replies as $reply)
                                                                            @if($reply->approved)
                                                                                <div class="reply mb-2">
                                                                                    <div class="media">
                                                                                        <div class="mr-3">
                                                                                            <img src="{{ $reply->user->photo ? asset('storage/upload/' . ($reply->user_type === 'App\\Models\\Instructor' ? 'instructor_images' : 'user_images') . '/' . $reply->user->photo) : asset('images/default-avatar.jpg') }}" alt="{{ $reply->user->name }}" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
                                                                                        </div>
                                                                                        <div class="media-body">
                                                                                            <h6 class="fs-14 font-weight-medium mb-1">{{ $reply->user->name }} {{ $reply->user_type === 'App\\Models\\Instructor' ? '(Instructor)' : '' }}</h6>
                                                                                            <p class="fs-13 text-gray mb-0">{{ $reply->message }}</p>
                                                                                            <span class="fs-12 text-muted">{{ \Carbon\Carbon::parse($reply->created_at)->diffForHumans() }}</span>
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
                                                <p class="fs-14 text-muted">No comments yet.</p>
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

        // Scroll to highlighted comment if present
        @if (request()->query('comment_id'))
            $('html, body').animate({
                scrollTop: $('.bg-light').offset().top - 100
            }, 500);
        @endif
    });
</script>

@endsection