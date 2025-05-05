@extends('frontend.master')

@section('title')
{{ $instructor->name ?? 'Instructor Not Found' }} | Easy Learning
@endsection

@section('home')
<!-- ================================
    START BREADCRUMB AREA
================================= -->
<section class="breadcrumb-area py-5 bg-white pattern-bg" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
    <div class="container">
        <div class="breadcrumb-content text-center">
            <div class="media media-card align-items-center pb-4 animate__animated animate__fadeInUp">
                <div class="media-img media--img media-img-lg rounded-full position-relative overflow-hidden" style="width: 150px; height: 150px;">
                    <img src="{{ $instructor->photo ? asset('storage/upload/instructor_images/' . $instructor->photo) : asset('upload/no_image.jpg') }}" 
                         alt="{{ $instructor->name ?? 'Instructor' }}'s Profile" 
                         class="rounded-full shadow-lg" 
                         style="width: 100%; height: 100%; object-fit: cover; object-position: center; border: 4px solid #fff;">
                    <span class="online-status bg-success position-absolute bottom-0 end-0 rounded-circle" style="width: 20px; height: 20px; border: 2px solid #fff;"></span>
                </div>
                <div class="media-body mt-3">
                    <h2 class="section__title fs-35 fw-bold text-dark animate__animated animate__fadeIn" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">{{ $instructor->name ?? 'Unknown Instructor' }}</h2>
                    <span class="d-block lh-18 pt-1 pb-2 text-muted fs-16"><i class="la la-calendar mr-1"></i> Joined {{ $instructor && $instructor->created_at ? \Carbon\Carbon::parse($instructor->created_at)->diffForHumans() : 'N/A' }}</span>
                    <p class="lh-20 fs-16 text-gray-600"><i class="la la-envelope mr-1"></i> {{ $instructor->email ?? 'No email available' }}</p>
                    @if($instructor->location)
                        <p class="lh-20 fs-16 text-gray-600"><i class="la la-map-marker mr-1"></i> {{ $instructor->location }}</p>
                    @endif
                    @if($instructor->website)
                        <p class="lh-20 fs-16"><a href="{{ $instructor->website }}" target="_blank" class="text-primary hover:underline"><i class="la la-globe mr-1"></i> Visit Website</a></p>
                    @endif
                </div>
            </div><!-- end media -->
            <ul class="social-icons social-icons-styled social--icons-styled d-flex justify-content-center gap-3 animate__animated animate__fadeInUp animate__delay-1s">
                @foreach(['facebook' => 'la-facebook', 'twitter' => 'la-twitter', 'instagram' => 'la-instagram', 'linkedin' => 'la-linkedin', 'youtube' => 'la-youtube'] as $platform => $icon)
                    @if($instructor->$platform)
                        <li><a href="{{ $instructor->$platform }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-circle"><i class="la {{ $icon }} fs-18"></i></a></li>
                    @endif
                @endforeach
            </ul>
        </div><!-- end breadcrumb-content -->
    </div><!-- end container -->
</section><!-- end breadcrumb-area -->
<!-- ================================
    END BREADCRUMB AREA
================================= -->

<!-- ================================
    START INSTRUCTOR DETAILS AREA
================================= -->
<section class="instructor-details-area pt-50px pb-70px bg-light">
    <div class="container">
        <!-- Statistiques -->
        <div class="student-details-wrap pb-40px">
            <div class="row justify-content-center g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="counter-item card shadow-sm text-center p-4 animate__animated animate__zoomIn">
                        <div class="counter__icon icon-element mb-3 bg-primary text-white mx-auto" style="width: 60px; height: 60px; line-height: 60px;">
                            <i class="la la-users fs-24"></i>
                        </div>
                        <h4 class="counter__title text-primary fs-35 fw-bold" data-count="{{ $totalStudents ?? 0 }}">{{ $totalStudents ?? '0' }}</h4>
                        <p class="counter__meta text-muted fs-16">Total Students</p>
                    </div>
                </div><!-- end col-lg-4 -->
                <div class="col-lg-4 col-md-6">
                    <div class="counter-item card shadow-sm text-center p-4 animate__animated animate__zoomIn" data-delay="0.2s">
                        <div class="counter__icon icon-element mb-3 bg-warning text-white mx-auto" style="width: 60px; height: 60px; line-height: 60px;">
                            <i class="la la-star fs-24"></i>
                        </div>
                        <h4 class="counter__title text-warning fs-35 fw-bold" data-count="{{ $totalReviews ?? 0 }}">{{ $totalReviews ?? '0' }}</h4>
                        <p class="counter__meta text-muted fs-16">Reviews</p>
                    </div>
                </div><!-- end col-lg-4 -->
                <div class="col-lg-4 col-md-6">
                    <div class="counter-item card shadow-sm text-center p-4 animate__animated animate__zoomIn" data-delay="0.4s">
                        <div class="counter__icon icon-element mb-3 bg-success text-white mx-auto" style="width: 60px; height: 60px; line-height: 60px;">
                            <i class="la la-book fs-24"></i>
                        </div>
                        <h4 class="counter__title text-success fs-35 fw-bold" data-count="{{ count($courses) }}">{{ count($courses) }}</h4>
                        <p class="counter__meta text-muted fs-16">Courses</p>
                    </div>
                </div><!-- end col-lg-4 -->
            </div><!-- end row -->
        </div><!-- end student-details-wrap -->

        <!-- Toutes les Informations -->
        <div class="row g-4">
            <!-- Colonne Gauche -->
            <div class="col-lg-6">
                <div class="card card-item shadow-sm p-4 animate__animated animate__fadeInUp">
                    <h4 class="fw-bold text-dark mb-3"><i class="la la-user mr-2 text-primary"></i> About Me</h4>
                    <p class="text-gray-700 lh-24">{{ $instructor->bio ?? 'No biography available.' }}</p>
                </div>
                <div class="card card-item shadow-sm p-4 mt-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <h4 class="fw-bold text-dark mb-3"><i class="la la-briefcase mr-2 text-primary"></i> Experience</h4>
                    <p class="text-gray-700 lh-24">{{ $instructor->experience ?? 'No experience details provided.' }}</p>
                </div>
            </div><!-- end col-lg-6 -->

            <!-- Colonne Droite -->
            <div class="col-lg-6">
                <div class="card card-item shadow-sm p-4 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                    <h4 class="fw-bold text-dark mb-3"><i class="la la-star mr-2 text-warning"></i> Specialty</h4>
                    <p class="text-primary fw-medium fs-16 bg-white p-2 rounded shadow-sm">{{ $instructor->specialty ?? 'No specialty specified.' }}</p>
                </div>
                <div class="card card-item shadow-sm p-4 mt-4 animate__animated animate__fadeInUp" style="animation-delay: 0.6s;">
                    <h4 class="fw-bold text-dark mb-3"><i class="la la-graduation-cap mr-2 text-primary"></i> Education</h4>
                    <p class="text-gray-700 lh-24">{{ $instructor->education ?? 'No education details provided.' }}</p>
                </div>
                @auth
                    <div class="mt-4 text-center animate__animated animate__fadeInUp" style="animation-delay: 0.8s;">
                        <!-- Placeholder for potential future functionality like messaging -->
                    </div>
                @else
                    <div class="mt-4 text-center animate__animated animate__fadeInUp" style="animation-delay: 0.8s;">
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary px-4 py-2">Login to Message</a>
                    </div>
                @endauth
            </div><!-- end col-lg-6 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end instructor-details-area -->
<!-- ================================
    END INSTRUCTOR DETAILS AREA
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
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
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

<section class="course-area section-padding bg-white">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between pb-4">
            <h3 class="fs-28 fw-bold text-dark"><i class="la la-book mr-2 text-primary"></i> My Courses</h3>
            <span class="ribbon ribbon-lg bg-primary text-white px-3 py-1 rounded">{{ count($courses) }}</span>
        </div>
        <div class="divider"><span class="bg-primary" style="height: 3px;"></span></div>
        <div class="row pt-40px g-4">
            @forelse($courses as $course)
                @php
                    $finalPrice = $course->discount_price !== null ? max(0, $course->selling_price - $course->discount_price) : $course->selling_price;
                    $discountPercentage = ($course->selling_price > 0 && $course->discount_price !== null) ? round(($course->selling_price - $finalPrice) / $course->selling_price * 100) : 0;
                    $rating = $course->reviews->avg('rating') ?? 0;
                    $reviews_count = $course->reviews->count();
                    $isWishlisted = auth()->check() && \App\Models\Wishlist::where('trackable_type', 'App\Models\User')
                        ->where('trackable_id', auth()->id())
                        ->where('course_id', $course->id)
                        ->exists();
                @endphp
                <div class="col-lg-4 col-md-6">
                    <div class="card card-item card-preview shadow-sm hover-scale animate__animated animate__fadeInUp" data-tooltip-content="#tooltip_content_{{ $course->id }}">
                        <div class="card-image position-relative">
                            <a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}" class="d-block">
                                <img class="card-img-top lazy rounded-top" src="{{ asset('storage/upload/course_images/thumbnail/' . ($course->course_image ?? 'default-course.jpg')) }}" alt="{{ $course->course_name }}" style="height: 200px; object-fit: cover;" onerror="this.src='{{ asset('images/default-course.jpg') }}';">
                            </a>
                            <div class="course-badge-labels position-absolute top-10 start-10">
                                @if($course->bestseller == 1)
                                    <div class="course-badge bg-success text-white px-2 py-1 rounded">Bestseller</div>
                                @endif
                                @if($course->discount_price == null)
                                    <div class="course-badge bg-info text-white px-2 py-1 rounded mt-1">New</div>
                                @else
                                    <div class="course-badge bg-danger text-white px-2 py-1 rounded mt-1">{{ $discountPercentage }}% OFF</div>
                                @endif
                            </div>
                        </div><!-- end card-image -->
                        <div class="card-body p-3">
                            <h6 class="ribbon ribbon-blue-bg fs-14 mb-2">{{ $course->label ?? 'All Levels' }}</h6>
                            <h5 class="card-title fs-18 fw-semibold"><a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}" class="text-dark hover:underline">{{ Str::limit($course->course_name, 40) }}</a></h5>
                            <p class="card-text fs-14 text-muted"><a href="{{ route('instructor.details', $course->courseable->id) }}">{{ $course->courseable->name ?? 'Unknown Instructor' }}</a></p>
                            <div class="rating-wrap d-flex align-items-center py-1">
                                <div class="review-stars">
                                    <span class="rating-number fw-bold text-warning">{{ number_format($rating, 1) }}</span>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="la la-star{{ $i <= floor($rating) ? ' text-warning' : '-o' }}"></span>
                                    @endfor
                                </div>
                                <span class="rating-total pl-1 text-muted fs-14">({{ number_format($reviews_count) }})</span>
                            </div><!-- end rating-wrap -->
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                @if($finalPrice < $course->selling_price)
                                    <p class="card-price text-dark fw-bold fs-16">{{ number_format($finalPrice, 2) }} TND <span class="before-price text-muted fs-14 text-decoration-line-through">{{ number_format($course->selling_price, 2) }} TND</span></p>
                                @else
                                    <p class="card-price text-dark fw-bold fs-16">{{ number_format($finalPrice, 2) }} TND</p>
                                @endif
                                @auth
                                    <button class="wishlist-btn icon-element icon-element-sm shadow-sm cursor-pointer border-0 bg-transparent {{ $isWishlisted ? 'wishlisted' : '' }}"
                                            data-course-id="{{ $course->id }}"
                                            title="{{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                                        <i class="la {{ $isWishlisted ? 'la-heart' : 'la-heart-o' }} fs-18"></i>
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="icon-element icon-element-sm shadow-sm cursor-pointer text-primary" title="Login to add to Wishlist">
                                        <i class="la la-heart-o fs-18"></i>
                                    </a>
                                @endauth
                            </div>
                        </div><!-- end card-body -->
                    </div><!-- end card -->
                </div><!-- end col-lg-4 -->
            @empty
                <div class="col-lg-12 text-center py-5">
                    <p class="text-muted fs-18">This instructor has no courses yet.</p>
                    <i class="la la-book-open fs-40 text-gray-400"></i>
                </div>
            @endforelse
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end course-area -->
<!--======================================
    END COURSE AREA
======================================-->

<!-- Tooltip Templates -->
@foreach($courses as $course)
    @php
        $finalPrice = $course->discount_price !== null ? max(0, $course->selling_price - $course->discount_price) : $course->selling_price;
        $rating = $course->reviews->avg('rating') ?? 0;
        $reviews_count = $course->reviews->count();
        $isWishlisted = auth()->check() && \App\Models\Wishlist::where('trackable_type', 'App\Models\User')
            ->where('trackable_id', auth()->id())
            ->where('course_id', $course->id)
            ->exists();
        $isInCart = \Darryldecode\Cart\Facades\CartFacade::get($course->id) !== null;
        $hasPurchased = Auth::check() && App\Models\Order::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('payment_status', 'paid')
            ->exists();
    @endphp
    <div class="tooltip_templates" style="display: none;">
        <div id="tooltip_content_{{ $course->id }}">
            <div class="card card-item shadow-sm">
                <div class="card-body p-3">
                    <p class="card-text fs-14 text-muted pb-1">By <a href="{{ route('instructor.details', $course->courseable->id) }}" class="text-dark">{{ $course->courseable->name ?? 'Unknown Instructor' }}</a></p>
                    <h5 class="card-title fs-18 fw-semibold pb-1"><a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}" class="text-dark">{{ $course->course_name }}</a></h5>
                    <div class="d-flex align-items-center pb-1">
                        @if($course->bestseller == 1)
                            <h6 class="ribbon fs-14 bg-success text-white px-2 py-1 mr-2">Bestseller</h6>
                        @endif
                        <p class="text-success fs-14">Updated <span class="fw-bold pl-1">{{ $course->updated_at ? \Carbon\Carbon::parse($course->updated_at)->format('F Y') : 'N/A' }}</span></p>
                    </div>
                    <div class="rating-wrap d-flex align-items-center py-1">
                        <div class="review-stars">
                            <span class="rating-number fw-bold text-warning">{{ number_format($rating, 1) }}</span>
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="la la-star{{ $i <= floor($rating) ? ' text-warning' : '-o' }}"></span>
                            @endfor
                        </div>
                        <span class="rating-total pl-1 text-muted fs-14">({{ number_format($reviews_count) }})</span>
                    </div>
                    <ul class="generic-list-item generic-list-item-bullet fs-14 py-2">
                        <li>{{ $course->duration ?? 'N/A' }}</li>
                        <li>{{ $course->label ?? 'All Levels' }}</li>
                    </ul>
                    <p class="card-text fs-14 lh-22 text-gray-600">{{ Str::limit($course->description ?? 'No description available.', 100) }}</p>
                    @php
                        $goals = App\Models\CourseGoal::where('goalable_type', 'App\\Models\\Course')
                            ->where('goalable_id', $course->id)
                            ->orderBy('id', 'DESC')
                            ->get();
                    @endphp
                    <ul class="generic-list-item fs-14 py-2">
                        @forelse($goals as $goal)
                            <li><i class="la la-check mr-1 text-success"></i> {{ $goal->goal_name }}</li>
                        @empty
                            <li><i class="la la-check mr-1 text-success"></i> Learn key skills for this course</li>
                            <li><i class="la la-check mr-1 text-success"></i> Boost your knowledge</li>
                        @endforelse
                    </ul>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        @if($hasPurchased)
                            <a href="{{ route('course.start', [$course->id, $course->course_name_slug]) }}" class="btn theme-btn btn-sm px-3">
                                <i class="la la-play-circle mr-1 fs-18"></i> Start Learning
                            </a>
                        @else
                            <button class="btn theme-btn btn-sm px-3 add-to-cart" data-course-id="{{ $course->id }}" {{ $isInCart ? 'data-in-cart="true"' : '' }}>
                                <i class="la la-shopping-cart mr-1"></i> {{ $isInCart ? 'In Cart' : 'Add to Cart' }}
                            </button>
                        @endif
                        @auth
                            <button class="wishlist-btn icon-element icon-element-sm shadow-sm cursor-pointer border-0 bg-transparent {{ $isWishlisted ? 'wishlisted' : '' }}"
                                    data-course-id="{{ $course->id }}"
                                    title="{{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                                <i class="la {{ $isWishlisted ? 'la-heart' : 'la-heart-o' }} fs-18"></i>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="icon-element icon-element-sm shadow-sm cursor-pointer text-primary" title="Login to add to Wishlist">
                                <i class="la la-heart-o fs-18"></i>
                            </a>
                        @endauth
                    </div>
                    <div id="cart-message-{{ $course->id }}" class="cart-message"></div>
                </div>
            </div><!-- end card -->
        </div>
    </div><!-- end tooltip_templates -->
@endforeach

<!-- Scripts -->
@push('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tooltipster/4.2.8/js/tooltipster.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" integrity="sha512-c42qTSw/wPZ3/5LBzD+Bw5f7bSF2oxou6wEb+I/lqeaKV5FDIfMvvRp772y4jcJLKuGUOpbJMdg/BTl50fJYAw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script>
        $(document).ready(function() {
            console.log('jQuery loaded and document ready');

            // Initialiser Tooltipster
            $('.card-preview').tooltipster({
                theme: 'tooltipster-shadow',
                interactive: true,
                contentAsHTML: true,
                maxWidth: 400,
                side: 'right',
                distance: 10
            });
            console.log('Tooltipster initialized');

            // Animation des compteurs
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

            // Effet hover pour les cartes
            $('.hover-scale').hover(
                function() { $(this).css('transform', 'scale(1.05)'); },
                function() { $(this).css('transform', 'scale(1)'); }
            );

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
                            // Sync all wishlist buttons for this course (card and tooltip)
                            $('.wishlist-btn[data-course-id="' + courseId + '"]').each(function() {
                                $(this).toggleClass('wishlisted', !isWishlisted);
                                $(this).find('i').toggleClass('la-heart-o', isWishlisted).toggleClass('la-heart', !isWishlisted);
                                $(this).attr('title', isWishlisted ? 'Add to Wishlist' : 'Remove from Wishlist');
                            });
                            // Trigger wishlist count update (if applicable)
                            $(document).trigger('wishlist-updated');
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
                var $message = $('#cart-message-' + courseId).length ? $('#cart-message-' + courseId) : $('<div class="cart-message"></div>').appendTo('body');
                var url = isInCart ? '{{ route("cart.remove", ":id") }}'.replace(':id', courseId) : '{{ route("cart.add", ":id") }}'.replace(':id', courseId);
                var method = isInCart ? 'GET' : 'POST';

                if (!courseId) {
                    console.error('Course ID is undefined');
                    $message.html('<div class="alert alert-danger">Error: Course ID is missing.</div>').css({
                        position: 'fixed',
                        top: '20px',
                        right: '20px',
                        zIndex: 10000
                    });
                    setTimeout(function() { $message.remove(); }, 3000);
                    return;
                }

                console.log('Sending AJAX request for course ID:', courseId, 'Action:', isInCart ? 'Remove' : 'Add');
                $.ajax({
                    url: url,
                    method: method,
                    data: isInCart ? {} : { _token: $('meta[name="csrf-token"]').attr('content') },
                    dataType: 'json',
                    beforeSend: function() {
                        $button.prop('disabled', true).html('<i class="la la-shopping-cart mr-1"></i> ' + (isInCart ? 'Removing...' : 'Adding...'));
                    },
                    success: function(response) {
                        console.log('Cart AJAX success:', response);
                        $button.prop('disabled', false);
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
                            $message.html('<div class="alert alert-info">Please log in to add this course to your cart.</div>').css({
                                position: 'fixed',
                                top: '20px',
                                right: '20px',
                                zIndex: 10000
                            });
                            setTimeout(function() {
                                console.log('Redirecting to:', response.redirect);
                                window.location.href = response.redirect;
                            }, 1500);
                        } else if (response.success) {
                            $message.html('<div class="alert alert-success">' + response.message + '</div>').css({
                                position: 'fixed',
                                top: '20px',
                                right: '20px',
                                zIndex: 10000
                            });
                            if (isInCart) {
                                // Remove from cart
                                $button.data('in-cart', false).removeAttr('data-in-cart');
                                $button.html('<i class="la la-shopping-cart mr-1"></i> Add to Cart');
                            } else {
                                // Add to cart
                                $button.data('in-cart', true);
                                $button.html('<i class="la la-shopping-cart mr-1"></i> In Cart');
                            }
                            // Update all buttons for this course
                            $('.add-to-cart[data-course-id="' + courseId + '"]').each(function() {
                                $(this).data('in-cart', !isInCart).prop('disabled', !isInCart)
                                    .html('<i class="la la-shopping-cart mr-1"></i> ' + (isInCart ? 'Add to Cart' : 'In Cart'));
                            });
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
                            $message.html('<div class="alert alert-info">' + (response.info || response.message || 'Action completed.') + '</div>').css({
                                position: 'fixed',
                                top: '20px',
                                right: '20px',
                                zIndex: 10000
                            });
                        }
                        setTimeout(function() { $message.remove(); }, 3000);
                    },
                    error: function(xhr) {
                        console.error('Cart AJAX error:', xhr);
                        $button.prop('disabled', false).html('<i class="la la-shopping-cart mr-1"></i> ' + (isInCart ? 'In Cart' : 'Add to Cart'));
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
                            $message.html('<div class="alert alert-info">Please log in to add this course to your cart.</div>').css({
                                position: 'fixed',
                                top: '20px',
                                right: '20px',
                                zIndex: 10000
                            });
                            setTimeout(function() {
                                console.log('Redirecting to:', response.redirect);
                                window.location.href = response.redirect;
                            }, 1500);
                        } else {
                            $message.html('<div class="alert alert-danger">' + (response.error || response.message || 'An error occurred.') + '</div>').css({
                                position: 'fixed',
                                top: '20px',
                                right: '20px',
                                zIndex: 10000
                            });
                            setTimeout(function() { $message.remove(); }, 3000);
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
                                $message.html('<div class="alert alert-info">Please log in to remove this course from your cart.</div>').css({
                                    position: 'fixed',
                                    top: '20px',
                                    right: '20px',
                                    zIndex: 10000
                                });
                                setTimeout(function() {
                                    window.location.href = response.redirect;
                                }, 1500);
                            } else if (response.success) {
                                $cartItem.remove();
                                $message.html('<div class="alert alert-success">' + response.message + '</div>').css({
                                    position: 'fixed',
                                    top: '20px',
                                    right: '20px',
                                    zIndex: 10000
                                });
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
                                        .html('<i class="la la-shopping-cart mr-1"></i> Add to Cart');
                                });
                            } else {
                                $message.html('<div class="alert alert-info">' + (response.message || 'Action completed.') + '</div>').css({
                                    position: 'fixed',
                                    top: '20px',
                                    right: '20px',
                                    zIndex: 10000
                                });
                            }
                            setTimeout(function() { $message.remove(); }, 3000);
                        },
                        error: function(xhr) {
                            console.error('Remove from cart AJAX error:', xhr);
                            var response = xhr.responseJSON || {};
                            $message.html('<div class="alert alert-danger">' + (response.message || 'An error occurred.') + '</div>').css({
                                position: 'fixed',
                                top: '20px',
                                right: '20px',
                                zIndex: 10000
                            });
                            setTimeout(function() { $message.remove(); }, 3000);
                        }
                    });
                });
            }

            // Initial binding for cart dropdown handlers
            bindCartDropdownHandlers();
        });
    </script>
@endpush
@endsection