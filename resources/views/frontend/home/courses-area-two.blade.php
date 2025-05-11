@php
// Fetch bestseller courses, eager load relationships, default to empty collection
$bestsellers = App\Models\Course::with(['courseable', 'reviews', 'goals'])
    ->where('bestseller', 1)
    ->where('status', 1)
    ->orderBy('id', 'asc')
    ->take(6)
    ->get() ?? collect();
@endphp

<section class="bestseller-area pb-120px">
    <div class="container">
        <div class="section-heading text-center">
            <h5 class="ribbon ribbon-lg mb-2">Discover Our Top Courses</h5>
            <h2 class="section__title">Popular Bestsellers</h2>
            <span class="section-divider"></span>
        </div><!-- end section-heading -->
    </div><!-- end container -->

    <div class="card-content-wrapper bg-gray pt-50px pb-120px">
        <div class="container">
            <div class="row">
                @forelse($bestsellers as $course)
                    @php
                        // Calculate final price and discount
                        $finalPrice = $course->discount_price !== null
                            ? max(0, $course->selling_price - $course->discount_price)
                            : $course->selling_price;
                        $discountPercentage = ($course->selling_price > 0 && $course->discount_price !== null)
                            ? round(($course->discount_price / $course->selling_price) * 100)
                            : 0;
                        // Calculate average rating and review count
                        $rating = $course->reviews->avg('rating') ?? 0;
                        $reviewsCount = $course->reviews->count();
                        // Get instructor
                        $instructor = $course->courseable instanceof \App\Models\Instructor ? $course->courseable : null;
                        // Check wishlist and cart status
                        $isWishlisted = auth()->check() && \App\Models\Wishlist::where('trackable_type', 'App\Models\User')
                            ->where('trackable_id', auth()->id())
                            ->where('course_id', $course->id)
                            ->exists();
                        $isInCart = \Darryldecode\Cart\Facades\CartFacade::get($course->id) !== null;
                        // Check if purchased
                        $hasPurchased = auth()->check() && \App\Models\Order::where('user_id', auth()->id())
                            ->where('course_id', $course->id)
                            ->where('payment_status', 'paid')
                            ->exists();
                    @endphp
                    <div class="col-lg-4 responsive-column-half">
                        <div class="card card-item card-preview" data-tooltip-content="#tooltip_content_{{ $course->id }}">
                            <div class="card-image">
                                <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}" class="d-block">
                                    <img class="card-img-top lazy" 
                                         src="{{ $course->course_image ? asset('upload/course_images/thumbnail/' . $course->course_image) : asset('images/no_image.jpg') }}"
                                         alt="{{ $course->course_name }} image">
                                </a>
                                <div class="course-badge-labels">
                                    <div class="course-badge red">Bestseller</div>
                                    @if ($course->highestrated == 1)
                                        <div class="course-badge blue">Highest Rated</div>
                                    @endif
                                    @if ($course->featured == 1)
                                        <div class="course-badge green">Featured</div>
                                    @endif
                                    @if ($course->discount_price !== null)
                                        <div class="course-badge blue">{{ $discountPercentage }}% Off</div>
                                    @else
                                        <div class="course-badge blue">New</div>
                                    @endif
                                </div>
                            </div><!-- end card-image -->
                            <div class="card-body">
                                <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $course->label ?? 'All Levels' }}</h6>
                                <h5 class="card-title">
                                    <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}">{{ $course->course_name }}</a>
                                </h5>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="card-price text-black font-weight-bold">
                                        {{ number_format($finalPrice, 2) }} TND
                                        @if ($finalPrice < $course->selling_price)
                                            <span class="before-price font-weight-medium">{{ number_format($course->selling_price, 2) }} TND</span>
                                        @endif
                                    </p>
                                </div>
                            </div><!-- end card-body -->
                        </div><!-- end card -->

                        <!-- Tooltip Content -->
                        <div class="tooltip_templates" style="display: none;">
                            <div id="tooltip_content_{{ $course->id }}">
                                <div class="card-body position-relative">
                                    <p class="card-text pb-2">By 
                                        @if ($instructor)
                                            <a href="{{ route('instructor.details', $instructor->id) }}">{{ $instructor->name }}</a>
                                        @else
                                            Unknown Instructor
                                        @endif
                                    </p>
                                    <h5 class="card-title pb-1">
                                        <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}">{{ $course->course_title }}</a>
                                    </h5>
                                    <div class="d-flex align-items-center pb-1">
                                        <h6 class="ribbon fs-14 mr-2">Bestseller</h6>
                                        @if ($course->highestrated == 1)
                                            <h6 class="ribbon blue fs-14 mr-2">Highest Rated</h6>
                                        @endif
                                        @if ($course->featured == 1)
                                            <h6 class="ribbon green fs-14 mr-2">Featured</h6>
                                        @endif
                                        <p class="text-success fs-14 font-weight-medium">Updated <span class="font-weight-bold pl-1">{{ \Carbon\Carbon::parse($course->updated_at)->format('F Y') }}</span></p>
                                    </div>
                                    <div class="rating-wrap d-flex align-items-center py-2">
                                        <div class="review-stars">
                                            <span class="rating-number">{{ number_format($rating, 1) }}</span>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="la la-star{{ $i <= floor($rating) ? '' : '-o' }}"></span>
                                            @endfor
                                        </div>
                                        <span class="rating-total pl-1">({{ number_format($reviewsCount) }})</span>
                                    </div>
                                    <ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet d-flex align-items-center fs-14">
                                        <li>{{ $course->duration ?? 'N/A' }}</li>
                                        <li>{{ $course->label ?? 'All Levels' }}</li>
                                    </ul>
                                    <p class="card-text pt-1 fs-14 lh-22">{{ Str::limit(strip_tags($course->description), 100, '...') ?? 'No description available.' }}</p>
                                    <ul class="generic-list-item fs-14 py-3">
                                        @forelse ($course->goals->take(3) as $goal)
                                            <li><i class="la la-check mr-1 text-black"></i> {{ $goal->goal_name }}</li>
                                        @empty
                                            <li><i class="la la-check mr-1 text-black"></i> Master key course concepts</li>
                                            <li><i class="la la-check mr-1 text-black"></i> Apply practical skills</li>
                                            <li><i class="la la-check mr-1 text-black"></i> Achieve learning objectives</li>
                                        @endforelse
                                    </ul>
                                    <div class="d-flex justify-content-between align-items-center">
                                        @if ($hasPurchased)
                                            <a href="{{ route('course.start', [$course->id, \Str::slug($course->course_name)]) }}" class="btn theme-btn flex-grow-1 mr-3">
                                                <i class="la la-play-circle fs-18 mr-1"></i> Start Learning
                                            </a>
                                        @else
                                            <button class="btn theme-btn flex-grow-1 mr-3 add-to-cart" 
                                                    data-course-id="{{ $course->id }}" 
                                                    {{ $isInCart ? 'data-in-cart="true" disabled' : '' }}>
                                                <i class="la la-shopping-cart fs-18 mr-1"></i> {{ $isInCart ? 'In Cart' : 'Add to Cart' }}
                                            </button>
                                        @endif
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
                @empty
                    <div class="col-12">
                        <h5 class="text-danger text-center">No Bestseller Courses Found</h5>
                    </div>
                @endforelse
            </div><!-- end row -->
            <div class="more-btn-box mt-4 text-center">
                <a href="{{ route('course.list') }}" class="btn theme-btn">Browse All Courses <i class="la la-arrow-right icon ml-1"></i></a>
            </div><!-- end more-btn-box -->
        </div><!-- end container -->
    </div><!-- end card-content-wrapper -->
</section><!-- end bestseller-area -->

<style>
/* CSS Variables for Theme Colors */
:root {
    --bg-light: #ffffff;
    --bg-dark: #1a1a1a;
    --bg-gray-light: #f8f9fa;
    --bg-gray-dark: #252525;
    --text-light: #333333;
    --text-dark: #e0e0e0;
    --text-muted-light: #666666;
    --text-muted-dark: #b0b0b0;
    --accent: #dc3545;
    --accent-hover: #c82333;
    --border-light: #e5e5e5;
    --border-dark: #444444;
    --shadow-light: rgba(0, 0, 0, 0.15);
    --shadow-dark: rgba(255, 255, 255, 0.1);
}

/* Scoped styles for Bestseller Area */
.bestseller-area {
    background: var(--bg-light);
    transition: background-color 0.3s ease, color 0.3s ease;
}
:where(.dark-mode) .bestseller-area {
    background: var(--bg-dark);
}

.card-content-wrapper.bg-gray {
    background: var(--bg-gray-light);
    transition: background-color 0.3s ease;
}
:where(.dark-mode) .card-content-wrapper.bg-gray {
    background: var(--bg-gray-dark);
}

.section-heading .ribbon {
    font-size: 14px;
    padding: 8px 20px;
    border-radius: 4px;
    background: var(--accent);
    color: #fff;
}
:where(.dark-mode) .section-heading .ribbon {
    background: var(--accent);
}

.section-heading .section__title {
    font-size: 2rem;
    color: var(--text-light);
    font-weight: 600;
    margin-bottom: 15px;
}
:where(.dark-mode) .section-heading .section__title {
    color: var(--text-dark);
}

.section-heading .section-divider {
    background: var(--accent);
    height: 3px;
    width: 60px;
    display: block;
    margin: 15px auto;
}

.card.card-item.card-preview {
    height: 380px;
    width: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border-radius: 8px;
    background: var(--bg-light);
    box-shadow: 0 4px 12px var(--shadow-light);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card.card-item.card-preview:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 16px var(--shadow-light);
}
:where(.dark-mode) .card.card-item.card-preview {
    background: var(--bg-gray-dark);
    box-shadow: 0 4px 12px var(--shadow-dark);
}
:where(.dark-mode) .card.card-item.card-preview:hover {
    box-shadow: 0 6px 16px var(--shadow-dark);
}

.card-image {
    height: 200px;
    overflow: hidden;
    position: relative;
}

.card-img-top {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.card-image:hover .card-img-top {
    transform: scale(1.05);
}

.course-badge-labels {
    position: absolute;
    top: 10px;
    left: 10px;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.course-badge {
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 3px;
    color: #fff;
    max-width: 100px;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
}
.course-badge.red {
    background: var(--accent);
}
.course-badge.blue {
    background: #007bff;
}
.course-badge.green {
    background: #28a745;
}
:where(.dark-mode) .course-badge.red {
    background: var(--accent);
}
:where(.dark-mode) .course-badge.blue {
    background: #0056b3;
}
:where(.dark-mode) .course-badge.green {
    background: #1e7e34;
}

.card-body {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 15px;
}

.ribbon.ribbon-blue-bg {
    font-size: 12px;
    max-width: 100px;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    background: #007bff;
    color: #fff;
}
:where(.dark-mode) .ribbon.ribbon-blue-bg {
    background: #0056b3;
}

.card-title {
    font-size: 16px;
    line-height: 1.4;
    max-height: 44px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    color: var(--text-light);
}
:where(.dark-mode) .card-title {
    color: var(--text-dark);
}

.card-title a {
    color: inherit;
    text-decoration: none;
}
.card-title a:hover {
    color: var(--accent);
}

.card-price {
    font-size: 16px;
    margin-bottom: 0;
    color: var(--text-light);
}
:where(.dark-mode) .card-price {
    color: var(--text-dark);
}

.before-price {
    font-size: 14px;
    color: var(--text-muted-light);
    text-decoration: line-through;
}
:where(.dark-mode) .before-price {
    color: var(--text-muted-dark);
}

.theme-btn {
    background: var(--accent);
    color: #fff;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 500;
    transition: background 0.3s ease;
}
.theme-btn:hover {
    background: var(--accent-hover);
}

.add-to-cart {
    background: var(--accent);
    color: #fff;
    border: none;
    transition: background 0.3s ease;
}
.add-to-cart:hover:not(:disabled) {
    background: var(--accent-hover);
}
.add-to-cart:disabled {
    background: #6c757d;
    cursor: not-allowed;
}

.wishlist-btn {
    background: transparent;
    border: none;
}
.wishlist-btn i {
    color: var(--text-light);
    transition: color 0.3s ease;
}
:where(.dark-mode) .wishlist-btn i {
    color: var(--text-dark);
}
.wishlist-btn.wishlisted i {
    color: #F16767;
}

.tooltipster-content {
    background: var(--bg-light);
    border: 1px solid var(--border-light);
    box-shadow: 0 2px 10px var(--shadow-light);
    padding: 15px;
    border-radius: 6px;
    color: var(--text-light);
    max-width: 400px;
}
:where(.dark-mode) .tooltipster-content {
    background: var(--bg-gray-dark);
    border: 1px solid var(--border-dark);
    box-shadow: 0 2px 10px var(--shadow-dark);
    color: var(--text-dark);
}

.tooltipster-content .card-text,
.tooltipster-content .rating-total,
.tooltipster-content .generic-list-item li {
    color: var(--text-muted-light);
}
:where(.dark-mode) .tooltipster-content .card-text,
:where(.dark-mode) .tooltipster-content .rating-total,
:where(.dark-mode) .tooltipster-content .generic-list-item li {
    color: var(--text-muted-dark);
}

.tooltipster-content .text-success {
    color: #28a745;
}
:where(.dark-mode) .tooltipster-content .text-success {
    color: #1e7e34;
}

.tooltipster-content .la-check {
    color: var(--text-light);
}
:where(.dark-mode) .tooltipster-content .la-check {
    color: var(--text-dark);
}

.tooltipster-content .ribbon {
    padding: 4px 8px;
    border-radius: 3px;
    color: #fff;
}
.tooltipster-content .ribbon.blue {
    background: #007bff;
}
.tooltipster-content .ribbon.green {
    background: #28a745;
}
:where(.dark-mode) .tooltipster-content .ribbon.blue {
    background: #0056b3;
}
:where(.dark-mode) .tooltipster-content .ribbon.green {
    background: #1e7e34;
}

.cart-message {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1000;
    padding: 10px;
    border-radius: 4px;
    max-width: 300px;
    box-shadow: 0 2px 5px var(--shadow-light);
}
:where(.dark-mode) .cart-message {
    box-shadow: 0 2px 5px var(--shadow-dark);
}

.alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
}
:where(.dark-mode) .alert-info {
    background-color: #2c6b74;
    color: #b0e0e6;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}
:where(.dark-mode) .alert-success {
    background-color: #2e5a38;
    color: #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}
:where(.dark-mode) .alert-danger {
    background-color: #7a353b;
    color: #f5c6cb;
}

.tooltipster-base {
    z-index: 9999 !important;
    pointer-events: auto !important;
}

.card-content-wrapper {
    overflow: visible !important;
}

.responsive-column-half {
    display: flex;
    align-items: stretch;
}

/* Responsive adjustments */
@media (max-width: 991px) {
    .col-lg-4.responsive-column-half {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (max-width: 767px) {
    .col-lg-4.responsive-column-half {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>

<!-- Scripts -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('js/tooltipster.bundle.min.js') }}"></script>
<script>
$(document).ready(function() {
    console.log('Bestseller section: jQuery loaded and document ready');

    // Initialize Tooltipster
    $('.card-preview').tooltipster({
        theme: 'tooltipster-shadow',
        interactive: true,
        contentAsHTML: true,
        maxWidth: 400,
        side: ['right', 'top', 'bottom', 'left'],
        distance: 10,
        animation: 'fade',
        delay: 200
    });
    console.log('Bestseller section: Tooltipster initialized');

    // Handle wishlist button clicks
    $('.wishlist-btn').on('click', function(e) {
        e.preventDefault();
        console.log('Wishlist button clicked');
        const $button = $(this);
        const courseId = $button.data('course-id');
        const isWishlisted = $button.hasClass('wishlisted');
        const url = isWishlisted ? `/wishlist/remove/${courseId}` : `/wishlist/add/${courseId}`;

        $.ajax({
            url: url,
            method: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                console.log('Wishlist AJAX success:', response);
                if (response.status === 'success') {
                    $button.toggleClass('wishlisted');
                    $button.find('i').toggleClass('la-heart la-heart-o');
                    $button.attr('title', isWishlisted ? 'Add to Wishlist' : 'Remove from Wishlist');
                } else {
                    alert(response.message || 'Action completed.');
                }
            },
            error: function(xhr) {
                console.error('Wishlist AJAX error:', xhr);
                alert(xhr.responseJSON?.message || 'An error occurred.');
            }
        });
    });

    // Handle Add/Remove from Cart button clicks
    $('.add-to-cart').on('click', function(e) {
        e.preventDefault();
        console.log('Cart button clicked');
        const $button = $(this);
        const courseId = $button.data('course-id');
        const isInCart = $button.data('in-cart') === true;
        const $message = $(`#cart-message-${courseId}`);
        const url = isInCart ? `{{ route('cart.remove', ':id') }}`.replace(':id', courseId) : `{{ route('cart.add', ':id') }}`.replace(':id', courseId);
        const method = isInCart ? 'GET' : 'POST';

        if (!courseId) {
            console.error('Course ID is undefined');
            $message.html('<div class="alert alert-danger">Error: Course ID is missing.</div>').fadeOut(3000);
            return;
        }

        $.ajax({
            url: url,
            method: method,
            data: isInCart ? {} : { _token: $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json',
            success: function(response) {
                console.log('Cart AJAX success:', response);
                if (response.redirect) {
                    // Handle non-authenticated users
                    let tempCart = JSON.parse(localStorage.getItem('tempCart')) || [];
                    if (!isInCart) {
                        const itemIndex = tempCart.findIndex(item => item.courseId === courseId);
                        if (itemIndex > -1) {
                            tempCart[itemIndex].quantity += 1;
                        } else {
                            tempCart.push({ courseId: courseId, quantity: 1 });
                        }
                        localStorage.setItem('tempCart', JSON.stringify(tempCart));
                    }
                    $message.html('<div class="alert alert-info">Please log in to manage your cart.</div>').fadeOut(3000);
                    setTimeout(() => window.location.href = response.redirect, 1500);
                } else if (response.success) {
                    $message.html(`<div class="alert alert-success">${response.message}</div>`).fadeOut(3000);
                    $button.data('in-cart', !isInCart).prop('disabled', !isInCart)
                        .html(`<i class="la la-shopping-cart fs-18 mr-1"></i> ${isInCart ? 'Add to Cart' : 'In Cart'}`);
                    if ($('#cartQty').length) $('#cartQty').text(response.cartCount);
                    if ($('#cartSubTotal').length) $('#cartSubTotal').text(`TND ${response.cartSubTotal}`);
                    updateCartDropdown();
                } else {
                    $message.html(`<div class="alert alert-info">${response.message || 'Action completed.'}</div>`).fadeOut(3000);
                }
            },
            error: function(xhr) {
                console.error('Cart AJAX error:', xhr);
                const response = xhr.responseJSON || {};
                if (xhr.status === 401 && response.redirect) {
                    let tempCart = JSON.parse(localStorage.getItem('tempCart')) || [];
                    if (!isInCart) {
                        const itemIndex = tempCart.findIndex(item => item.courseId === courseId);
                        if (itemIndex > -1) {
                            tempCart[itemIndex].quantity += 1;
                        } else {
                            tempCart.push({ courseId: courseId, quantity: 1 });
                        }
                        localStorage.setItem('tempCart', JSON.stringify(tempCart));
                    }
                    $message.html('<div class="alert alert-info">Please log in to manage your cart.</div>').fadeOut(3000);
                    setTimeout(() => window.location.href = response.redirect, 1500);
                } else {
                    $message.html(`<div class="alert alert-danger">${response.message || 'An error occurred.'}</div>`).fadeOut(3000);
                }
            }
        });
    });

    // Update cart dropdown
    function updateCartDropdown() {
        $.ajax({
            url: '{{ route("cart") }}',
            method: 'GET',
            success: function(html) {
                console.log('Cart dropdown updated');
                $('#cartDropdown').html($(html).find('#cartDropdown').html());
                bindCartDropdownHandlers();
            },
            error: function(xhr) {
                console.error('Cart dropdown AJAX error:', xhr);
            }
        });
    }

    // Bind remove-from-cart handlers in cart dropdown
    function bindCartDropdownHandlers() {
        $('#cartDropdown .remove-from-cart').on('click', function(e) {
            e.preventDefault();
            console.log('Remove from cart clicked in dropdown');
            const courseId = $(this).data('id');
            const $cartItem = $(`#cart-item-${courseId}`);
            const $message = $(`#cart-message-${courseId}`).length ? $(`#cart-message-${courseId}`) : $('<div class="cart-message"></div>').appendTo('body');

            $.ajax({
                url: `{{ route('cart.remove', ':id') }}`.replace(':id', courseId),
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Remove from cart AJAX success:', response);
                    if (response.redirect) {
                        $message.html('<div class="alert alert-info">Please log in to manage your cart.</div>').fadeOut(3000);
                        setTimeout(() => window.location.href = response.redirect, 1500);
                    } else if (response.success) {
                        $cartItem.remove();
                        $message.html(`<div class="alert alert-success">${response.message}</div>`).fadeOut(3000);
                        if ($('#cartQty').length) $('#cartQty').text(response.cartCount);
                        if ($('#cartSubTotal').length) $('#cartSubTotal').text(`TND ${response.cartSubTotal}`);
                        if (response.cartCount === 0) {
                            $('#cartDropdown').html(`
                                <li class="media media-card">
                                    <div class="media-body fs-15 text-center">
                                        <p class="text-muted lh-18">Your cart is empty</p>
                                    </div>
                                </li>
                                <li class="mt-3">
                                    <a href="{{ route('cart') }}" class="btn theme-btn w-100 py-2">Go to Cart <i class="la la-arrow-right icon ml-1"></i></a>
                                </li>
                            `);
                        }
                        $(`.add-to-cart[data-course-id="${courseId}"]`).data('in-cart', false).prop('disabled', false)
                            .html('<i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart');
                    } else {
                        $message.html(`<div class="alert alert-info">${response.message || 'Action completed.'}</div>`).fadeOut(3000);
                    }
                },
                error: function(xhr) {
                    console.error('Remove from cart AJAX error:', xhr);
                    $message.html(`<div class="alert alert-danger">${xhr.responseJSON?.message || 'An error occurred.'}</div>`).fadeOut(3000);
                }
            });
        });
    }

    // Initial binding for cart dropdown handlers
    bindCartDropdownHandlers();
});
</script>