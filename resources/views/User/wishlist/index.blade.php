@extends('User.layout.User_layout')

@section('title')
    My Wishlist | Easy Learning
@endsection

@section('userdashboard')
    <!-- Dépendances spécifiques à DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        .wishlist-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .wishlist-table th, .wishlist-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }
        .wishlist-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .wishlist-table tr:hover {
            background-color: #f1f1f1;
        }
        .wishlist-btn i {
            transition: color 0.3s ease;
            color: #F16767;
        }
        .wishlist-btn.wishlisted i {
            color: #F16767;
        }
        .action-btn {
            padding: 5px 10px;
            font-size: 0.9em;
            border-radius: 4px;
            transition: transform 0.2s ease;
        }
        .action-btn:hover {
            transform: translateY(-2px);
        }
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
        .course-title a {
            color: #123458;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .course-title a:hover {
            color: #1a4a7a;
        }
        .price-container {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .rating-container {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .rating-stars i {
            color: #ffc107;
            font-size: 0.9em;
        }
        .instructor-name a {
            color: #555;
            text-decoration: none;
        }
        .instructor-name a:hover {
            color: #123458;
        }
    </style>

    <div class="page-content">
        <!-- Breadcrumb -->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Wishlist</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Your Wishlisted Courses</h4>
                <div class="table-responsive">
                    @if ($wishlistCourses->isEmpty())
                        <div class="text-center py-4">
                            <i class="bx bx-heart text-muted" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mt-2">Your wishlist is empty. Browse courses and add some to see them here!</p>
                            <a href="{{ url('/') }}" class="btn btn-primary mt-2">Browse Courses</a>
                        </div>
                    @else
                        <table id="wishlistTable" class="table table-striped table-bordered wishlist-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Course Title</th>
                                    <th>Instructor</th>
                                    <th>Price</th>
                                    <th>Rating</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($wishlistCourses as $key => $course)
                                    @php
                                        $finalPrice = $course->discount_price !== null
                                            ? max(0, $course->selling_price - $course->discount_price)
                                            : $course->selling_price;
                                        $discount = $course->selling_price && $course->discount_price !== null
                                            ? round(($course->selling_price - $finalPrice) / $course->selling_price * 100)
                                            : 0;
                                        $rating = $course->reviews->avg('rating') ?? 0;
                                        $reviewsCount = $course->reviews->count();
                                    @endphp
                                    <tr data-course-id="{{ $course->id }}" class="animate__animated animate__fadeIn">
                                        <td>{{ $key + 1 }}</td>
                                        <td class="course-title">
                                            <a href="{{ route('course.details', [$course->id, $course->course_name_slug]) }}" 
                                               title="{{ $course->course_title }}">
                                                {{ Str::limit($course->course_title, 40) }}
                                            </a>
                                        </td>
                                        <td class="instructor-name">
                                            <a href="{{ route('instructor.details', $course->courseable->id) }}">
                                                {{ Str::limit($course->courseable->name ?? 'Unknown Instructor', 25) }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="price-container">
                                                <span class="fw-bold text-primary">${{ number_format($finalPrice, 2) }}</span>
                                                @if ($discount > 0)
                                                    <span class="text-muted text-decoration-line-through fs-6">
                                                        ${{ number_format($course->selling_price, 2) }}
                                                    </span>
                                                    <span class="badge bg-success-subtle text-success">
                                                        Save {{ $discount }}%
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="rating-container">
                                                <span class="fw-medium">{{ number_format($rating, 1) }}</span>
                                                <div class="rating-stars">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="bx {{ $i <= floor($rating) ? 'bxs-star' : 'bx-star' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="text-muted">({{ number_format($reviewsCount) }})</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <form action="{{ route('cart.add', $course->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success action-btn" 
                                                            title="Add to Cart">
                                                        <i class="bx bx-cart"></i>
                                                    </button>
                                                </form>
                                                <button class="wishlist-btn btn btn-danger action-btn wishlisted" 
                                                        data-course-id="{{ $course->id }}" 
                                                        title="Remove from Wishlist">
                                                    <i class="bx bx-heart"></i>
                                                </button>
                                            </div>
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

    <!-- Scripts spécifiques à DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#wishlistTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "lengthMenu": [5, 10, 25, 50],
                "pageLength": 10,
                "language": {
                    "search": "Search courses:",
                    "lengthMenu": "Show _MENU_ courses",
                    "info": "Showing _START_ to _END_ of _TOTAL_ courses",
                    "paginate": {
                        "previous": "Previous",
                        "next": "Next"
                    }
                }
            });

            $('.wishlist-btn').on('click', function(e) {
                e.preventDefault();
                var $button = $(this);
                var courseId = $button.data('course-id');
                var url = '/wishlist/remove/' + courseId;

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            $button.removeClass('wishlisted');
                            var row = $button.closest('tr');
                            row.addClass('animate__animated animate__fadeOut');
                            setTimeout(() => {
                                table.row(row).remove().draw();
                                if (table.data().length === 0) {
                                    location.reload();
                                }
                            }, 300);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'An error occurred.');
                    }
                });
            });

            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif
        });
    </script>
@endsection