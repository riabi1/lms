@php
use App\Models\Course;
use App\Models\Category;
use App\Models\CartItem;
use Illuminate\Support\Str;

$courses = Course::with(['courseable', 'reviews', 'goals'])->where('status', 1)->orderBy('id', 'ASC')->limit(6)->get();
$categories = Category::orderBy('category_name', 'ASC')->get();

$cartItems = collect([]);
if (auth()->check()) {
    $cartItems = CartItem::where('user_id', auth()->id())
        ->where('cartable_type', 'App\Models\Course')
        ->pluck('cartable_id');
} else {
    // Use localStorage via JavaScript instead of cookie; initialize empty for server-side rendering
    $cartItems = collect([]);
}
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
                        $isInCart = $cartItems->contains($course->id);
                        $hasPurchased = auth()->check() && \App\Models\Order::where('user_id', auth()->id())
                            ->where('course_id', $course->id)
                            ->where('payment_status', 'paid')
                            ->exists();
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
                                                <button class="btn theme-btn flex-grow-1 mr-3 add-to-cart" 
                                                        data-course-id="{{ $course->id }}" 
                                                        data-price="{{ $finalPrice }}"
                                                        data-course-name="{{ $course->course_name }}"
                                                        data-course-image="{{ $course->course_image ? asset('upload/course_images/thumbnail/' . $course->course_image) : asset('images/default-course.jpg') }}"
                                                        data-selling-price="{{ $sellingPrice }}"
                                                        data-discount-price="{{ $discountPrice }}"
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
                        $isInCart = $cartItems->contains($course->id);
                        $hasPurchased = auth()->check() && \App\Models\Order::where('user_id', auth()->id())
                            ->where('course_id', $course->id)
                            ->where('payment_status', 'paid')
                            ->exists();
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
                                                <button class="btn theme-btn flex-grow-1 mr-3 add-to-cart" 
                                                        data-course-id="{{ $course->id }}" 
                                                        data-price="{{ $finalPrice }}"
                                                        data-course-name="{{ $course->course_name }}"
                                                        data-course-image="{{ $course->course_image ? asset('upload/course_images/thumbnail/' . $course->course_image) : asset('images/default-course.jpg') }}"
                                                        data-selling-price="{{ $sellingPrice }}"
                                                        data-discount-price="{{ $discountPrice }}"
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
    // Initialize localStorage for non-authenticated users
    let tempCart = [];
    const storedCart = localStorage.getItem('tempCart');
    if (storedCart) {
        try {
            tempCart = JSON.parse(storedCart);
            if (!Array.isArray(tempCart)) {
                tempCart = [];
                localStorage.setItem('tempCart', JSON.stringify(tempCart));
            }
        } catch (e) {
            tempCart = [];
            localStorage.setItem('tempCart', JSON.stringify(tempCart));
        }
    } else {
        localStorage.setItem('tempCart', JSON.stringify(tempCart));
    }

    let cartItems = @auth [] @else tempCart.map(item => item.courseId) @endauth;

    // Update cart button states for non-authenticated users
    if (!@auth true @else false @endauth) {
        $('.add-to-cart').each(function() {
            const courseId = $(this).data('course-id');
            if (cartItems.includes(courseId)) {
                $(this).data('in-cart', true)
                    .attr('data-in-cart', 'true')
                    .prop('disabled', true)
                    .html('<i class="la la-shopping-cart fs-18 mr-1"></i> In Cart');
            }
        });
    }

    // Show notification
    function showNotification(message, type) {
        const $message = $('<div class="cart-message"></div>').html(`<div class="alert alert-${type}">${message}</div>`)
            .css({ position: 'fixed', top: '10px', right: '10px', 'z-index': 1000 });
        $('body').append($message);
        setTimeout(() => $message.fadeOut(300, () => $message.remove()), 3000);
    }

    // Debounce utility
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

    // Slugify function
    function slugify(text) {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-') // Replace spaces with -
            .replace(/[^\w\-]+/g, '') // Remove all non-word chars
            .replace(/\-\-+/g, '-') // Replace multiple - with single -
            .replace(/^-+/, '') // Trim - from start
            .replace(/-+$/, ''); // Trim - from end
    }

    // Initialize Tooltipster
    $('.card-preview').tooltipster({
        theme: 'tooltipster-shadow',
        interactive: true,
        contentAsHTML: true,
        maxWidth: 400,
        side: 'right',
        distance: 10
    });

    // Wishlist functionality
    $('.wishlist-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const $button = $(this);
        const courseId = $button.data('course-id');
        const isWishlisted = $button.hasClass('wishlisted');
        const url = isWishlisted ? '/wishlist/remove/' + courseId : '/wishlist/add/' + courseId;

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
                    showNotification(response.message || 'An error occurred.', 'danger');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                showNotification(response.message || 'An error occurred.', 'danger');
            }
        });
    });

    // Add to cart functionality with debounce
    $(document).off('click', '.add-to-cart').on('click', '.add-to-cart:not(.tooltipstered)', debounce(function(e) {
        e.preventDefault();
        e.stopPropagation();
        const $button = $(this);
        const courseId = $button.data('course-id');
        const isInCart = $button.data('in-cart') === true;
        if (isInCart) return;

        const courseData = {
            courseId: courseId,
            price: parseFloat($button.data('price')),
            course_name: $button.data('course-name'),
            image: $button.data('course-image'),
            selling_price: parseFloat($button.data('selling-price')),
            discount_price: parseFloat($button.data('discount-price')),
            quantity: 1,
            courseSlug: $button.data('course-name') ? slugify($button.data('course-name')) : ''
        };

        $.ajax({
            url: '{{ route("cart.add", ":id") }}'.replace(':id', courseId),
            method: 'POST',
            data: { 
                _token: $('meta[name="csrf-token"]').attr('content'),
                quantity: 1
            },
            dataType: 'json',
            beforeSend: function() {
                $button.prop('disabled', true).html('<i class="la la-spinner la-spin mr-1"></i> Adding...');
            },
            success: function(response) {
                if (response.redirect) {
                    // Non-authenticated user: store in localStorage and redirect to login
                    tempCart = tempCart.filter(item => item.courseId !== courseId);
                    tempCart.push(courseData);
                    localStorage.setItem('tempCart', JSON.stringify(tempCart));
                    showNotification(response.message, 'info');
                    setTimeout(() => window.location.href = response.redirect, 1500);
                } else if (response.success) {
                    // Authenticated user: update UI
                    $button.data('in-cart', true)
                        .attr('data-in-cart', 'true')
                        .prop('disabled', true)
                        .html('<i class="la la-shopping-cart fs-18 mr-1"></i> In Cart');
                    $('#cartQty').text(response.cartCount);
                    $('#cartSubTotal').text('TND ' + response.cartSubTotal);

                    // Update cart dropdown
                    $.ajax({
                        url: '{{ route("cart") }}',
                        method: 'GET',
                        success: function(html) {
                            const $newCart = $(html).find('#cartDropdown').html();
                            $('#cartDropdown').html($newCart);
                            showNotification(response.message, 'success');
                        },
                        error: function(xhr) {
                            showNotification('Failed to update cart display.', 'danger');
                        }
                    });
                } else {
                    showNotification(response.message || response.error || 'Failed to add to cart.', 'danger');
                    $button.prop('disabled', false).html('<i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                showNotification(response.error || response.message || 'An error occurred while adding to cart.', 'danger');
                $button.prop('disabled', false).html('<i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart');
            }
        });
    }, 300));
});
</script>