@extends('frontend.master')
@section('home')
@section('title')
{{ $instructor->name ?? 'Instructor Not Found' }} | Easy Learning
@endsection

<!-- ================================
    START BREADCRUMB AREA
================================= -->
<section class="breadcrumb-area py-5 bg-white pattern-bg">
    <div class="container">
        <div class="breadcrumb-content">
            <div class="media media-card align-items-center pb-4">
                <div class="media-img media--img media-img-md rounded-full">
                    <img class="rounded-full" src="{{ $instructor->photo ? asset('storage/upload/instructor_images/' . $instructor->photo) : asset('upload/no_image.jpg') }}" alt="Instructor thumbnail image">
                </div>
                <div class="media-body">
                    <h2 class="section__title fs-30">{{ $instructor->name ?? 'Unknown Instructor' }}</h2>
                    <span class="d-block lh-18 pt-1 pb-2">Joined {{ $instructor && $instructor->created_at ? \Carbon\Carbon::parse($instructor->created_at)->diffForHumans() : 'N/A' }}</span>
                    <p class="lh-18">{{ $instructor->email ?? 'No email available' }}</p>
                    @if($instructor->location)
                        <p class="lh-18"><i class="la la-map-marker"></i> {{ $instructor->location }}</p>
                    @endif
                    @if($instructor->website)
                        <p class="lh-18"><a href="{{ $instructor->website }}" target="_blank"><i class="la la-globe"></i> Visit Website</a></p>
                    @endif
                </div>
            </div><!-- end media -->
            <ul class="social-icons social-icons-styled social--icons-styled">
                @if($instructor->facebook)
                    <li><a href="{{ $instructor->facebook }}" target="_blank"><i class="la la-facebook"></i></a></li>
                @endif
                @if($instructor->twitter)
                    <li><a href="{{ $instructor->twitter }}" target="_blank"><i class="la la-twitter"></i></a></li>
                @endif
                @if($instructor->instagram)
                    <li><a href="{{ $instructor->instagram }}" target="_blank"><i class="la la-instagram"></i></a></li>
                @endif
                @if($instructor->linkedin)
                    <li><a href="{{ $instructor->linkedin }}" target="_blank"><i class="la la-linkedin"></i></a></li>
                @endif
                @if($instructor->youtube)
                    <li><a href="{{ $instructor->youtube }}" target="_blank"><i class="la la-youtube"></i></a></li>
                @endif
            </ul>
        </div><!-- end breadcrumb-content -->
    </div><!-- end container -->
</section><!-- end breadcrumb-area -->
<!-- ================================
    END BREADCRUMB AREA
================================= -->

<!-- ================================
       START TEACHER DETAILS AREA
================================= -->
<section class="teacher-details-area pt-50px">
    <div class="container">
        <div class="student-details-wrap pb-20px">
            <div class="row">
                <div class="col-lg-4 responsive-column-half">
                    <div class="counter-item">
                        <div class="counter__icon icon-element mb-3 shadow-sm">
                            <i class="la la-users"></i>
                        </div>
                        <h4 class="counter__title counter text-color-2 fs-35">{{ $totalStudents ?? '0' }}</h4>
                        <p class="counter__meta">Total Students</p>
                    </div><!-- end counter-item -->
                </div><!-- end col-lg-4 -->
                <div class="col-lg-4 responsive-column-half">
                    <div class="counter-item">
                        <div class="counter__icon icon-element mb-3 shadow-sm">
                            <i class="la la-star"></i>
                        </div>
                        <h4 class="counter__title counter text-color-3 fs-35">{{ $totalReviews ?? '0' }}</h4>
                        <p class="counter__meta">Reviews</p>
                    </div><!-- end counter-item -->
                </div><!-- end col-lg-4 -->
                <div class="col-lg-4 responsive-column-half">
                    <div class="counter-item">
                        <div class="counter__icon icon-element mb-3 shadow-sm">
                            <i class="la la-book"></i>
                        </div>
                        <h4 class="counter__title counter text-color-4 fs-35">{{ count($courses) }}</h4>
                        <p class="counter__meta">Courses</p>
                    </div><!-- end counter-item -->
                </div><!-- end col-lg-4 -->
            </div><!-- end row -->
        </div><!-- end student-details-wrap -->
    </div><!-- end container -->
    <div class="bg-gray py-5">
        <div class="container">
            <ul class="nav nav-tabs generic-tab justify-content-center" id="myTab" role="tablist">
                @auth
                <li class="nav-item">
                    <div id="app">
                        <send-message 
                            :recevierid="{{ $instructor ? $instructor->id : 0 }}" 
                            :receivername="{{ $instructor ? json_encode($instructor->name) : json_encode('Unknown Instructor') }}"
                        ></send-message>
                    </div>
                </li>
                @else
                <button class="btn theme-btn d-none d-lg-inline-block">Login First</button>
                @endauth
                <li class="nav-item">
                    <a class="nav-link active" id="about-me-tab" data-toggle="tab" href="#about-me" role="tab" aria-controls="about-me" aria-selected="true">About Me</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="experience-tab" data-toggle="tab" href="#experience" role="tab" aria-controls="experience" aria-selected="false">Experience</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="education-tab" data-toggle="tab" href="#education" role="tab" aria-controls="education" aria-selected="false">Education</a>
                </li>
            </ul>
            <div class="tab-content pt-40px" id="myTabContent">
                <div class="tab-pane fade show active" id="about-me" role="tabpanel" aria-labelledby="about-me-tab">
                    <div class="card card-item">
                        <div class="card-body">
                            <h4>About Me</h4>
                            <p class="card-text pb-3">{{ $instructor->bio ?? 'No biography available.' }}</p>
                        </div>
                    </div>
                </div><!-- end tab-pane -->
                <div class="tab-pane fade" id="experience" role="tabpanel" aria-labelledby="experience-tab">
                    <div class="card card-item">
                        <div class="card-body">
                            <h4>Experience</h4>
                            <p>{{ $instructor->experience ?? 'No experience details provided.' }}</p>
                            @if($instructor->skills)
                                <div class="skills-wrap pt-30px">
                                    <h5>Skills</h5>
                                    <div class="skills">
                                        @foreach(explode(',', $instructor->skills) as $skillEntry)
                                            @php
                                                [$skillName, $skillLevel] = explode(':', $skillEntry . ':0');
                                                $skillName = trim($skillName);
                                                $skillLevel = (int) trim($skillLevel); // Convertit en entier, 0 si invalide
                                            @endphp
                                            @if($skillName) <!-- Vérifie que le nom n'est pas vide -->
                                                <div class="skill">
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                                                        <span class="fs-16 text-black font-weight-semi-bold pr-3">{{ $skillName }}</span>
                                                        <span>{{ $skillLevel }}%</span>
                                                    </div>
                                                    <div class="progress_bg">
                                                        <div class="progress_bar" style="width: {{ $skillLevel }}%;"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div><!-- end skills -->
                                </div>
                            @endif
                        </div>
                    </div>
                </div><!-- end tab-pane -->
                <div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="education-tab">
                    <div class="card card-item">
                        <div class="card-body">
                            <h4>Education</h4>
                            <p>{{ $instructor->education ?? 'No education details provided.' }}</p>
                        </div>
                    </div>
                </div><!-- end tab-pane -->
            </div><!-- end tab-content -->
        </div><!-- end container -->
    </div>
</section><!-- end teacher-details-area -->
<!-- ================================
       END TEACHER DETAILS AREA
================================= -->

<!--======================================
        START COURSE AREA
======================================-->
<section class="course-area section-padding">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between pb-3">
            <h3 class="fs-24 font-weight-semi-bold">My Courses</h3>
            <span class="ribbon ribbon-lg">{{ count($courses) }}</span>
        </div>
        <div class="divider"><span></span></div>
        <div class="row pt-30px">
            @forelse($courses as $course)
                @php
                $finalPrice = $course->discount_price !== null
                    ? max(0, $course->selling_price - $course->discount_price)
                    : $course->selling_price;
                $discountPercentage = ($course->selling_price > 0 && $course->discount_price !== null)
                    ? round(($course->discount_price / $course->selling_price) * 100)
                    : 0;
                $rating = $course->reviews->avg('rating') ?? 0;
                $reviews_count = $course->reviews->count();
                @endphp
                <div class="col-lg-4 responsive-column-half">
                    <div class="card card-item card-preview" data-tooltip-content="#tooltip_content_{{ $course->id }}">
                        <div class="card-image">
                            <a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}" class="d-block">
                                <img class="card-img-top lazy" src="{{ asset('storage/upload/course_images/thumbnail/' . ($course->course_image ?? 'default-course.jpg')) }}" alt="Course image">
                            </a>
                            <div class="course-badge-labels">
                                @if($course->bestseller == 1)
                                    <div class="course-badge">Bestseller</div>
                                @endif
                                @if($course->discount_price == null)
                                    <div class="course-badge blue">New</div>
                                @else
                                    <div class="course-badge blue">{{ $discountPercentage }}%</div>
                                @endif
                            </div>
                        </div><!-- end card-image -->
                        <div class="card-body">
                            <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $course->label }}</h6>
                            <h5 class="card-title"><a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}">{{ $course->course_name }}</a></h5>
                            <p class="card-text"><a href="{{ route('instructor.details', $course->courseable->id) }}">{{ $course->courseable->name ?? 'Unknown Instructor' }}</a></p>
                            <div class="rating-wrap d-flex align-items-center py-2">
                                <div class="review-stars">
                                    <span class="rating-number">{{ number_format($rating, 1) }}</span>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="la la-star{{ $i <= floor($rating) ? '' : '-o' }}"></span>
                                    @endfor
                                </div>
                                <span class="rating-total pl-1">({{ number_format($reviews_count) }})</span>
                            </div><!-- end rating-wrap -->
                            <div class="d-flex justify-content-between align-items-center">
                                @if($finalPrice < $course->selling_price)
                                    <p class="card-price text-black font-weight-bold">${{ number_format($finalPrice, 2) }} <span class="before-price font-weight-medium">${{ number_format($course->selling_price, 2) }}</span></p>
                                @else
                                    <p class="card-price text-black font-weight-bold">${{ number_format($finalPrice, 2) }}</p>
                                @endif
                                <div class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Add to Wishlist"><i class="la la-heart-o"></i></div>
                            </div>
                        </div><!-- end card-body -->
                    </div><!-- end card -->
                </div><!-- end col-lg-4 -->
            @empty
                <div class="col-lg-12">
                    <p class="text-center">This instructor has no courses yet.</p>
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
    $finalPrice = $course->discount_price !== null
        ? max(0, $course->selling_price - $course->discount_price)
        : $course->selling_price;
    $rating = $course->reviews->avg('rating') ?? 0;
    $reviews_count = $course->reviews->count();
    @endphp
    <div class="tooltip_templates" style="display: none;">
        <div id="tooltip_content_{{ $course->id }}">
            <div class="card card-item">
                <div class="card-body">
                    <p class="card-text pb-2">By <a href="{{ route('instructor.details', $course->courseable->id) }}">{{ $course->courseable->name ?? 'Unknown Instructor' }}</a></p>
                    <h5 class="card-title pb-1"><a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}">{{ $course->course_name }}</a></h5>
                    <div class="d-flex align-items-center pb-1">
                        @if($course->bestseller == 1)
                            <h6 class="ribbon fs-14 mr-2">Bestseller</h6>
                        @endif
                        <p class="text-success fs-14 font-weight-medium">Updated <span class="font-weight-bold pl-1">{{ $course->updated_at ? \Carbon\Carbon::parse($course->updated_at)->format('F Y') : 'N/A' }}</span></p>
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
                        <li>{{ $course->duration ?? 'N/A' }}</li>
                        <li>{{ $course->label ?? 'All Levels' }}</li>
                    </ul>
                    <p class="card-text pt-1 fs-14 lh-22">{{ $course->description ?? 'No description available.' }}</p>
                   @php
                        $goals = App\Models\CourseGoal::where('goalable_type', 'App\\Models\\Course')
                            ->where('goalable_id', $course->id)
                            ->orderBy('id', 'DESC')
                            ->get();
                    @endphp
                    <ul class="generic-list-item fs-14 py-3">
                        @foreach($goals as $goal)
                            <li><i class="la la-check mr-1 text-black"></i> {{ $goal->goal_name }}</li>
                        @endforeach
                        @if($goals->isEmpty())
                            <li><i class="la la-check mr-1 text-black"></i> Learn key skills for this course</li>
                            <li><i class="la la-check mr-1 text-black"></i> Boost your knowledge</li>
                            <li><i class="la la-check mr-1 text-black"></i> Practical exercises included</li>
                        @endif
                    </ul>
                    <div class="d-flex justify-content-between align-items-center">
                        <form action="{{ route('cart.add', $course->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn theme-btn flex-grow-1 mr-3">
                                <i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart
                            </button>
                        </form>
                        <div class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Add to Wishlist"><i class="la la-heart-o"></i></div>
                    </div>
                </div>
            </div><!-- end card -->
        </div>
    </div><!-- end tooltip_templates -->
@endforeach

<!-- Script pour initialiser Tooltipster avec position à droite -->
<script>
$(document).ready(function() {
    $('.card-preview').tooltipster({
        theme: 'tooltipster-shadow',
        interactive: true,
        contentAsHTML: true,
        maxWidth: 400,
        side: 'right', // Positionne le tooltip à droite du cours
        distance: 10   // Distance entre la carte et le tooltip
    });
});
</script>

@endsection