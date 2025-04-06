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
                    $discountPercentage = ($course->selling_price > 0 && $course->discount_price !== null) ? round(($course->discount_price / $course->selling_price) * 100) : 0;
                    $rating = $course->reviews->avg('rating') ?? 0;
                    $reviews_count = $course->reviews->count();
                @endphp
                <div class="col-lg-4 col-md-6">
                    <div class="card card-item card-preview shadow-sm hover-scale animate__animated animate__fadeInUp" data-tooltip-content="#tooltip_content_{{ $course->id }}">
                        <div class="card-image position-relative">
                            <a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}" class="d-block">
                                <img class="card-img-top lazy rounded-top" src="{{ asset('storage/upload/course_images/thumbnail/' . ($course->course_image ?? 'default-course.jpg')) }}" alt="{{ $course->course_name }}" style="height: 200px; object-fit: cover;">
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
                                    <p class="card-price text-dark fw-bold fs-16">${{ number_format($finalPrice, 2) }} <span class="before-price text-muted fs-14 text-decoration-line-through">${{ number_format($course->selling_price, 2) }}</span></p>
                                @else
                                    <p class="card-price text-dark fw-bold fs-16">${{ number_format($finalPrice, 2) }}</p>
                                @endif
                                <div class="icon-element icon-element-sm shadow-sm cursor-pointer text-primary" title="Add to Wishlist"><i class="la la-heart-o fs-18"></i></div>
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
                        <form action="{{ route('cart.add', $course->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn theme-btn btn-sm px-3"><i class="la la-shopping-cart mr-1"></i> Add to Cart</button>
                        </form>
                        <div class="icon-element icon-element-sm shadow-sm cursor-pointer text-primary" title="Add to Wishlist"><i class="la la-heart-o fs-18"></i></div>
                    </div>
                </div>
            </div><!-- end card -->
        </div>
    </div><!-- end tooltip_templates -->
@endforeach

<!-- Scripts -->
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tooltipster/4.2.8/js/tooltipster.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" integrity="sha512-c42qTSw/wPZ3/5LBzD+Bw5f7bSF2oxou6wEb+I/lqeaKV5FDIfMvvRp772y4jcJLKuGUOpbJMdg/BTl50fJYAw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script>
        $(document).ready(function() {
            // Initialiser Tooltipster
            $('.card-preview').tooltipster({
                theme: 'tooltipster-shadow',
                interactive: true,
                contentAsHTML: true,
                maxWidth: 400,
                side: 'right',
                distance: 10
            });

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
        });
    </script>
@endpush
@endsection