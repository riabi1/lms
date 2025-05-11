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
                        <h4 class="counter__title text-success fs-35 fw-bold" data-count="{{ $courses->total() }}">{{ $courses->total() }}</h4>
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
            <h5 class="ribbon ribbon-lg mb-2">Courses by {{ $instructor->name ?? 'Instructor' }}</h5>
            <h2 class="section__title">Explore {{ $instructor->name ?? 'Instructor' }}'s Courses</h2>
            <span class="section-divider"></span>
        </div><!-- end section-heading -->

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

        <div class="card-content-wrapper bg-gray pt-50px pb-120px">
            <div class="container">
                <div class="tab-content" id="myTabContent">
                    <!-- All Courses Tab -->
                    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                        <div class="row">
                            @forelse($courses as $course)
                                <div class="col-lg-4 responsive-column-half">
                                    <div class="card card-item card-preview" data-tooltip-content="#tooltip_content_{{ $course->id }}">
                                        <div class="card-image">
                                            <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}" class="d-block">
                                                <img class="card-img-top lazy" 
                                                     src="{{ $course->course_image ? asset('storage/upload/course_images/thumbnail/' . $course->course_image) : asset('images/default-course.jpg') }}" 
                                                     alt="Course image">
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
                                                @if ($course->discount_percentage > 0)
                                                    <div class="course-badge blue">{{ $course->discount_percentage }}% OFF</div>
                                                @else
                                                    <div class="course-badge blue">New</div>
                                                @endif
                                            </div>
                                        </div><!-- end card-image -->
                                        <div class="card-body">
                                            <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $course->label ?? 'All Levels' }}</h6>
                                            <h5 class="card-title">
                                                <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}">{{ Str::limit($course->course_name, 40) }}</a>
                                            </h5>
                                            <div class="d-flex justify-content-between align-items-center">
                                                @if ($course->final_price < $course->selling_price)
                                                    <p class="card-price text-black font-weight-bold">{{ number_format($course->final_price, 2) }} TND
                                                        <span class="before-price font-weight-medium">{{ number_format($course->selling_price, 2) }} TND</span>
                                                    </p>
                                                @else
                                                    <p class="card-price text-black font-weight-bold">{{ number_format($course->final_price, 2) }} TND</p>
                                                @endif
                                            </div>
                                        </div><!-- end card-body -->
                                    </div><!-- end card -->

                                    <!-- Tooltip Content -->
                                    <div class="tooltip_templates" style="display: none;">
                                        <div id="tooltip_content_{{ $course->id }}">
                                            <div class="card-body position-relative">
                                                <p class="card-text pb-2">By 
                                                    <a href="{{ route('instructor.details', $instructor->id) }}">{{ $instructor->name ?? 'Unknown Instructor' }}</a>
                                                </p>
                                                <h5 class="card-title pb-1">
                                                    <a href="{{ route('course.details', [$course->id, $course->course_name_slug]) }}">{{ $course->course_name }}</a>
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
                                                    @if ($course->has_purchased)
                                                        <a href="{{ route('course.start', [$course->id, \Str::slug($course->course_name)]) }}" class="btn theme-btn flex-grow-1 mr-3">
                                                            <i class="la la-play-circle fs-18 mr-1"></i> Start Learning
                                                        </a>
                                                    @else
                                                        <button class="btn theme-btn flex-grow-1 mr-3 add-to-cart" data-course-id="{{ $course->id }}" {{ $course->is_in_cart ? 'data-in-cart="true"' : '' }}>
                                                            <i class="la la-shopping-cart fs-18 mr-1"></i> {{ $course->is_in_cart ? 'In Cart' : 'Add to Cart' }}
                                                        </button>
                                                    @endif
                                                    @auth
                                                        <button class="wishlist-btn icon-element icon-element-sm shadow-sm cursor-pointer border-0 bg-transparent {{ $course->is_wishlisted ? 'wishlisted' : '' }}"
                                                                data-course-id="{{ $course->id }}"
                                                                title="{{ $course->is_wishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                                                            <i class="la {{ $course->is_wishlisted ? 'la-heart' : 'la-heart-o' }}"></i>
                                                        </button>
                                                    @else
                                                        <a href="{{ route('login') }}" class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Login to add to Wishlist">
                                                            <i class="la la-heart-o"></i>
                                                        </a>
                                                    @endif
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
                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $courses->links() }}
                        </div>
                    </div><!-- end tab-pane -->

                    <!-- Category-wise Tabs -->
                    @foreach ($categories as $category)
                        <div class="tab-pane fade" id="category{{ $category->id }}" role="tabpanel" aria-labelledby="category-{{ $category->id }}-tab">
                            <div class="row">
                                @php
                                    $catwiseCourses = $courses->filter(function ($course) use ($category) {
                                        return optional($course->subcategory)->category_id == $category->id;
                                    });
                                @endphp
                                @forelse ($catwiseCourses as $course)
                                    <div class="col-lg-4 responsive-column-half">
                                        <div class="card card-item card-preview" data-tooltip-content="#tooltip_content_{{ $course->id }}">
                                            <div class="card-image">
                                                <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}" class="d-block">
                                                    <img class="card-img-top lazy" 
                                                         src="{{ $course->course_image ? asset('storage/upload/course_images/thumbnail/' . $course->course_image) : asset('images/default-course.jpg') }}" 
                                                         alt="Course image">
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
                                                    @if ($course->discount_percentage > 0)
                                                        <div class="course-badge blue">{{ $course->discount_percentage }}% OFF</div>
                                                    @else
                                                        <div class="course-badge blue">New</div>
                                                    @endif
                                                </div>
                                            </div><!-- end card-image -->
                                            <div class="card-body">
                                                <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $course->label ?? 'All Levels' }}</h6>
                                                <h5 class="card-title">
                                                    <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}">{{ Str::limit($course->course_name, 40) }}</a>
                                                </h5>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    @if ($course->final_price < $course->selling_price)
                                                        <p class="card-price text-black font-weight-bold">{{ number_format($course->final_price, 2) }} TND
                                                            <span class="before-price font-weight-medium">{{ number_format($course->selling_price, 2) }} TND</span>
                                                        </p>
                                                    @else
                                                        <p class="card-price text-black font-weight-bold">{{ number_format($course->final_price, 2) }} TND</p>
                                                    @endif
                                                </div>
                                            </div><!-- end card-body -->
                                        </div><!-- end card -->

                                        <!-- Tooltip Content (Repeated for each course to ensure uniqueness) -->
                                        <div class="tooltip_templates" style="display: none;">
                                            <div id="tooltip_content_{{ $course->id }}">
                                                <div class="card-body position-relative">
                                                    <p class="card-text pb-2">By 
                                                        <a href="{{ route('instructor.details', $instructor->id) }}">{{ $instructor->name ?? 'Unknown Instructor' }}</a>
                                                    </p>
                                                    <h5 class="card-title pb-1">
                                                        <a href="{{ route('course.details', [$course->id, $course->course_name_slug]) }}">{{ $course->course_name }}</a>
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
                                                        @if ($course->has_purchased)
                                                            <a href="{{ route('course.start', [$course->id, \Str::slug($course->course_name)]) }}" class="btn theme-btn flex-grow-1 mr-3">
                                                                <i class="la la-play-circle fs-18 mr-1"></i> Start Learning
                                                            </a>
                                                        @else
                                                            <button class="btn theme-btn flex-grow-1 mr-3 add-to-cart" data-course-id="{{ $course->id }}" {{ $course->is_in_cart ? 'data-in-cart="true"' : '' }}>
                                                                <i class="la la-shopping-cart fs-18 mr-1"></i> {{ $course->is_in_cart ? 'In Cart' : 'Add to Cart' }}
                                                            </button>
                                                        @endif
                                                        @auth
                                                            <button class="wishlist-btn icon-element icon-element-sm shadow-sm cursor-pointer border-0 bg-transparent {{ $course->is_wishlisted ? 'wishlisted' : '' }}"
                                                                    data-course-id="{{ $course->id }}"
                                                                    title="{{ $course->is_wishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                                                                <i class="la {{ $course->is_wishlisted ? 'la-heart' : 'la-heart-o' }}"></i>
                                                            </button>
                                                        @else
                                                            <a href="{{ route('login') }}" class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Login to add to Wishlist">
                                                                <i class="la la-heart-o"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <div id="cart-message-{{ $course->id }}" class="cart-message"></div>
                                                </div>
                                            </div>
                                        </div><!-- end tooltip_templates -->
                                    </div><!-- end col-lg-4 -->
                                @empty
                                    <div class="col-12">
                                        <h5 class="text-danger text-center">No Courses Found in this Category</h5>
                                    </div>
                                @endforelse
                            </div><!-- end row -->
                        </div><!-- end tab-pane -->
                    @endforeach
                </div><!-- end tab-content -->
                <div class="more-btn-box mt-4 text-center">
                    <a href="{{ route('course.list') }}" class="btn theme-btn">Browse all Courses <i class="la la-arrow-right icon ml-1"></i></a>
                </div><!-- end more-btn-box -->
            </div><!-- end container -->
        </div><!-- end card-content-wrapper -->
    </div><!-- end container -->
</section><!-- end course-area -->
<!--======================================
    END COURSE AREA
======================================-->

<!-- Scripts -->
@push('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tooltipster/4.2.8/js/tooltipster.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" integrity="sha512-c42qTSw/wPZ3/5LBzD+Bw5f7bSF2oxou6wEb+I/lqeaKV5FDIfMvvRp772y4jcJLKuGUOpbJMdg/BTl50fJYAw==" crossorigin="anonymous" referrerpolicy="no-referrer">
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
                            $('.wishlist-btn[data-course-id="' + courseId + '"]').each(function() {
                                $(this).toggleClass('wishlisted', !isWishlisted);
                                $(this).find('i').toggleClass('la-heart-o', isWishlisted).toggleClass('la-heart', !isWishlisted);
                                $(this).attr('title', isWishlisted ? 'Add to Wishlist' : 'Remove from Wishlist');
                            });
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
                                $button.data('in-cart', false).removeAttr('data-in-cart');
                                $button.html('<i class="la la-shopping-cart mr-1"></i> Add to Cart');
                            } else {
                                $button.data('in-cart', true);
                                $button.html('<i class="la la-shopping-cart mr-1"></i> In Cart');
                            }
                            $('.add-to-cart[data-course-id="' + courseId + '"]').each(function() {
                                $(this).data('in-cart', !isInCart).prop('disabled', !isInCart)
                                    .html('<i class="la la-shopping-cart mr-1"></i> ' + (isInCart ? 'Add to Cart' : 'In Cart'));
                            });
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
                                    console.log('Cart dropdown HTML received');
                                    var $newCart = $(html).find('#cartDropdown').html();
                                    $('#cartDropdown').html($newCart);
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
