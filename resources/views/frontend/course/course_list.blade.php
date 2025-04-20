@extends('frontend.master')

@section('title')
    Course List | Easy Learning
@endsection

@section('home')
<!-- ================================
    START BREADCRUMB AREA
================================= -->
<section class="breadcrumb-area section-padding img-bg-2">
    <div class="overlay"></div>
    <div class="container">
        <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
            <div class="section-heading">
                <h2 class="section__title text-white">Course List</h2>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li>Course List</li>
            </ul>
        </div><!-- end breadcrumb-content -->
    </div><!-- end container -->
</section><!-- end breadcrumb-area -->
<!-- ================================
    END BREADCRUMB AREA
================================= -->

<!--======================================
        START COURSE AREA
======================================-->
<style>
.wishlist-btn i {
    transition: color 0.3s ease;
}
.wishlist-btn.wishlisted i {
    color: #F16767;
}
.cart-message {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1000;
    padding: 10px;
    border-radius: 4px;
    max-width: 300px;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
.alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
}
.alert-success {
    background-color: #d4edda;
    color: #155724;
}
.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}
</style>

<section class="course-area section--padding">
    <div class="container">
        <!-- Formulaire de filtration -->
        <div class="filter-bar mb-4">
            <form method="GET" action="{{ route('course.list') }}" class="d-flex flex-wrap align-items-center justify-content-between w-100">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="select-container select--container mr-3 mb-2">
                        <select name="category_id" class="select-container-select" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="select-container select--container mr-3 mb-2">
                        <select name="label" class="select-container-select" onchange="this.form.submit()">
                            <option value="">All Levels</option>
                            <option value="Beginner" {{ request('label') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="Intermediate" {{ request('label') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="Expert" {{ request('label') == 'Expert' ? 'selected' : '' }}>Expert</option>
                        </select>
                    </div>
                    <div class="select-container select--container mr-3 mb-2">
                        <select name="rating" class="select-container-select" onchange="this.form.submit()">
                            <option value="">All Ratings</option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4.0 & Up</option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3.0 & Up</option>
                            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2.0 & Up</option>
                        </select>
                    </div>
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input" id="bestseller" name="bestseller" value="1" {{ request('bestseller') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="custom-control-label" for="bestseller">Bestseller Only</label>
                    </div>
                </div>
                <p class="fs-14">We found <span class="text-black">{{ $courses->total() }}</span> courses available for you</p>
            </form>
        </div><!-- end filter-bar -->

        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    @foreach ($courses as $course)
                        @php
                            $finalPrice = $course->discount_price !== null
                                ? max(0, $course->selling_price - $course->discount_price)
                                : $course->selling_price;
                            $discountPercentage = ($course->selling_price > 0 && $course->discount_price !== null)
                                ? round(($course->discount_price / $course->selling_price) * 100)
                                : 0;
                            $instructor = $course->courseable instanceof \App\Models\Instructor ? $course->courseable : null;
                            $isWishlisted = auth()->check() && \App\Models\Wishlist::where('trackable_type', 'App\Models\User')
                                ->where('trackable_id', auth()->id())
                                ->where('course_id', $course->id)
                                ->exists();
                            $isInCart = \Darryldecode\Cart\Facades\CartFacade::get($course->id) !== null;
                        @endphp
                        <div class="col-lg-4 col-md-6 responsive-column-half">
                            <div class="card card-item card-preview" data-tooltip-content="#tooltip_content_{{ $course->id }}">
                                <div class="card-image">
                                    <a href="{{ route('course.details', [$course->id, $course->course_name_slug]) }}" class="d-block">
                                        <img class="card-img-top lazy" src="{{ asset('storage/upload/course_images/thumbnail/' . $course->course_image) }}" alt="{{ $course->course_title }}" onerror="this.src='{{ asset('images/default-course.jpg') }}'">
                                    </a>
                                    <div class="course-badge-labels">
                                        @if ($course->bestseller == 1)
                                            <div class="course-badge">Bestseller</div>
                                        @endif
                                        @if ($course->discount_price !== null && $course->discount_price < $course->selling_price)
                                            <div class="course-badge blue">-{{ $discountPercentage }}%</div>
                                        @elseif ($course->discount_price === null)
                                            <div class="course-badge blue">New</div>
                                        @endif
                                    </div>
                                </div><!-- end card-image -->
                                <div class="card-body">
                                    <h5 class="card-title pb-1">
                                        <a href="{{ route('course.details', [$course->id, $course->course_name_slug]) }}">{{ $course->course_title }}</a>
                                    </h5>
                                    <p class="card-text">
                                        @if ($instructor)
                                            <a href="{{ route('instructor.details', $instructor->id) }}">{{ $instructor->name }}</a>
                                        @else
                                            Unknown Instructor
                                        @endif
                                    </p>
                                    <div class="rating-wrap d-flex align-items-center py-2">
                                        <div class="review-stars">
                                            <span class="rating-number">{{ number_format($course->rating, 1) }}</span>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="la la-star{{ $i <= floor($course->rating) ? '' : '-o' }}"></span>
                                            @endfor
                                        </div>
                                        <span class="rating-total pl-1">({{ number_format($course->reviews_count) }})</span>
                                    </div><!-- end rating-wrap -->
                                    @if ($finalPrice < $course->selling_price)
                                        <p class="card-price text-black font-weight-bold">{{ number_format($finalPrice, 2) }} TND
                                            <span class="before-price font-weight-medium">{{ number_format($course->selling_price, 2) }} TND</span>
                                        </p>
                                    @else
                                        <p class="card-price text-black font-weight-bold">{{ number_format($finalPrice, 2) }} TND</p>
                                    @endif
                                </div><!-- end card-body -->
                            </div><!-- end card -->

                            <!-- Tooltip Content -->
                            <div class="tooltip_templates" style="display: none;">
                                <div id="tooltip_content_{{ $course->id }}">
                                    <div class="card-body position-relative">
                                        <p class="card-text pb-2">
                                            By 
                                            @if ($instructor)
                                                <a href="{{ route('instructor.details', $instructor->id) }}">{{ $instructor->name }}</a>
                                            @else
                                                Unknown Instructor
                                            @endif
                                        </p>
                                        <h5 class="card-title pb-1">
                                            <a href="{{ route('course.details', [$course->id, $course->course_name_slug]) }}">{{ $course->course_title }}</a>
                                        </h5>
                                        <div class="d-flex align-items-center pb-1">
                                            @if ($course->bestseller == 1)
                                                <h6 class="ribbon fs-14 mr-2">Bestseller</h6>
                                            @endif
                                            <p class="text-success fs-14 font-weight-medium">Updated <span class="font-weight-bold pl-1">{{ \Carbon\Carbon::parse($course->updated_at)->format('F Y') }}</span></p>
                                        </div>
                                        <div class="rating-wrap d-flex align-items-center py-2">
                                            <div class="review-stars">
                                                <span class="rating-number">{{ number_format($course->rating, 1) }}</span>
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span class="la la-star{{ $i <= floor($course->rating) ? '' : '-o' }}"></span>
                                                @endfor
                                            </div>
                                            <span class="rating-total pl-1">({{ number_format($course->reviews_count) }})</span>
                                        </div><!-- end rating-wrap -->
                                        <ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet d-flex align-items-center fs-14">
                                            <li>{{ $course->duration ?? 'N/A' }}</li>
                                            <li>{{ $course->label ?? 'All Levels' }}</li>
                                        </ul>
                                        <p class="card-text pt-1 fs-14 lh-22">{{ Str::limit(strip_tags($course->description), 100) ?? 'No description available.' }}</p>
                                        <ul class="generic-list-item fs-14 py-3">
                                            @foreach ($course->goals->take(3) as $goal)
                                                <li><i class="la la-check mr-1 text-black"></i> {{ $goal->goal_name }}</li>
                                            @endforeach
                                            @if ($course->goals->isEmpty())
                                                <li><i class="la la-check mr-1 text-black"></i> Learn key skills for this course</li>
                                                <li><i class="la la-check mr-1 text-black"></i> Boost your knowledge</li>
                                                <li><i class="la la-check mr-1 text-black"></i> Practical exercises included</li>
                                            @endif
                                        </ul>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <button class="btn theme-btn flex-grow-1 mr-3 add-to-cart" data-course-id="{{ $course->id }}" {{ $isInCart ? 'data-in-cart="true"' : '' }}>
                                                <i class="la la-shopping-cart fs-18 mr-1"></i> {{ $isInCart ? 'In Cart' : 'Add to Cart' }}
                                            </button>
                                            @auth
                                                <button class="wishlist-btn icon-element icon-element-sm shadow-sm cursor-pointer border-0 bg-transparent {{ $isWishlisted ? 'wishlisted' : '' }}"
                                                        data-course-id="{{ $course->id }}"
                                                        title="{{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                                                    <i class="la {{ $isWishlisted ? 'la-heart' : 'la-heart-o' }}"></i>
                                                </button>
                                            @else
                                                <a href="{{ route('login') }}" class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Login to add to Wishlist">
                                                    <i class="la la-heart-o"></i>
                                                </a>
                                            @endauth
                                        </div>
                                        <div id="cart-message-{{ $course->id }}" class="cart-message"></div>
                                    </div>
                                </div>
                            </div><!-- end tooltip_templates -->
                        </div><!-- end col-lg-4 -->
                    @endforeach
                </div><!-- end row -->
                <div class="text-center pt-3">
                    {{ $courses->links() }}
                    <p class="fs-14 pt-2">Showing {{ $courses->firstItem() }}–{{ $courses->lastItem() }} of {{ $courses->total() }} results</p>
                </div>
            </div><!-- end col-lg-12 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end courses-area -->
<!--======================================
        END COURSE AREA
======================================-->

<!-- Scripts -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('js/tooltipster.bundle.min.js') }}"></script>
<script>
$(document).ready(function() {
    console.log('jQuery loaded and document ready');

    // Initialize Tooltipster
    $('.card-preview').tooltipster({
        theme: 'tooltipster-shadow',
        interactive: true,
        contentAsHTML: true,
        maxWidth: 400,
        side: 'right',
        distance: 10
    });
    console.log('Tooltipster initialized');

    // Handle wishlist button clicks
    $('.wishlist-btn').off('click').on('click', function(e) {
        e.preventDefault();
        console.log('Wishlist button clicked');
        var $button = $(this);
        var courseId = $button.data('course-id');
        var isWishlisted = $button.hasClass('wishlisted');
        var url = isWishlisted ? '/wishlist/remove/' + courseId : '/wishlist/add/' + courseId;

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Wishlist AJAX success:', response);
                if (response.status === 'success') {
                    if (isWishlisted) {
                        $button.removeClass('wishlisted');
                        $button.find('i').removeClass('la-heart').addClass('la-heart-o');
                        $button.attr('title', 'Add to Wishlist');
                    } else {
                        $button.addClass('wishlisted');
                        $button.find('i').removeClass('la-heart-o').addClass('la-heart');
                        $button.attr('title', 'Remove from Wishlist');
                    }
                }
            },
            error: function(xhr) {
                console.error('Wishlist AJAX error:', xhr);
                var response = xhr.responseJSON;
                alert(response.message || 'An error occurred.');
            }
        });
    });

    // Handle Add/Remove from Cart button clicks
    $('.add-to-cart').off('click').on('click', function(e) {
        e.preventDefault();
        console.log('Cart button clicked');
        var $button = $(this);
        var courseId = $button.data('course-id');
        var isInCart = $button.data('in-cart') === true;
        var $message = $('#cart-message-' + courseId);
        var url = isInCart ? '{{ route("cart.remove", ":id") }}'.replace(':id', courseId) : '{{ route("cart.add", ":id") }}'.replace(':id', courseId);
        var method = isInCart ? 'GET' : 'POST';

        if (!courseId) {
            console.error('Course ID is undefined');
            $message.html('<div class="alert alert-danger">Error: Course ID is missing.</div>');
            setTimeout(function() { $message.empty(); }, 3000);
            return;
        }

        console.log('Sending AJAX request for course ID:', courseId, 'Action:', isInCart ? 'Remove' : 'Add');
        $.ajax({
            url: url,
            method: method,
            data: isInCart ? {} : { _token: $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json',
            success: function(response) {
                console.log('Cart AJAX success:', response);
                if (response.redirect) {
                    // Non-authenticated: Store course in localStorage
                    let tempCart = JSON.parse(localStorage.getItem('tempCart')) || [];
                    const itemIndex = tempCart.findIndex(item => item.courseId === response.course_id);
                    if (itemIndex > -1) {
                        tempCart[itemIndex].quantity += 1;
                    } else {
                        tempCart.push({ courseId: response.course_id, quantity: 1 });
                    }
                    localStorage.setItem('tempCart', JSON.stringify(tempCart));
                    $message.html('<div class="alert alert-info">Please log in to add this course to your cart.</div>');
                    setTimeout(function() {
                        console.log('Redirecting to:', response.redirect);
                        window.location.href = response.redirect;
                    }, 1500);
                } else if (response.success) {
                    $message.html('<div class="alert alert-success">' + response.message + '</div>');
                    if (isInCart) {
                        // Remove from cart
                        $button.data('in-cart', false).removeAttr('data-in-cart');
                        $button.prop('disabled', false).html('<i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart');
                    } else {
                        // Add to cart
                        $button.data('in-cart', true);
                        $button.prop('disabled', true).html('<i class="la la-shopping-cart fs-18 mr-1"></i> In Cart');
                    }
                    // Update cart count and subtotal
                    if ($('#cartQty').length) {
                        $('#cartQty').text(response.cartCount);
                    }
                    if ($('#cartSubTotal').length) {
                        $('#cartSubTotal').text('TND ' + response.cartSubTotal);
                    }
                    // Update cart dropdown
                    console.log('Updating cart dropdown');
                    $.ajax({
                        url: '{{ route("cart") }}',
                        method: 'GET',
                        success: function(html) {
                            console.log('Cart dropdown HTML received');
                            var $newCart = $(html).find('#cartDropdown').html();
                            $('#cartDropdown').html($newCart);
                            // Rebind remove-from-cart handlers in dropdown
                            bindCartDropdownHandlers();
                        },
                        error: function(xhr) {
                            console.error('Cart dropdown AJAX error:', xhr);
                        }
                    });
                } else {
                    $message.html('<div class="alert alert-info">' + (response.info || response.message || 'Action completed.') + '</div>');
                }
                setTimeout(function() { $message.empty(); }, 3000);
            },
            error: function(xhr) {
                console.error('Cart AJAX error:', xhr);
                var response = xhr.responseJSON || {};
                if (xhr.status === 401 && response.redirect) {
                    // Handle 401 Unauthorized for non-authenticated users
                    let tempCart = JSON.parse(localStorage.getItem('tempCart')) || [];
                    const itemIndex = tempCart.findIndex(item => item.courseId === response.course_id);
                    if (itemIndex > -1) {
                        tempCart[itemIndex].quantity += 1;
                    } else {
                        tempCart.push({ courseId: response.course_id, quantity: 1 });
                    }
                    localStorage.setItem('tempCart', JSON.stringify(tempCart));
                    $message.html('<div class="alert alert-info">Please log in to add this course to your cart.</div>');
                    setTimeout(function() {
                        console.log('Redirecting to:', response.redirect);
                        window.location.href = response.redirect;
                    }, 1500);
                } else {
                    $message.html('<div class="alert alert-danger">' + (response.error || response.message || 'An error occurred.') + '</div>');
                    setTimeout(function() { $message.empty(); }, 3000);
                }
            }
        });
    });

    // Function to bind remove-from-cart handlers in cart dropdown
    function bindCartDropdownHandlers() {
        $('#cartDropdown .remove-from-cart').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('Remove from cart button clicked in dropdown');
            var courseId = $(this).data('id');
            var $cartItem = $('#cart-item-' + courseId);
            var $message = $('#cart-message-' + courseId).length ? $('#cart-message-' + courseId) : $('<div class="cart-message"></div>').appendTo('body');

            $.ajax({
                url: '{{ route("cart.remove", ":id") }}'.replace(':id', courseId),
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Remove from cart AJAX success:', response);
                    if (response.redirect) {
                        $message.html('<div class="alert alert-info">Please log in to remove this course from your cart.</div>');
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1500);
                    } else if (response.success) {
                        $cartItem.remove();
                        $message.html('<div class="alert alert-success">' + response.message + '</div>');
                        if ($('#cartQty').length) {
                            $('#cartQty').text(response.cartCount);
                        }
                        if ($('#cartSubTotal').length) {
                            $('#cartSubTotal').text('TND ' + response.cartSubTotal);
                        }
                        if (response.cartCount === 0) {
                            $('#cartDropdown').html(
                                '<li class="media media-card">' +
                                '<div class="media-body fs-15 text-center">' +
                                '<p class="text-muted lh-18">Your cart is empty</p>' +
                                '</div></li>' +
                                '<li class="mt-3">' +
                                '<a href="{{ route('cart') }}" class="btn theme-btn w-100 py-2">Go to Cart <i class="la la-arrow-right icon ml-1"></i></a>' +
                                '</li>'
                            );
                        }
                        // Update course card button state
                        $('.add-to-cart[data-course-id="' + courseId + '"]').each(function() {
                            $(this).data('in-cart', false).removeAttr('data-in-cart')
                                .prop('disabled', false)
                                .html('<i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart');
                        });
                    } else {
                        $message.html('<div class="alert alert-info">' + (response.message || 'Action completed.') + '</div>');
                    }
                    setTimeout(function() { $message.empty(); }, 3000);
                },
                error: function(xhr) {
                    console.error('Remove from cart AJAX error:', xhr);
                    var response = xhr.responseJSON || {};
                    $message.html('<div class="alert alert-danger">' + (response.message || 'An error occurred.') + '</div>');
                    setTimeout(function() { $message.empty(); }, 3000);
                }
            });
        });
    }

    // Initial binding for cart dropdown handlers
    bindCartDropdownHandlers();
});
</script>
@endsection