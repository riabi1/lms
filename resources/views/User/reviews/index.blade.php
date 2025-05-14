@extends('User.layout.User_layout')

@section('title')
    My Reviews | Easy Learning
@endsection

@section('userdashboard')
    <!-- Dépendances spécifiques à DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        .rating-stars i {
            font-size: 1.2em;
        }
        /* Styles pour DataTables */
        .dataTables_wrapper .dataTables_filter {
            float: right;
            margin-bottom: 15px;
            display: block !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            margin-left: 10px;
            width: 200px;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 5px 10px;
            margin: 0 2px;
            border-radius: 4px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #e9ecef;
            border: 1px solid #ddd;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>

    <div class="page-content">
        <!-- Breadcrumb -->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Reviews</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">My Reviews</h4>
                <div class="table-responsive">
                    @if ($reviews->isEmpty())
                        <p class="text-muted">You haven’t submitted any reviews yet.</p>
                    @else
                        <table id="reviewsTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Course</th>
                                    <th>Instructor</th>
                                    <th>Comment</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reviews as $key => $review)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $review->course ? $review->course->course_name : 'N/A' }}</td>
                                        <td>{{ $review->instructor ? $review->instructor->name : 'N/A' }}</td>
                                        <td>{{ $review->comment }}</td>
                                        <td class="rating-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="bx bxs-star {{ $i <= $review->rating ? 'text-warning' : 'text-secondary' }}"></i>
                                            @endfor
                                        </td>
                                        <td>
                                            @if ($review->status)
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($review->status == 0)
                                                <a href="{{ route('user.reviews.edit', $review->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form action="{{ route('user.reviews.destroy', $review->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            @else
                                                <span class="text-muted">No actions available</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts spécifiques -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#reviewsTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "lengthMenu": [5, 10, 25, 50],
                "pageLength": 10,
                "language": {
                    "search": "Search reviews:",
                    "lengthMenu": "Show _MENU_ entries per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ reviews",
                    "paginate": {
                        "previous": "Previous",
                        "next": "Next"
                    }
                }
            });

            @if (session('message'))
                toastr.options = { "closeButton": true, "progressBar": true };
                toastr["{{ session('alert-type', 'success') }}"]("{{ session('message') }}");
            @endif
        });
    </script>
@endsection