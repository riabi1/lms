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
                            <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Comments & Replies</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-body">
                @if (session('message'))
                    <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show mb-4" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Table -->
                <div class="table-responsive">
                    <table id="commentsTable" class="table table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Post</th>
                                <th>User</th>
                                <th>Type</th>
                                <th>Parent</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                                <tr>
                                    <td>{{ $item['id'] }}</td>
                                    <td title="{{ $item['post_title'] }}">{{ Str::limit($item['post_title'], 20, '...') }}</td>
                                    <td title="{{ $item['user_name'] }}">{{ Str::limit($item['user_name'], 15, '...') }}</td>
                                    <td>
                                        <span class="badge {{ $item['type'] === 'comment' ? 'bg-primary' : 'bg-info' }} text-capitalize">
                                            {{ $item['type'] }}
                                        </span>
                                    </td>
                                    <td title="{{ $item['parent_comment'] ?? '' }}">
                                        {{ $item['parent_comment'] ? Str::limit($item['parent_comment'], 20, '...') : '—' }}
                                    </td>
                                    <td title="{{ $item['message'] }}">{{ Str::limit($item['message'], 30, '...') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item['created_at'])->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge {{ $item['approved'] ? 'bg-success' : 'bg-warning' }} text-capitalize">
                                            {{ $item['approved'] ? 'Approved' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <form action="{{ route('admin.comments.toggle', [$item['id'], $item['type']]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" 
                                                        class="btn btn-sm {{ $item['approved'] ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                        title="{{ $item['approved'] ? 'Disapprove' : 'Approve' }}"
                                                        onclick="return confirm('Are you sure you want to {{ $item['approved'] ? 'disapprove' : 'approve' }} this {{ $item['type'] }}?');">
                                                    <i class="lni {{ $item['approved'] ? 'lni-close' : 'lni-checkmark' }}"></i>
                                                </button>
                                            </form>
                                            <a href="#" 
                                               class="btn btn-sm btn-primary" 
                                               data-bs-toggle="modal" 
                                               data-bs-target="#viewModal-{{ $item['id'] }}-{{ $item['type'] }}"
                                               title="View">
                                                <i class="lni lni-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.comments.destroy', [$item['id'], $item['type']]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger"
                                                        title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this {{ $item['type'] }}?');">
                                                    <i class="lni lni-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Modal for Viewing Details -->
                                        <div class="modal fade" 
                                             id="viewModal-{{ $item['id'] }}-{{ $item['type'] }}" 
                                             tabindex="-1" 
                                             aria-labelledby="viewModalLabel-{{ $item['id'] }}-{{ $item['type'] }}" 
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="viewModalLabel-{{ $item['id'] }}-{{ $item['type'] }}">
                                                            {{ ucfirst($item['type']) }} Details
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-3 fw-bold">Blog Post:</div>
                                                            <div class="col-md-9">{{ $item['post_title'] }}</div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-md-3 fw-bold">User:</div>
                                                            <div class="col-md-9">{{ $item['user_name'] }}</div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-md-3 fw-bold">Type:</div>
                                                            <div class="col-md-9 text-capitalize">{{ $item['type'] }}</div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-md-3 fw-bold">Parent Comment:</div>
                                                            <div class="col-md-9">{{ $item['parent_comment'] ?? 'None' }}</div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-md-3 fw-bold">Message:</div>
                                                            <div class="col-md-9">{!! nl2br(e($item['message'])) !!}</div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-md-3 fw-bold">Created At:</div>
                                                            <div class="col-md-9">{{ \Carbon\Carbon::parse($item['created_at'])->format('d M Y H:i') }}</div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3 fw-bold">Status:</div>
                                                            <div class="col-md-9 text-capitalize">{{ $item['approved'] ? 'Approved' : 'Pending' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No comments or replies found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
            $('#commentsTable').DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
                language: {
                    emptyTable: 'No comments or replies available in table',
                    search: 'Search:',
                    searchPlaceholder: 'Search comments...'
                },
                order: [[0, 'desc']],
                columnDefs: [
                    { width: '5%', targets: 0 },  // ID
                    { width: '15%', targets: 1 }, // Post
                    { width: '10%', targets: 2 }, // User
                    { width: '8%', targets: 3 },  // Type
                    { width: '15%', targets: 4 }, // Parent
                    { width: '20%', targets: 5 }, // Message
                    { width: '12%', targets: 6 }, // Date
                    { width: '8%', targets: 7 },  // Status
                    { width: '12%', targets: 8, orderable: false } // Actions
                ]
            });
        });
    </script>
@endsection