@extends('admin.layout.Admin_layout')

@section('admin')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <div class="page-content">
        <!-- Breadcrumb -->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/admin/dashboard') }}" class="text-primary"><i class="bx bx-home-alt"></i> Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">All Blog Posts</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Table -->
                <div class="table-responsive">
                    <table id="blogPostsTable" class="table table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Post Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Instructor</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($posts as $key => $item)
                                <tr>
                                    <td>{{ $posts->firstItem() + $key }}</td>
                                    <td>
                                        @if ($item->image)
                                            <img src="{{ asset('upload/blog-posts/' . $item->image) }}" alt="{{ $item->title }}" style="width: 70px; height: 40px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('upload/no_image.jpg') }}" alt="No Image" style="width: 70px; height: 40px;">
                                        @endif
                                    </td>
                                    <td title="{{ $item->title }}">{{ Str::limit($item->title, 30, '...') }}</td>
                                    <td>
                                        @if ($item->category)
                                            {{ $item->category->name }}
                                        @elseif ($item->blog_category_id)
                                            <span class="text-danger">Invalid ID: {{ $item->blog_category_id }}</span>
                                        @else
                                            No Category
                                        @endif
                                    </td>
                                    <td>{{ $item->instructor->name ?? 'No Instructor' }}</td>
                                    <td>
                                        <span class="badge {{ $item->status == 'active' ? 'bg-primary' : 'bg-secondary' }} py-1 px-2">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.blog-posts.toggle', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-sm {{ $item->status == 'active' ? 'btn-warning' : 'btn-success' }} px-3"
                                                    onclick="return confirm('Are you sure you want to change the status of this post?');">
                                                {{ $item->status == 'active' ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No blog posts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#blogPostsTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 10,
                paging: false, // Laravel pagination handles paging
                searching: true,
                lengthChange: true,
                info: true,
                autoWidth: false,
                responsive: true,
                language: {
                    emptyTable: 'No blog posts available',
                    search: 'Search:',
                    searchPlaceholder: 'Search posts...'
                },
                columnDefs: [
                    { width: '5%', targets: 0 },  // Sl
                    { width: '10%', targets: 1 }, // Post Image
                    { width: '25%', targets: 2 }, // Title
                    { width: '15%', targets: 3 }, // Category
                    { width: '15%', targets: 4 }, // Instructor
                    { width: '10%', targets: 5 }, // Status
                    { width: '15%', targets: 6, orderable: false } // Action
                ]
            });
        });
    </script>
@endsection