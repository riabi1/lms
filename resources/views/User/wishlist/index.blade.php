@extends('User.layout.User_layout')

@section('title')
    My Wishlist | Easy Learning
@endsection

@section('userdashboard')
    <!-- Styles (unchanged, included for context) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        /* Existing styles unchanged */
        .wishlist-btn i {
            transition: color 0.3s ease, transform 0.2s ease;
        }
        .wishlist-btn.wishlisted i {
            color: #F16767;
            transform: scale(1.1);
        }
        .wishlist-btn:not(.wishlisted) i {
            color: #6c757d;
        }
    </style>

    <div class="page-content">
        <!-- Breadcrumb (unchanged) -->
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
                <div class="table-responsive" id="wishlist-container">
                    @if ($wishlistCourses->isEmpty())
                        <div class="text-center py-4" id="empty-wishlist">
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
                                @php
                                    $cartItems = Auth::check() ? App\Models\CartItem::where('user_id', Auth::id())
                                        ->where('cartable_type', 'App\Models\Course')
                                        ->pluck('cartable_id')->toArray() : [];
                                    $tempCart = json_decode(request()->cookie('tempCart', '[]'), true);
                                    $tempCartIds = array_column($tempCart, 'courseId');
                                @endphp
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
                                        $isInCart = in_array($course->id, $cartItems) || in_array($course->id, $tempCartIds);
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
                                                @if ($isInCart)
                                                    <button class="remove-from-cart-btn btn btn-warning action-btn" 
                                                            data-course-id="{{ $course->id }}" 
                                                            title="Remove from Cart">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                @else
                                                    <form class="add-to-cart-form" data-course-id="{{ $course->id }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success action-btn" 
                                                                title="Add to Cart">
                                                            <i class="bx bx-cart"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <button class="wishlist-btn btn btn-danger action-btn wishlisted" 
                                                        data-course-id="{{ $course->id }}" 
                                                        title="Remove from Wishlist">
                                                    <i class="bx bxs-heart"></i>
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

    <!-- Scripts -->
    @push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        (function($) { // Wrap in IIFE to avoid jQuery conflicts
            $(document).ready(function() {
                // Debug: Log jQuery version and dependencies
                console.log('jQuery version:', $.fn.jquery);
                console.log('DataTables loaded:', typeof $.fn.DataTable !== 'undefined');
                console.log('Toastr loaded:', typeof toastr !== 'undefined');

                // Configure Toastr
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 3000
                };

                // Initialize DataTable
                var table = $('#wishlistTable').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    lengthMenu: [5, 10, 25, 50],
                    pageLength: 10,
                    language: {
                        search: 'Search courses:',
                        lengthMenu: 'Show _MENU_ courses',
                        info: 'Showing _START_ to _END_ of _TOTAL_ courses',
                        paginate: {
                            previous: 'Previous',
                            next: 'Next'
                        }
                    }
                });

                // Debug: Log when wishlist button is clicked
                $(document).on('click', '.wishlist-btn', function(e) {
                    e.preventDefault();
                    var $button = $(this);
                    var courseId = $button.data('course-id');
                    var isWishlisted = $button.hasClass('wishlisted');
                    var url = isWishlisted ? '/wishlist/remove/' + courseId : '/wishlist/add/' + courseId;

                    console.log('Wishlist button clicked:', {
                        courseId: courseId,
                        isWishlisted: isWishlisted,
                        url: url
                    });

                    $.ajax({
                        url: url,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {},
                        success: function(response) {
                            console.log('AJAX success:', response);
                            if (response.status === 'success') {
                                toastr.success(response.message);
                                if (isWishlisted) {
                                    // Remove from wishlist
                                    $button.removeClass('wishlisted');
                                    $button.find('i').removeClass('bxs-heart').addClass('bx-heart');
                                    $button.attr('title', 'Add to Wishlist');
                                    var row = $button.closest('tr');
                                    row.addClass('animate__animated animate__fadeOut');
                                    setTimeout(() => {
                                        table.row(row).remove().draw();
                                        if (table.data().length === 0) {
                                            $('#wishlist-container').html(`
                                                <div class="text-center py-4" id="empty-wishlist">
                                                    <i class="bx bx-heart text-muted" style="font-size: 2.5rem;"></i>
                                                    <p class="text-muted mt-2">Your wishlist is empty. Browse courses and add some to see them here!</p>
                                                    <a href="{{ url('/') }}" class="btn btn-primary mt-2">Browse Courses</a>
                                                </div>
                                            `);
                                        }
                                    }, 300);
                                } else {
                                    // Add to wishlist
                                    $button.addClass('wishlisted');
                                    $button.find('i').removeClass('bx-heart').addClass('bxs-heart');
                                    $button.attr('title', 'Remove from Wishlist');
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', {
                                status: xhr.status,
                                response: xhr.responseJSON,
                                error: error
                            });
                            if (xhr.status === 419) {
                                toastr.error('Session expired. Please refresh the page or log in again.');
                            } else if (xhr.status === 401) {
                                toastr.error(xhr.responseJSON.message || 'You must be logged in.');
                                if (xhr.responseJSON.redirect) {
                                    setTimeout(() => {
                                        window.location.href = xhr.responseJSON.redirect;
                                    }, 2000);
                                }
                            } else {
                                toastr.error(xhr.responseJSON?.message || 'An error occurred.');
                            }
                        }
                    });
                });

                // Handle Add to Cart (unchanged, included for completeness)
                $('.add-to-cart-form').on('submit', function(e) {
                    e.preventDefault();
                    var $form = $(this);
                    var courseId = $form.data('course-id');
                    var url = '{{ route("cart.add", ":id") }}'.replace(':id', courseId);

                    console.log('Add to cart form submitted:', { courseId: courseId, url: url });

                    $.ajax({
                        url: url,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {},
                        success: function(response) {
                            console.log('Add to cart success:', response);
                            if (response.success) {
                                toastr.success(response.message);
                                var $td = $form.closest('td');
                                $form.replaceWith(`
                                    <button class="remove-from-cart-btn btn btn-warning action-btn" 
                                            data-course-id="${courseId}" 
                                            title="Remove from Cart">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                `);
                            } else if (response.info) {
                                toastr.info(response.info);
                            }
                        },
                        error: function(xhr) {
                            console.error('Add to cart error:', xhr.responseJSON);
                            toastr.error(xhr.responseJSON?.error || 'An error occurred while adding to cart.');
                        }
                    });
                });

                // Handle Remove from Cart (unchanged)
                $(document).on('click', '.remove-from-cart-btn', function(e) {
                    e.preventDefault();
                    var $button = $(this);
                    var courseId = $button.data('course-id');
                    var url = '{{ route("cart.remove", ":id") }}'.replace(':id', courseId);

                    console.log('Remove from cart clicked:', { courseId: courseId, url: url });

                    $.ajax({
                        url: url,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {},
                        success: function(response) {
                            console.log('Remove from cart success:', response);
                            if (response.success) {
                                toastr.success(response.message);
                                var $td = $button.closest('td');
                                $button.replaceWith(`
                                    <form class="add-to-cart-form" data-course-id="${courseId}">
                                        @csrf
                                        <button type="submit" class="btn btn-success action-btn" title="Add to Cart">
                                            <i class="bx bx-cart"></i>
                                        </button>
                                    </form>
                                `);
                            }
                        },
                        error: function(xhr) {
                            console.error('Remove from cart error:', xhr.responseJSON);
                            if (xhr.responseJSON.redirect) {
                                toastr.error(xhr.responseJSON.message);
                                setTimeout(() => {
                                    window.location.href = xhr.responseJSON.redirect;
                                }, 2000);
                            } else {
                                toastr.error(xhr.responseJSON?.message || 'An error occurred while removing from cart.');
                            }
                        }
                    });
                });

                // Session-based success message
                @if (session('success'))
                    toastr.success("{{ session('success') }}");
                @endif
            });
        })(jQuery); // Pass jQuery to IIFE to ensure correct $
    </script>
    @endpush
@endsection