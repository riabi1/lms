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
                    <div class="custom-control custom-checkbox mr-3 mb-2">
                        <input type="checkbox" class="custom-control-input" id="bestseller" name="bestseller" value="1" {{ request('bestseller') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="custom-control-label" for="bestseller">Bestseller Only</label>
                    </div>
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input" id="highestrated" name="highestrated" value="1" {{ request('highestrated') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="custom-control-label" for="highestrated">Highest Rated Only</label>
                    </div>
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input" id="featured" name="featured" value="1" {{ request('featured') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="custom-control-label" for="featured">Featured Only</label>
                    </div>
                </div>
                <p class="fs-14">We found <span class="text-black">{{ $courses->total() }}</span> courses available for you</p>
            </form>
        </div><!-- end filter-bar -->

        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    @forelse ($courses as $course)
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
                            $hasPurchased = auth()->check() && \App\Models\Order::where('user_id', auth()->id())
                                ->where('course_id', $course->id)
                                ->where('payment_status', 'paid')
                                ->exists();
                        @endphp
                        <div class="col-lg-4 col-md-6 responsive-column-half">
                            <div class="card card-item card-preview" data-tooltip-content="#tooltip_content_{{ $course->id }}">
                                <div class="card-image">
                                    <a href="{{ route('course.details', [$course->id, $course->course_name_slug]) }}" class="d-block">
                                        <img class="card-img-top lazy" 
                                             src="{{ $course->course_image ? asset('upload/course_images/thumbnail/' . $course->course_image) : asset('images/default-course.jpg') }}" 
                                             alt="{{ $course->course_title }}"
                                             loading="lazy">
                                    </a>
                                    <div class="course-badge-labels">
                                        @if ($course->bestseller == 1)
                                            <div class="course-badge red">Bestseller</div>
                                        @endif
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
                                    <p class="card-price text-black font-weight-bold">
                                        {{ number_format($finalPrice, 2) }} TND
                                        @if ($finalPrice < $course->selling_price)
                                            <span class="before-price font-weight-medium">{{ number_format($course->selling_price, 2) }} TND</span>
                                        @endif
                                    </p>
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
                                        <div class="d-flex align-items-center pb-1 flex-wrap">
                                            @if ($course->bestseller == 1)
                                                <h6 class="ribbon fs-14 mr-2">Bestseller</h6>
                                            @endif
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
                            <h5 class="text-danger text-center">No Courses Found</h5>
                        </div>
                    @endforelse
                </div><!-- end row -->
                <div class="text-center pt-3">
                    {{ $courses->links() }}
                    <p class="fs-14 pt-2">Showing {{ $courses->firstItem() }}–{{ $courses->lastItem() }} of {{ $courses->total() }} results</p>
                </div>
            </div><!-- end col-lg-12 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end course-area -->
<!--======================================
        END COURSE AREA
======================================-->

<style>
/* CSS Variables for Theme Colors (consistent with bestseller section) */
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

/* Scoped styles for Course Area */
.course-area {
    background: var(--bg-light);
    transition: background-color 0.3s ease;
}
:where(.dark-mode) .course-area {
    background: var(--bg-dark);
}

.filter-bar {
    background: var(--bg-gray-light);
    padding: 15px;
    border-radius: 6px;
    transition: background-color 0.3s ease;
}
:where(.dark-mode) .filter-bar {
    background: var(--bg-gray-dark);
}

.select-container-select {
    padding: 8px;
    border-radius: 4px;
    border: 1px solid var(--border-light);
    background: var(--bg-light);
    color: var(--text-light);
}
:where(.dark-mode) .select-container-select {
    border: 1px solid var(--border-dark);
    background: var(--bg-gray-dark);
    color: var(--text-dark);
}

.custom-control-label {
    color: var(--text-light);
}
:where(.dark-mode) .custom-control-label {
    color: var(--text-dark);
}

.card.card-item.card-preview {
    height: 420px;
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

.card-text {
    font-size: 14px;
    color: var(--text-muted-light);
}
:where(.dark-mode) .card-text {
    color: var(--text-muted-dark);
}

.card-text a {
    color: var(--text-muted-light);
    text-decoration: none;
}
.card-text a:hover {
    color: var(--accent);
}
:where(.dark-mode) .card-text a {
    color: var(--text-muted-dark);
}

.rating-wrap .rating-number {
    color: var(--text-light);
}
:where(.dark-mode) .rating-wrap .rating-number {
    color: var(--text-dark);
}

.rating-total {
    color: var(--text-muted-light);
}
:where(.dark-mode) .rating-total {
    color: var(--text-muted-dark);
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
    background: #d1ecf1;
    color: #0c5460;
}
:where(.dark-mode) .alert-info {
    background: #2c6b74;
    color: #b0e0e6;
}

.alert-success {
    background: #d4edda;
    color: #155724;
}
:where(.dark-mode) .alert-success {
    background: #2e5a38;
    color: #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
}
:where(.dark-mode) .alert-danger {
    background: #7a353b;
    color: #f5c6cb;
}

.tooltipster-base {
    z-index: 9999 !important;
    pointer-events: auto !important;
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
    console.log('Course List: jQuery loaded and document ready');

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
    console.log('Course List: Tooltipster initialized');

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
@endsection