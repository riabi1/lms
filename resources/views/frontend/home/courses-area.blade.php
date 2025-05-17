@php
use App\Models\Course;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

$courses = Course::with(['courseable', 'reviews', 'goals'])->where('status', 1)->orderBy('id', 'ASC')->limit(6)->get();
$categories = Category::orderBy('category_name', 'ASC')->get();
$cartSubtotal = auth()->check() ? App\Models\CartItem::where('user_id', auth()->id())->sum('price') : 0.00;
$cartItems = auth()->check() ? Cache::remember('cart_items_' . auth()->id(), 60, function () {
    return App\Models\CartItem::where('user_id', auth()->id())
        ->where('cartable_type', 'App\\Models\\Course')
        ->pluck('cartable_id')
        ->toArray();
}) : [];
@endphp

<style>
.wishlist-btn i {
    transition: color 0.3s ease;
}
.wishlist-btn.wishlisted i {
    color: #F16767;
}
.cart-message {
    position: fixed;
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
.tooltipster-base {
    z-index: 9999 !important;
    pointer-events: auto !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}
.tooltipster-content {
    background-color: #fff;
    border: 1px solid #ddd;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    padding: 10px;
}
.card-content-wrapper {
    overflow: visible !important;
}
.card.card-item.card-preview {
    height: 380px;
    width: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.card-image {
    height: 200px;
    overflow: hidden;
}
.card-img-top {
    width: 100%;
    height: 100%;
    object-fit: cover;
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
}
.ribbon.ribbon-blue-bg {
    font-size: 12px;
    max-width: 100%;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
}
.responsive-column-half {
    display: flex;
    align-items: stretch;
}
.cart-info {
    display: inline-flex;
    align-items: center;
    margin-left: 15px;
}
.cart-info .cart-count {
    background-color: #007bff;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    margin-left: 5px;
}
.cart-btn.in-cart {
    background-color: #6c757d;
    color: #fff;
}
.cart-btn.in-cart:hover {
    background-color: #5a6268;
}
</style>



<section class="course-area pb-120px">
    <div class="container">
        <div class="section-heading text-center">
            <h5 class="ribbon ribbon-lg mb-2">Choose your desired courses</h5>
            <h2 class="section__title">The world’s largest selection of courses</h2>
            <span class="section-divider"></span>
        </div>

        <ul class="nav nav-tabs generic-tab justify-content-center pb-4" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab" aria-controls="all" aria-selected="true">All</a>
            </li>
            @foreach ($categories as $category)
            <li class="nav-item">
                <a class="nav-link" id="category-{{ $category->id }}-tab" data-toggle="tab" href="#category{{ $category->id }}" role="tab" aria-controls="category{{ $category->id }}" aria-selected="false">{{ $category->category_name }}</a>
            </li>
            @endforeach
        </ul>
    </div>

    <div class="card-content-wrapper bg-gray pt-50px pb-120px">
        <div class="container">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    <div class="row">
                        @forelse($courses as $course)
                        @php
                        $sellingPrice = $course->selling_price ?? 0;
                        $discountPrice = $course->discount_price ?? 0;
                        $finalPrice = max(0, $sellingPrice - $discountPrice);
                        $discountPercentage = ($sellingPrice > 0 && $discountPrice > 0) ? round($discountPrice / $sellingPrice * 100) : 0;
                        $rating = $course->reviews->avg('rating') ?? 0;
                        $reviews_count = $course->reviews->count();
                        $instructor = $course->courseable instanceof \App\Models\Instructor ? $course->courseable : null;
                        $isWishlisted = auth()->check() && \App\Models\Wishlist::where('trackable_type', 'App\Models\User')
                            ->where('trackable_id', auth()->id())
                            ->where('course_id', $course->id)
                            ->exists();
                        $hasPurchased = auth()->check() && \App\Models\Order::where('user_id', auth()->id())
                            ->where('course_id', $course->id)
                            ->where('payment_status', 'paid')
                            ->exists();
                        $isInCart = in_array($course->id, $cartItems);
                        @endphp
                        <div class="col-lg-4 responsive-column-half">
                            <div class="card card-item card-preview" data-tooltip-content="#tooltip_content_{{ $course->id }}">
                                <div class="card-image">
                                    <a href="{{ route('course.details', [$course->id, Str::slug($course->course_name)]) }}" class="d-block">
                                        <img class="card-img-top lazy" 
                                             src="{{ $course->course_image ? asset('upload/course_images/thumbnail/' . $course->course_image) : asset('images/default-course.jpg') }}" 
                                             alt="Course image"
                                             onerror="this.src='{{ asset('images/default-course.jpg') }}'">
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
                                        @if ($discountPrice == 0)
                                            <div class="course-badge blue">New</div>
                                        @else
                                            <div class="course-badge blue">{{ $discountPercentage }}% Off</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $course->label }}</h6>
                                    <h5 class="card-title">
                                        <a href="{{ route('course.details', [$course->id, Str::slug($course->course_name)]) }}">{{ $course->course_name }}</a>
                                    </h5>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="card-price text-black font-weight-bold">{{ number_format($finalPrice, 2) }} TND</p>
                                        @if ($discountPercentage > 0)
                                            <p class="card-price text-muted fs-14 text-decoration-line-through">{{ number_format($sellingPrice, 2) }} TND</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

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
                                            <a href="{{ route('course.details', [$course->id, Str::slug($course->course_name)]) }}">{{ $course->course_name }}</a>
                                        </h5>
                                        <div class="d-flex align-items-center pb-1">
                                            @if ($course->bestseller == 1)
                                                <h6 class="ribbon fs-14 mr-2">Bestseller</h6>
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
                                            <span class="rating-total pl-1">({{ number_format($reviews_count) }})</span>
                                        </div>
                                        <ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet d-flex align-items-center fs-14">
                                            <li>{{ $course->label ?? 'All Levels' }}</li>
                                        </ul>
                                        <p class="card-text pt-1 fs-14 lh-22">{{ Str::limit(strip_tags($course->description), 100) ?? 'No description available.' }}</p>
                                        <ul class="generic-list-item fs-14 py-3">
                                            @forelse ($course->goals->take(3) as $goal)
                                                <li><i class="la la-check mr-1 text-black"></i> {{ $goal->goal_name }}</li>
                                            @empty
                                                <li><i class="la la-check mr-1 text-black"></i> Learn key skills for this course</li>
                                                <li><i class="la la-check mr-1 text-black"></i> Boost your knowledge</li>
                                                <li><i class="la la-check mr-1 text-black"></i> Practical exercises included</li>
                                            @endforelse
                                        </ul>
                                        <div class="d-flex justify-content-between align-items-center">
                                            @if ($hasPurchased)
                                                <a href="{{ route('course.start', [$course->id, Str::slug($course->course_name)]) }}" class="btn theme-btn flex-grow-1 mr-3">
                                                    <i class="la la-play-circle fs-18 mr-1"></i> Start Learning
                                                </a>
                                            @else
                                                <button class="btn theme-btn flex-grow-1 mr-3 cart-btn {{ $isInCart ? 'in-cart' : 'add-to-cart' }}" 
                                                        data-course-id="{{ $course->id }}"
                                                        data-action="{{ $isInCart ? 'remove' : 'add' }}">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <h5 class="text-danger text-center">No Courses Found</h5>
                        </div>
                        @endforelse
                    </div>
                </div>

                @foreach ($categories as $category)
                <div class="tab-pane fade" id="category{{ $category->id }}" role="tabpanel" aria-labelledby="category-{{ $category->id }}-tab">
                    <div class="row">
                        @php
                        $catwiseCourse = Course::with(['courseable', 'reviews', 'goals'])
                            ->whereHas('subcategory', function ($query) use ($category) {
                                $query->where('category_id', $category->id);
                            })
                            ->where('status', 1)
                            ->orderBy('id', 'DESC')
                            ->get();
                        @endphp
                        @forelse ($catwiseCourse as $course)
                        @php
                        $sellingPrice = $course->selling_price ?? 0;
                        $discountPrice = $course->discount_price ?? 0;
                        $finalPrice = max(0, $sellingPrice - $discountPrice);
                        $discountPercentage = ($sellingPrice > 0 && $discountPrice > 0) ? round($discountPrice / $sellingPrice * 100) : 0;
                        $rating = $course->reviews->avg('rating') ?? 0;
                        $reviews_count = $course->reviews->count();
                        $instructor = $course->courseable instanceof \App\Models\Instructor ? $course->courseable : null;
                        $isWishlisted = auth()->check() && \App\Models\Wishlist::where('trackable_type', 'App\Models\User')
                            ->where('trackable_id', auth()->id())
                            ->where('course_id', $course->id)
                            ->exists();
                        $hasPurchased = auth()->check() && \App\Models\Order::where('user_id', auth()->id())
                            ->where('course_id', $course->id)
                            ->where('payment_status', 'paid')
                            ->exists();
                        $isInCart = in_array($course->id, $cartItems);
                        @endphp
                        <div class="col-lg-4 responsive-column-half">
                            <div class="card card-item card-preview" data-tooltip-content="#tooltip_content_{{ $course->id }}">
                                <div class="card-image">
                                    <a href="{{ route('course.details', [$course->id, Str::slug($course->course_name)]) }}" class="d-block">
                                        <img class="card-img-top lazy" 
                                             src="{{ $course->course_image ? asset('upload/course_images/thumbnail/' . $course->course_image) : asset('images/default-course.jpg') }}" 
                                             alt="Course image"
                                             onerror="this.src='{{ asset('images/default-course.jpg') }}'">
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
                                        @if ($discountPrice == 0)
                                            <div class="course-badge blue">New</div>
                                        @else
                                            <div class="course-badge blue">{{ $discountPercentage }}% Off</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $course->label }}</h6>
                                    <h5 class="card-title">
                                        <a href="{{ route('course.details', [$course->id, Str::slug($course->course_name)]) }}">{{ $course->course_name }}</a>
                                    </h5>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="card-price text-black font-weight-bold">{{ number_format($finalPrice, 2) }} TND</p>
                                        @if ($discountPercentage > 0)
                                            <p class="card-price text-muted fs-14 text-decoration-line-through">{{ number_format($sellingPrice, 2) }} TND</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

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
                                            <a href="{{ route('course.details', [$course->id, Str::slug($course->course_name)]) }}">{{ $course->course_name }}</a>
                                        </h5>
                                        <div class="d-flex align-items-center pb-1">
                                            @if ($course->bestseller == 1)
                                                <h6 class="ribbon fs-14 mr-2">Bestseller</h6>
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
                                            <span class="rating-total pl-1">({{ number_format($reviews_count) }})</span>
                                        </div>
                                        <ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet d-flex align-items-center fs-14">
                                            <li>{{ $course->label ?? 'All Levels' }}</li>
                                        </ul>
                                        <p class="card-text pt-1 fs-14 lh-22">{{ Str::limit(strip_tags($course->description), 100) ?? 'No description available.' }}</p>
                                        <ul class="generic-list-item fs-14 py-3">
                                            @forelse ($course->goals->take(3) as $goal)
                                                <li><i class="la la-check mr-1 text-black"></i> {{ $goal->goal_name }}</li>
                                            @empty
                                                <li><i class="la la-check mr-1 text-black"></i> Learn key skills for this course</li>
                                                <li><i class="la la-check mr-1 text-black"></i> Boost your knowledge</li>
                                                <li><i class="la la-check mr-1 text-black"></i> Practical exercises included</li>
                                            @endforelse
                                        </ul>
                                        <div class="d-flex justify-content-between align-items-center">
                                            @if ($hasPurchased)
                                                <a href="{{ route('course.start', [$course->id, Str::slug($course->course_name)]) }}" class="btn theme-btn flex-grow-1 mr-3">
                                                    <i class="la la-play-circle fs-18 mr-1"></i> Start Learning
                                                </a>
                                            @else
                                                <button class="btn theme-btn flex-grow-1 mr-3 cart-btn {{ $isInCart ? 'in-cart' : 'add-to-cart' }}" 
                                                        data-course-id="{{ $course->id }}"
                                                        data-action="{{ $isInCart ? 'remove' : 'add' }}">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <h5 class="text-danger text-center">No Courses Found</h5>
                        </div>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>
            <div class="more-btn-box mt-4 text-center">
                <a href="{{ route('course.list') }}" class="btn theme-btn">Browse all Courses <i class="la la-arrow-right icon ml-1"></i></a>
            </div>
        </div>
    </div>
</section>

<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('js/tooltipster.bundle.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('.card-preview').tooltipster({
        theme: 'tooltipster-shadow',
        interactive: true,
        contentAsHTML: true,
        maxWidth: 400,
        side: 'right',
        distance: 10
    });

    function showNotification(message, type) {
        const $message = $('<div class="cart-message"></div>').html(`<div class="alert alert-${type}">${message}</div>`)
            .css({ position: 'fixed', top: '10px', right: '10px', 'z-index': 1000 });
        $('body').append($message);
        setTimeout(() => $message.fadeOut(300, () => $message.remove()), 3000);
    }

    let tempCart = JSON.parse(localStorage.getItem('tempCart')) || [];
    let processing = false; // Flag to prevent multiple simultaneous requests

    // Debounce function to limit rapid clicks
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    $('.wishlist-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const $button = $(this);
        const courseId = $button.data('course-id');
        const isWishlisted = $button.hasClass('wishlisted');
        const url = isWishlisted ? '/wishlist/remove/' + courseId : '/wishlist/add/' + courseId;

        if (processing) return; // Prevent multiple clicks
        processing = true;

        $.ajax({
            url: url,
            method: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    if (isWishlisted) {
                        $button.removeClass('wishlisted')
                            .find('i').removeClass('la-heart').addClass('la-heart-o')
                            .attr('title', 'Add to Wishlist');
                        showNotification('Removed from wishlist.', 'success');
                    } else {
                        $button.addClass('wishlisted')
                            .find('i').removeClass('la-heart-o').addClass('la-heart')
                            .attr('title', 'Remove from Wishlist');
                        showNotification('Added to wishlist.', 'success');
                    }
                } else {
                    showNotification(response.message || 'An error occurred while updating wishlist.', 'danger');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                showNotification(response.message || 'Failed to update wishlist. Please try again.', 'danger');
            },
            complete: function() {
                processing = false;
            }
        });
    });

    const handleCartClick = debounce(function(e) {
        e.preventDefault();
        const $button = $(this);
        const courseId = $button.data('course-id');
        const action = $button.data('action');

        if (!courseId) {
            console.error('Course ID is undefined');
            showNotification('Course ID not found.', 'danger');
            return;
        }

        if (processing) return; // Prevent multiple clicks
        processing = true;

        const isAuthenticated = {!! json_encode(auth()->check()) !!};

        if (!isAuthenticated) {
            if (!tempCart.some(item => item.courseId === courseId)) {
                tempCart.push({ courseId: courseId });
                localStorage.setItem('tempCart', JSON.stringify(tempCart));
                showNotification('Course added to temporary cart. Please log in to proceed.', 'info');
                setTimeout(() => {
                    window.location.href = '{{ route('login') }}?redirect=' + encodeURIComponent('{{ route('cart') }}') + '&course_id=' + courseId;
                }, 1500);
            } else {
                showNotification('Course already in temporary cart.', 'info');
            }
            processing = false;
            return;
        }

        const url = action === 'add' ? '/cart/add/' + courseId : '/cart/remove/' + courseId;
        const originalState = {
            class: $button.hasClass('in-cart') ? 'in-cart' : 'add-to-cart',
            action: action,
            html: $button.html()
        };

        $button.prop('disabled', true).html('<i class="la la-spinner la-spin mr-1"></i> Processing...');

        $.ajax({
            url: url,
            method: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (action === 'add') {
                        $button
                            .removeClass('add-to-cart')
                            .addClass('in-cart')
                            .data('action', 'remove')
                            .html('<i class="la la-shopping-cart fs-18 mr-1"></i> In Cart');
                    } else {
                        $button
                            .removeClass('in-cart')
                            .addClass('add-to-cart')
                            .data('action', 'add')
                            .html('<i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart');
                    }
                    showNotification(response.message, 'success');
                    $(document).trigger('cartUpdated', {
                        cartCount: response.cartCount,
                        cartSubTotal: response.cartSubTotal
                    });
                } else if (response.info) {
                    showNotification(response.info, 'info');
                    $button
                        .prop('disabled', false)
                        .removeClass('in-cart add-to-cart')
                        .addClass(originalState.class)
                        .data('action', originalState.action)
                        .html(originalState.html);
                } else if (response.redirect) {
                    showNotification(response.message || 'Please log in to continue.', 'info');
                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 1500);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                const message = response.message || 'An error occurred. Please try again.';
                showNotification(message, 'danger');
                $button
                    .prop('disabled', false)
                    .removeClass('in-cart add-to-cart')
                    .addClass(originalState.class)
                    .data('action', originalState.action)
                    .html(originalState.html);
                if (response.redirect) {
                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 1500);
                }
            },
            complete: function() {
                processing = false;
            }
        });
    }, 300);

    $('.cart-btn').on('click', handleCartClick);

    @if (auth()->check())
        const urlParams = new URLSearchParams(window.location.search);
        const courseIdFromRedirect = urlParams.get('course_id');
        if (courseIdFromRedirect && !tempCart.some(item => item.courseId === parseInt(courseIdFromRedirect))) {
            tempCart.push({ courseId: parseInt(courseIdFromRedirect) });
            localStorage.setItem('tempCart', JSON.stringify(tempCart));
        }

        if (tempCart.length > 0) {
            $.ajax({
                url: '/cart/sync',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    tempCart: tempCart
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        $(document).trigger('cartUpdated', {
                            cartCount: response.cartCount,
                            cartSubTotal: response.cartSubTotal
                        });
                        if (response.clearTempCart) {
                            tempCart = [];
                            localStorage.removeItem('tempCart');
                        }
                        tempCart.forEach(function(item) {
                            const $button = $(`.cart-btn[data-course-id="${item.courseId}"]`);
                            if ($button.length) {
                                $button
                                    .removeClass('add-to-cart')
                                    .addClass('in-cart')
                                    .data('action', 'remove')
                                    .html('<i class="la la-shopping-cart fs-18 mr-1"></i> In Cart');
                            }
                        });
                    } else {
                        showNotification(response.message || 'Failed to sync cart.', 'danger');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    showNotification(response.message || 'An error occurred while syncing cart.', 'danger');
                }
            });
        }
        @if (session('cart_added_message'))
            showNotification('{{ session('cart_added_message') }}', 'success');
        @endif
    @endif

    $(document).on('cartUpdated', function(event, data) {
        $('.cart-count').text(data.cartCount);
        $('.cart-subtotal').text('TND ' + data.cartSubTotal);
    });
});
</script>
