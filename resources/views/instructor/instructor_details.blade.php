@extends('frontend.master')

@section('title')
{{ $instructor->name ?? 'Instructor Not Found' }} | Easy Learning
@endsection

@section('home')
<style>
    :root {
        --primary: #4DB6AC; /* Soft Teal */
        --secondary: #FF8A80; /* Light Coral */
        --text-dark: #37474F; /* Dark Slate */
        --text-muted: #78909C; /* Soft Gray */
        --bg-light: #FFF8E1; /* Creamy White */
        --bg-white: #FFFFFF; /* Pure White */
        --success: #81C784; /* Soft Green */
        --warning: #FFAB91; /* Soft Orange */
        --red: #F16767; /* Wishlist Heart Color */
    }
    .bg-primary { background-color: var(--primary); }
    .text-primary { color: var(--primary); }
    .bg-success { background-color: var(--success); }
    .text-success { color: var(--success); }
    .bg-warning { background-color: var(--warning); }
    .card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
    .card-img-top { border-top-left-radius: 10px; border-top-right-radius: 10px; }
    .media-img { border: 2px solid var(--bg-white); box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
    .online-status { background-color: var(--success); }
    .section-padding { padding: 40px 0; }
    .hover-scale:hover { transform: scale(1.02); transition: transform 0.2s ease; }
    .course-card { height: 380px; display: flex; flex-direction: column; }
    .course-image { height: 200px; overflow: hidden; }
    .course-img { width: 100%; height: 100%; object-fit: cover; }
    .course-body { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
    .course-title { font-size: 16px; line-height: 1.4; max-height: 44px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    .btn-details { background-color: var(--primary); color: var(--bg-white); border-radius: 5px; padding: 8px 15px; font-size: 14px; }
    .btn-details:hover { background-color: var(--secondary); }
    .course-badge { padding: 5px 10px; margin-bottom: 5px; border-radius: 5px; font-size: 12px; color: #fff; }
    .course-badge.red { background-color: #dc3545; }
    .course-badge.blue { background-color: #007bff; }
    .course-badge.green { background-color: #28a745; }
    .course-badge-labels { position: absolute; top: 10px; left: 10px; }
    .ribbon.ribbon-blue-bg { font-size: 12px; max-width: 100%; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; background-color: var(--primary); color: var(--bg-white); }
    .wishlist-btn i { transition: color 0.3s ease; }
    .wishlist-btn.wishlisted i { color: var(--red); }
    .cart-message { position: absolute; top: 10px; right: 10px; z-index: 1000; padding: 10px; border-radius: 4px; max-width: 300px; background-color: var(--bg-white); box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
    .alert-info { background-color: #d1ecf1; color: #0c5460; }
    .alert-success { background-color: #d4edda; color: #155724; }
    .alert-danger { background-color: #f8d7da; color: #721c24; }
    .tooltipster-base { z-index: 9999 !important; pointer-events: auto !important; }
    .tooltipster-content { background-color: var(--bg-white); border: 1px solid #ddd; box-shadow: 0 2px 10px rgba(0,0,0,0.2); padding: 10px; max-width: 400px; }
    .responsive-column-half { display: flex; align-items: stretch; }
</style>

<!-- Breadcrumb Area -->
<section class="breadcrumb-area py-5 bg-white" style="background: linear-gradient(135deg, var(--bg-light), #ECEFF1);">
    <div class="container">
        <div class="breadcrumb-content text-center">
            <div class="media media-card align-items-center pb-4">
                <div class="media-img media--img media-img-lg rounded-full position-relative" style="width: 120px; height: 120px;">
                    <img src="{{ $instructor->photo ? asset('upload/instructor_images/' . $instructor->photo) : asset('images/default-instructor.jpg') }}" 
                         alt="{{ $instructor->name ?? 'Instructor' }}'s Profile" 
                         class="rounded-full" 
                         style="width: 100%; height: 100%; object-fit: cover;">
                    <span class="online-status position-absolute bottom-0 end-0 rounded-circle" style="width: 15px; height: 15px; border: 2px solid var(--bg-white);"></span>
                </div>
                <div class="media-body mt-3">
                    <h2 class="section__title fs-30 fw-bold" style="color: var(--text-dark);">{{ $instructor->name ?? 'Unknown Instructor' }}</h2>
                    <span class="d-block lh-18 pt-1 pb-2 text-muted fs-15"><i class="la la-calendar mr-1"></i> Joined {{ $instructor && $instructor->created_at ? \Carbon\Carbon::parse($instructor->created_at)->diffForHumans() : 'N/A' }}</span>
                    <p class="lh-20 fs-15" style="color: var(--text-muted);"><i class="la la-envelope mr-1"></i> {{ $instructor->email ?? 'No email available' }}</p>
                    @if($instructor->location)
                        <p class="lh-20 fs-15" style="color: var(--text-muted);"><i class="la la-map-marker mr-1"></i> {{ $instructor->location }}</p>
                    @endif
                    @if($instructor->website)
                        <p class="lh-20 fs-15"><a href="{{ $instructor->website }}" target="_blank" class="text-primary hover:underline"><i class="la la-globe mr-1"></i> Visit Website</a></p>
                    @endif
                </div>
            </div>
            <ul class="social-icons social-icons-styled d-flex justify-content-center gap-2">
                @foreach(['facebook' => 'la-facebook', 'twitter' => 'la-twitter', 'instagram' => 'la-instagram', 'linkedin' => 'la-linkedin', 'youtube' => 'la-youtube'] as $platform => $icon)
                    @if($instructor->$platform)
                        <li><a href="{{ $instructor->$platform }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-circle"><i class="la {{ $icon }} fs-16"></i></a></li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</section>

<!-- Instructor Details Area -->
<section class="instructor-details-area section-padding bg-light">
    <div class="container">
        <!-- Statistics -->
        <div class="student-details-wrap pb-40px">
            <div class="row justify-content-center g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="counter-item card shadow-sm text-center p-4">
                        <div class="counter__icon icon-element mb-3 bg-primary text-white mx-auto" style="width: 50理论; height: 50px; line-height: 50px;">
                            <i class="la la-users fs-20"></i>
                        </div>
                        <h4 class="counter__title text-primary fs-30 fw-bold" data-count="{{ $totalStudents ?? 0 }}">{{ $totalStudents ?? '0' }}</h4>
                        <p class="counter__meta text-muted fs-15">Total Students</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="counter-item card shadow-sm text-center p-4">
                        <div class="counter__icon icon-element mb-3 bg-warning text-white mx-auto" style="width: 50px; height: 50px; line-height: 50px;">
                            <i class="la la-star fs-20"></i>
                        </div>
                        <h4 class="counter__title text-warning fs-30 fw-bold" data-count="{{ $totalReviews ?? 0 }}">{{ $totalReviews ?? '0' }}</h4>
                        <p class="counter__meta text-muted fs-15">Reviews</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="counter-item card shadow-sm text-center p-4">
                        <div class="counter__icon icon-element mb-3 bg-success text-white mx-auto" style="width: 50px; height: 50px; line-height: 50px;">
                            <i class="la la-book fs-20"></i>
                        </div>
                        <h4 class="counter__title text-success fs-30 fw-bold" data-count="{{ count($courses) }}">{{ count($courses) }}</h4>
                        <p class="counter__meta text-muted fs-15">Courses</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructor Information -->
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card card-item shadow-sm p-4">
                    <h4 class="fw-bold mb-3" style="color: var(--text-dark);"><i class="la la-user mr-2 text-primary"></i> About Me</h4>
                    <p class="lh-24" style="color: var(--text-dark);">{{ $instructor->bio ?? 'No biography available.' }}</p>
                </div>
                <div class="card card-item shadow-sm p-4 mt-4">
                    <h4 class="fw-bold mb-3" style="color: var(--text-dark);"><i class="la la-briefcase mr-2 text-primary"></i> Professional Experience</h4>
                    @if($instructor->experience)
                        <ul class="list-unstyled ps-3">
                            @foreach(explode("\n", $instructor->experience) as $exp)
                                @if(trim($exp))
                                    <li class="mb-2" style="color: var(--text-dark);"><i class="la la-circle fa-xs mr-2 text-primary"></i> {{ trim($exp) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <p class="lh-24" style="color: var(--text-dark);">No experience details provided.</p>
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-item shadow-sm p-4">
                    <h4 class="fw-bold mb-3" style="color: var(--text-dark);"><i class="la la-star mr-2 text-warning"></i> Specialty</h4>
                    <p class="text-primary fw-medium fs-16 bg-white p-2 rounded shadow-sm">{{ $instructor->specialty ?? 'No specialty specified.' }}</p>
                </div>
                <div class="card card-item shadow-sm p-4 mt-4">
                    <h4 class="fw-bold mb-3" style="color: var(--text-dark);"><i class="la la-graduation-cap mr-2 text-primary"></i> Education</h4>
                    @if($instructor->education)
                        <ul class="list-unstyled ps-3">
                            @foreach(explode("\n", $instructor->education) as $edu)
                                @if(trim($edu))
                                    <li class="mb-2" style="color: var(--text-dark);"><i class="la la-circle fa-xs mr-2 text-primary"></i> {{ trim($edu) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <p class="lh-24" style="color: var(--text-dark);">No education details provided.</p>
                    @endif
                </div>
                @auth
                    <div class="mt-4 text-center">
                        <!-- Placeholder for messaging functionality -->
                    </div>
                @else
                    <div class="mt-4 text-center">
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary px-4 py-2">Login to Message</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</section>

<!-- Course Area -->
<section class="course-area section-padding bg-white">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between pb-4">
            <h3 class="fs-28 fw-bold" style="color: var(--text-dark);"><i class="la la-book mr-2 text-primary"></i> My Courses</h3>
            <span class="ribbon bg-primary text-white px-3 py-1 rounded">{{ count($courses) }}</span>
        </div>
        <div class="divider"><span class="bg-primary" style="height: 3px;"></span></div>
        <div class="card-content-wrapper pt-50px">
            <div class="row">
                @forelse($courses as $course)
                    @php
                        $finalPrice = $course->discount_price !== null ? max(0, $course->selling_price - $course->discount_price) : $course->selling_price;
                        $discountPercentage = ($course->selling_price > 0 && $course->discount_price !== null) 
                            ? round(($course->selling_price - $finalPrice) / $course->selling_price * 100) 
                            : 0;
                        $rating = $course->reviews->avg('rating') ?? 0;
                        $reviews_count = $course->reviews->count();
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
                    <div class="col-lg-4 col-md-6 mb-4 responsive-column-half">
                        <div class="card card-item card-preview course-card hover-scale" data-tooltip-content="#tooltip_content_{{ $course->id }}">
                            <div class="course-image">
                                <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}" class="d-block">
                                    <img class="course-img" 
                                         src="{{ $course->course_image ? asset('upload/course_images/thumbnail/' . $course->course_image) : asset('images/default-course.jpg') }}" 
                                         alt="{{ $course->course_name }}">
                                </a>
                                <div class="course-badge-labels">
                                    @if($course->bestseller == 1)
                                        <div class="course-badge red">Bestseller</div>
                                    @endif
                                    @if($course->highestrated == 1)
                                        <div class="course-badge blue">Highest Rated</div>
                                    @endif
                                    @if($course->featured == 1)
                                        <div class="course-badge green">Featured</div>
                                    @endif
                                    @if($course->discount_price == null)
                                        <div class="course-badge blue">New</div>
                                    @else
                                        <div class="course-badge blue">{{ $discountPercentage }}%</div>
                                    @endif
                                </div>
                            </div>
                            <div class="course-body">
                                <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $course->label ?? 'All Levels' }}</h6>
                                <h5 class="course-title">
                                    <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}" class="text-dark">
                                        {{ Str::limit($course->course_name, 40) }}
                                    </a>
                                </h5>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <p class="card-price text-black fw-bold fs-16">
                                        {{ number_format($finalPrice, 2) }} TND
                                        @if($finalPrice < $course->selling_price)
                                            <span class="before-price text-muted fs-14">{{ number_format($course->selling_price, 2) }} TND</span>
                                        @endif
                                    </p>
                                </div>
                                <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}" class="btn btn-details w-100 text-center">
                                    View Details
                                </a>
                            </div>
                            <!-- Tooltip Content -->
                            <div class="tooltip_templates" style="display: none;">
                                <div id="tooltip_content_{{ $course->id }}">
                                    <div class="card-body position-relative">
                                        <p class="card-text pb-2" style="color: var(--text-muted);">
                                            By 
                                            @if($instructor)
                                                <a href="{{ route('instructor.details', $instructor->id) }}" class="text-dark">{{ $instructor->name }}</a>
                                            @else
                                                Unknown Instructor
                                            @endif
                                        </p>
                                        <h5 class="card-title pb-1">
                                            <a href="{{ route('course.details', [$course->id, $course->course_name_slug]) }}" class="text-dark">{{ $course->course_name }}</a>
                                        </h5>
                                        <div class="d-flex align-items-center pb-1">
                                            @if($course->bestseller == 1)
                                                <h6 class="ribbon fs-14 bg-success text-white px-2 py-1 mr-2">Bestseller</h6>
                                            @endif
                                            <p class="text-success fs-14 fw-medium">Updated <span class="fw-bold pl-1">{{ $course->updated_at ? \Carbon\Carbon::parse($course->updated_at)->format('F Y') : 'N/A' }}</span></p>
                                        </div>
                                        <div class="rating-wrap d-flex align-items-center py-2">
                                            <div class="review-stars">
                                                <span class="rating-number fw-bold text-warning">{{ number_format($rating, 1) }}</span>
                                                @for($i = 1; $i <= 5; $i++)
                                                    <span class="la la-star{{ $i <= floor($rating) ? '' : '-o' }}"></span>
                                                @endfor
                                            </div>
                                            <span class="rating-total pl-1 text-muted fs-14">({{ number_format($reviews_count) }})</span>
                                        </div>
                                        <ul class="generic-list-item generic-list-item-bullet d-flex align-items-center fs-14">
                                            <li>{{ $course->label ?? 'All Levels' }}</li>
                                        </ul>
                                        <p class="card-text pt-1 fs-14 lh-22" style="color: var(--text-muted);">{{ Str::limit(strip_tags($course->description ?? 'No description available.'), 100) }}</p>
                                        <ul class="generic-list-item fs-14 py-3">
                                            @forelse($course->goals->take(3) as $goal)
                                                <li><i class="la la-check mr-1 text-success"></i> {{ $goal->goal_name }}</li>
                                            @empty
                                                <li><i class="la la-check mr-1 text-success"></i> Learn key skills for this course</li>
                                                <li><i class="la la-check mr-1 text-success"></i> Boost your knowledge</li>
                                                <li><i class="la la-check mr-1 text-success"></i> Practical exercises included</li>
                                            @endforelse
                                        </ul>
                                        <div class="d-flex justify-content-between align-items-center">
                                            @if($hasPurchased)
                                                <a href="{{ route('course.start', [$course->id, \Str::slug($course->course_name)]) }}" class="btn theme-btn flex-grow-1 mr-3">
                                                    <i class="la la-play-circle fs-18 mr-1"></i> Start Learning
                                                </a>
                                            @else
                                                <button class="btn theme-btn flex-grow-1 mr-3 add-to-cart" data-course-id="{{ $course->id }}" {{ $isInCart ? 'data-in-cart="true"' : '' }}>
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
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-lg-12 text-center py-5">
                        <p class="text-muted fs-18">This instructor has no courses yet.</p>
                        <i class="la la-book-open fs-40" style="color: var(--text-muted);"></i>
                    </div>
                @endforelse
            </div>
            <div class="mt-4">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
</section>

<!-- Scripts -->
@push('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tooltipster/4.2.8/js/tooltipster.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Tooltipster
            $('.card-preview').tooltipster({
                theme: 'tooltipster-shadow',
                interactive: true,
                contentAsHTML: true,
                maxWidth: 400,
                side: 'right',
                distance: 10
            });

            // Counter Animation
            $('.counter__title').each(function() {
                const $this = $(this);
                const countTo = $this.attr('data-count');
                $({ countNum: 0 }).animate({
                    countNum: countTo
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.countNum));
                    },
                    complete: function() {
                        $this.text(this.countNum);
                    }
                });
            });

            // Handle wishlist button clicks
            $('.wishlist-btn').off('click').on('click', function(e) {
                e.preventDefault();
                var $button = $(this);
                var courseId = $button.data('course-id');
                var isWishlisted = $button.hasClass('wishlisted');
                var url = isWishlisted ? '/wishlist/remove/' + courseId : '/wishlist/add/' + courseId;

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
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
                        var response = xhr.responseJSON;
                        alert(response.message || 'An error occurred.');
                    }
                });
            });

            // Handle Add/Remove from Cart button clicks
            $('.add-to-cart').off('click').on('click', function(e) {
                e.preventDefault();
                var $button = $(this);
                var courseId = $button.data('course-id');
                var isInCart = $button.data('in-cart') === true;
                var $message = $('#cart-message-' + courseId);
                var url = isInCart ? '{{ route("cart.remove", ":id") }}'.replace(':id', courseId) : '{{ route("cart.add", ":id") }}'.replace(':id', courseId);
                var method = isInCart ? 'GET' : 'POST';

                if (!courseId) {
                    $message.html('<div class="alert alert-danger">Error: Course ID is missing.</div>');
                    setTimeout(function() { $message.empty(); }, 3000);
                    return;
                }

                $.ajax({
                    url: url,
                    method: method,
                    data: isInCart ? {} : { _token: $('meta[name="csrf-token"]').attr('content') },
                    dataType: 'json',
                    success: function(response) {
                        if (response.redirect) {
                            let tempCart = JSON.parse(localStorage.getItem('tempCart')) || [];
                            const itemIndex = tempCart.findIndex(item => item.courseId === response.course_id);
                            if (itemIndex > -1) {
                                tempCart[itemIndex].quantity += 1;
                            } else {
                                tempCart.push({ courseId: response.course_id, quantity: 1 });
                            }
                            localStorage.setItem('tempCart', JSON.stringify(tempCart));
                            $message.html('<div class="alert alert-info">Please log in to add this course to your cart.</div>');
                            setTimeout(function() { window.location.href = response.redirect; }, 1500);
                        } else if (response.success) {
                            $message.html('<div class="alert alert-success">' + response.message + '</div>');
                            if (isInCart) {
                                $button.data('in-cart', false).removeAttr('data-in-cart');
                                $button.prop('disabled', false).html('<i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart');
                            } else {
                                $button.data('in-cart', true);
                                $button.prop('disabled', true).html('<i class="la la-shopping-cart fs-18 mr-1"></i> In Cart');
                            }
                            if ($('#cartQty').length) {
                                $('#cartQty').text(response.cartCount);
                            }
                            if ($('#cartSubTotal').length) {
                                $('#cartSubTotal').text('TND ' + response.cartSubTotal);
                            }
                            $.ajax({
                                url: '{{ route("cart") }}',
                                method: 'GET',
                                success: function(html) {
                                    var $newCart = $(html).find('#cartDropdown').html();
                                    $('#cartDropdown').html($newCart);
                                    bindCartDropdownHandlers();
                                }
                            });
                        } else {
                            $message.html('<div class="alert alert-info">' + (response.info || response.message || 'Action completed.') + '</div>');
                        }
                        setTimeout(function() { $message.empty(); }, 3000);
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON || {};
                        if (xhr.status === 401 && response.redirect) {
                            let tempCart = JSON.parse(localStorage.getItem('tempCart')) || [];
                            const itemIndex = tempCart.findIndex(item => item.courseId === response.course_id);
                            if (itemIndex > -1) {
                                tempCart[itemIndex].quantity += 1;
                            } else {
                                tempCart.push({ courseId: response.course_id, quantity: 1 });
                            }
                            localStorage.setItem('tempCart', JSON.stringify(tempCart));
                            $message.html('<div class="alert alert-info">Please log in to add this course to your cart.</div>');
                            setTimeout(function() { window.location.href = response.redirect; }, 1500);
                        } else {
                            $message.html('<div class="alert alert-danger">' + (response.error || response.message || 'An error occurred.') + '</div>');
                            setTimeout(function() { $message.empty(); }, 3000);
                        }
                    }
                });
            });

            function bindCartDropdownHandlers() {
                $('#cartDropdown .remove-from-cart').off('click').on('click', function(e) {
                    e.preventDefault();
                    var courseId = $(this).data('id');
                    var $cartItem = $('#cart-item-' + courseId);
                    var $message = $('#cart-message-' + courseId).length ? $('#cart-message-' + courseId) : $('<div class="cart-message"></div>').appendTo('body');

                    $.ajax({
                        url: '{{ route("cart.remove", ":id") }}'.replace(':id', courseId),
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.redirect) {
                                $message.html('<div class="alert alert-info">Please log in to remove this course from your cart.</div>');
                                setTimeout(function() { window.location.href = response.redirect; }, 1500);
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
                            var response = xhr.responseJSON || {};
                            $message.html('<div class="alert alert-danger">' + (response.message || 'An error occurred.') + '</div>');
                            setTimeout(function() { $message.empty(); }, 3000);
                        }
                    });
                });
            }

            bindCartDropdownHandlers();
        });
    </script>
@endpush
@endsection