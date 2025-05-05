@extends('frontend.master')

@section('title')
    {{ $course->course_name ?? 'Course Details' }} | Easy Learning
@endsection

@section('home')
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
.preview-course-video {
    position: relative;
    overflow: hidden;
}
.preview-course-video-content {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transition: all 0.3s ease;
}
.preview-course-video-content:hover {
    background-color: rgba(0, 0, 0, 0.3);
}
.preview-course-video-content .overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}
.preview-course-video-content .play-button {
    position: relative;
    z-index: 1;
    width: 70px;
    height: 70px;
    transform: scale(1);
    transition: transform 0.3s ease;
}
.preview-course-video-content:hover .play-button {
    transform: scale(1.1);
}
.preview-course-video-content .play-button svg {
    width: 100%;
    height: 100%;
    fill: #fff;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
}
.preview-course-video-content p {
    position: relative;
    z-index: 1;
    margin-top: 15px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}
</style>

<!-- BREADCRUMB AREA -->
<section class="breadcrumb-area pt-50px pb-50px bg-white pattern-bg">
    <div class="container">
        <div class="col-lg-8 mr-auto">
            <div class="breadcrumb-content">
                <ul class="generic-list-item generic-list-item-arrow d-flex flex-wrap align-items-center">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><a href="{{ $course->subcategory && $course->subcategory->category ? url('category/'.$course->subcategory->category->id.'/'.$course->subcategory->category->category_slug) : '#' }}">{{ $course->subcategory && $course->subcategory->category ? $course->subcategory->category->category_name : 'Uncategorized' }}</a></li>
                    <li><a href="{{ $course->subcategory ? url('subcategory/'.$course->subcategory->id.'/'.$course->subcategory->subcategory_slug) : '#' }}">{{ $course->subcategory ? $course->subcategory->subcategory_name : 'No Subcategory' }}</a></li>
                </ul>
                <div class="section-heading">
                    <h2 class="section__title">{{ $course->course_name ?? 'Untitled Course' }}</h2>
                    <p class="section__desc pt-2 lh-30">{{ $course->course_title ?? 'No title available' }}</p>
                </div>
                <div class="d-flex flex-wrap align-items-center pt-3">
                    @if ($course->bestseller == 1)
                        <h6 class="ribbon ribbon-lg mr-2 bg-3 text-white">Bestseller</h6>
                    @endif
                    @if ($course->featured == 1)
                        <h6 class="ribbon ribbon-lg mr-2 bg-3 text-white">Featured</h6>
                    @endif
                    @if ($course->highestrated == 1)
                        <h6 class="ribbon ribbon-lg mr-2 bg-3 text-white">Highest Rated</h6>
                    @endif

                    @php
                        $reviewcount = $course->reviews()->where('status', 1)->latest()->get();
                        $average = $course->reviews()->where('status', 1)->avg('rating') ?? 0;
                        $isWishlisted = auth()->check() && \App\Models\Wishlist::where('trackable_type', 'App\Models\User')
                            ->where('trackable_id', auth()->id())
                            ->where('course_id', $course->id)
                            ->exists();
                    @endphp

                    <div class="rating-wrap d-flex flex-wrap align-items-center">
                        <div class="review-stars">
                            <span class="rating-number">{{ round($average, 1) }}</span>
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="la la-star{{ $i <= floor($average) ? '' : '-o' }}"></span>
                            @endfor
                        </div>
                        <span class="rating-total pl-1">({{ count($reviewcount) }} ratings)</span>
                    </div>
                </div>
                <p class="pt-2 pb-1">Created by 
                    @if ($instructor)
                        <a href="{{ route('instructor.details', $instructor->id) }}" class="text-color hover-underline">{{ $instructor->name }}</a>
                    @else
                        Unknown Instructor
                    @endif
                </p>
                <div class="d-flex flex-wrap align-items-center">
                    <p class="pr-3 d-flex align-items-center">
                        <svg class="svg-icon-color-gray mr-1" width="16px" viewBox="0 0 24 24">
                            <path d="M23 12l-2.44-2.78.34-3.68-3.61-.82-1.89-3.18L12 3 8.6 1.54 6.71 4.72l-3.61.81.34 3.68L1 12l2.44 2.78-.34 3.69 3.61.82 1.89 3.18L12 21l3.4 1.46 1.89-3.18 3.61-.82-.34-3.68L23 12zm-10 5h-2v-2h2v2zm0-4h-2V7h2v6z"></path>
                        </svg>
                        Last updated {{ $course->updated_at ? \Carbon\Carbon::parse($course->updated_at)->format('M d Y') : 'N/A' }}
                    </p>
                </div>
                <div class="bread-btn-box pt-3">
                    @auth
                        <button class="wishlist-btn btn theme-btn theme-btn-sm theme-btn-transparent lh-28 mr-2 mb-2 {{ $isWishlisted ? 'wishlisted' : '' }}"
                                data-course-id="{{ $course->id }}"
                                title="{{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                            <i class="la {{ $isWishlisted ? 'la-heart' : 'la-heart-o' }} mr-1"></i>
                            <span class="swapping-btn" data-text-swap="Wishlisted" data-text-original="Wishlist">{{ $isWishlisted ? 'Wishlisted' : 'Wishlist' }}</span>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn theme-btn theme-btn-sm theme-btn-transparent lh-28 mr-2 mb-2" title="Login to add to Wishlist">
                            <i class="la la-heart-o mr-1"></i>
                            <span class="swapping-btn" data-text-swap="Wishlisted" data-text-original="Wishlist">Wishlist</span>
                        </a>
                    @endauth
                    <button class="btn theme-btn theme-btn-sm theme-btn-transparent lh-28 mr-2 mb-2" data-toggle="modal" data-target="#shareModal">
                        <i class="la la-share mr-1"></i>Share
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- COURSE DETAILS AREA -->
    <section class="course-details-area pb-20px">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 pb-5">
                    <div class="course-details-content-wrap pt-90px">
                        <div class="course-overview-card bg-gray p-4 rounded">
                            <h3 class="fs-24 font-weight-semi-bold pb-3">What you'll learn</h3>
                            <ul class="generic-list-item overview-list-item">
                                @forelse ($goals as $goal)
                                    <li><i class="la la-check mr-1 text-black"></i> {{ trim($goal->goal_name ?? $goal) }}</li>
                                @empty
                                    <li><i class="la la-check mr-1 text-black"></i> No goals specified for this course.</li>
                                @endforelse
                            </ul>
                        </div>

                        <div class="course-overview-card">
                            <h3 class="fs-24 font-weight-semi-bold pb-3">Requirements</h3>
                            <ul class="generic-list-item generic-list-item-bullet fs-15">
                                <li>{{ $course->prerequisites ?? 'No specific requirements.' }}</li>
                            </ul>
                        </div>

                        <div class="course-overview-card">
                            <h3 class="fs-24 font-weight-semi-bold pb-3">Description</h3>
                            <p class="fs-15 pb-2">{!! $course->description ?? 'No description available.' !!}</p>
                            <div class="collapse" id="collapseMore">
                                <h4 class="fs-20 font-weight-semi-bold py-2">Who this course is for:</h4>
                                <p class="fs-15 pb-2">{{ $course->prerequisites ?? 'Anyone interested in learning.' }}</p>
                            </div>
                            <a class="collapse-btn collapse--btn fs-15" data-toggle="collapse" href="#collapseMore" role="button" aria-expanded="false" aria-controls="collapseMore">
                                <span class="collapse-btn-hide">Show more<i class="la la-angle-down ml-1 fs-14"></i></span>
                                <span class="collapse-btn-show">Show less<i class="la la-angle-up ml-1 fs-14"></i></span>
                            </a>
                        </div>

                        <div class="course-overview-card">
                            <div class="curriculum-header d-flex align-items-center justify-content-between pb-4">
                                <h3 class="fs-24 font-weight-semi-bold">Course content</h3>
                                <div class="curriculum-duration fs-15">
                                    <span class="curriculum-total__text mr-2"><strong class="text-black font-weight-semi-bold">Total:</strong> {{ $course->lectures->count() }} lectures</span>
                                    <span class="curriculum-total__hours"><strong class="text-black font-weight-semi-bold">Total hours:</strong> {{ $course->duration ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <div class="curriculum-content">
                                <div id="accordion" class="generic-accordion">
                                    @foreach ($course->sections ?? [] as $sec)
                                        <div class="card">
                                            <div class="card-header" id="heading{{ $sec->id }}">
                                                <button class="btn btn-link d-flex align-items-center justify-content-between w-100 section-toggle">
                                                    <span class="d-flex align-items-center">
                                                        <i class="la la-plus mr-2"></i>
                                                        <i class="la la-minus mr-2" style="display: none;"></i>
                                                        {{ $sec->section_title ?? 'Untitled Section' }}
                                                    </span>
                                                    <span class="fs-15 text-gray font-weight-medium">
                                                        {{ $sec->lectures->count() }} {{ $sec->lectures->count() === 1 ? 'lecture' : 'lectures' }}
                                                    </span>
                                                </button>
                                            </div>
                                            <div id="collapse{{ $sec->id }}" class="section-content" style="display: none;">
                                                <div class="card-body">
                                                    <ul class="generic-list-item">
                                                        @foreach ($sec->lectures ?? [] as $lect)
                                                            <li class="curriculum-content">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <span>
                                                                        <i class="la la-play-circle mr-2"></i>
                                                                        {{ $lect->lecture_title ?? 'Untitled Lecture' }}
                                                                    </span>
                                                                    <span class="text-muted">{{ $lect->duration ?? 'N/A' }}</span>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="course-overview-card pt-4">
                            <h3 class="fs-24 font-weight-semi-bold pb-4">About the instructor</h3>
                            <div class="instructor-wrap">
                                <div class="media media-card">
                                    <div class="instructor-img">
                                        <a href="{{ $instructor ? route('instructor.details', $instructor->id) : '#' }}" class="media-img d-block">
                                            <img class="lazy" src="{{ $instructor && $instructor->photo ? asset('storage/upload/instructor_images/' . $instructor->photo) : asset('images/no_image.jpg') }}" alt="Instructor image" loading="lazy" onerror="this.src='{{ asset('images/no_image.jpg') }}'">
                                        </a>
                                        <ul class="generic-list-item pt-3">
                                            <li><i class="la la-play-circle-o mr-2 text-color-3"></i> {{ $instructorCourses->count() ?? 0 }} Courses</li>
                                            <li><a href="{{ $instructor ? route('instructor.details', $instructor->id) : '#' }}">View all Courses</a></li>
                                        </ul>
                                    </div>
                                    <div class="media-body">
                                        <h5><a href="{{ $instructor ? route('instructor.details', $instructor->id) : '#' }}">{{ $instructor->name ?? 'Unknown Instructor' }}</a></h5>
                                        <span class="d-block lh-18 pt-2 pb-3">Joined {{ $instructor && $instructor->created_at ? \Carbon\Carbon::parse($instructor->created_at)->diffForHumans() : 'N/A' }}</span>
                                        <p class="text-black lh-18 pb-3">{{ $instructor->email ?? 'No email available' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="course-overview-card pt-4">
                            <h3 class="fs-24 font-weight-semi-bold pb-40px">Student feedback</h3>
                            <div class="feedback-wrap">
                                <div class="media media-card align-items-center">
                                    <div class="review-rating-summary">
                                        <span class="stats-average__count">{{ round($average, 1) }}</span>
                                        <div class="rating-wrap pt-1">
                                            <div class="review-stars">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span class="la la-star{{ $i <= floor($average) ? '' : '-o' }}"></span>
                                                @endfor
                                            </div>
                                            <span class="rating-total d-block">({{ count($reviewcount) }})</span>
                                            <span>Course Rating</span>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        @php
                                            use Illuminate\Support\Facades\DB;
                                            $reviewcount = $course->reviews()
                                                ->where('status', 1)
                                                ->select('rating', DB::raw('count(*) as count'))
                                                ->groupBy('rating')
                                                ->orderBy('rating', 'desc')
                                                ->get();

                                            $totalReviews = $reviewcount->sum('count');
                                            $percentages = [];
                                            for ($i = 5; $i >= 1; $i--) {
                                                $ratingCount = $reviewcount->where('rating', $i)->first();
                                                $count = $ratingCount ? $ratingCount->count : 0;
                                                $percent = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                                                $percentages[] = [
                                                    'rating' => $i,
                                                    'percent' => $percent,
                                                    'count' => $count,
                                                ];
                                            }
                                        @endphp

                                        @if (count($percentages) > 0)
                                            @foreach ($percentages as $ratingInfo)
                                                <div class="review-bars d-flex align-items-center mb-2">
                                                    <div class="review-bars__text">{{ $ratingInfo['rating'] }} stars</div>
                                                    <div class="review-bars__fill">
                                                        <div class="skillbar-box">
                                                            <div class="skillbar" data-percent="{{ $ratingInfo['percent'] }}%">
                                                                <div class="skillbar-bar bg-3" style="width: {{ $ratingInfo['percent'] }}%;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="review-bars__percent">{{ number_format($ratingInfo['percent'], 2) }}%</div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No Reviews Available</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="course-overview-card pt-4">
                            <h3 class="fs-24 font-weight-semi-bold pb-4">Reviews</h3>
                            <div class="review-wrap">
                                @php
                                    $reviews = $course->reviews()
                                        ->where('status', 1)
                                        ->latest()
                                        ->limit(5)
                                        ->get();
                                @endphp
                                @forelse ($reviews as $item)
                                    <div class="media media-card border-bottom border-bottom-gray pb-4 mb-4">
                                        <div class="media-img mr-4 rounded-full">
                                            <img class="rounded-full lazy" src="{{ $item->user->photo ? asset('storage/upload/user_images/' . $item->user->photo) : asset('images/no_image.jpg') }}" alt="User image" loading="lazy" onerror="this.src='{{ asset('images/no_image.jpg') }}'">
                                        </div>
                                        <div class="media-body">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between pb-1">
                                                <h5>{{ $item->user->name ?? 'Anonymous' }}</h5>
                                                <div class="review-stars">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <span class="la la-star{{ $i <= $item->rating ? '' : '-o' }}"></span>
                                                    @endfor
                                                </div>
                                            </div>
                                            <span class="d-block lh-18 pb-2">{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->diffForHumans() : 'N/A' }}</span>
                                            <p class="pb-2">{{ $item->comment ?? 'No comment provided' }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p>No reviews available yet.</p>
                                @endforelse
                            </div>
                        </div>

                        @guest
                            <p><b>For Add Course Review. You need to login first <a href="{{ route('login') }}">Login Here</a></b></p>
                        @else
                            <div class="course-overview-card pt-4">
                                <h3 class="fs-24 font-weight-semi-bold pb-4">Add a Review</h3>
                                <form method="post" action="{{ route('store.review') }}" class="row">
                                    @csrf
                                    <div class="leave-rating-wrap pb-4">
                                        <div class="leave-rating leave--rating">
                                            <input type="radio" name="rate" id="star5" value="5" required />
                                            <label for="star5"></label>
                                            <input type="radio" name="rate" id="star4" value="4" />
                                            <label for="star4"></label>
                                            <input type="radio" name="rate" id="star3" value="3" />
                                            <label for="star3"></label>
                                            <input type="radio" name="rate" id="star2" value="2" />
                                            <label for="star2"></label>
                                            <input type="radio" name="rate" id="star1" value="1" />
                                            <label for="star1"></label>
                                        </div>
                                    </div>
                                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                                    <input type="hidden" name="instructor_id" value="{{ $instructor ? $instructor->id : null }}">
                                    <div class="input-box col-lg-12">
                                        <label class="label-text">Message</label>
                                        <div class="form-group">
                                            <textarea class="form-control form--control pl-3" name="comment" placeholder="Write Message" rows="5" required></textarea>
                                        </div>
                                    </div>
                                    <div class="btn-box col-lg-12">
                                        <button class="btn theme-btn" type="submit">Submit Review</button>
                                    </div>
                                </form>
                            </div>
                        @endguest
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sidebar sidebar-negative">
                        <div class="card card-item">
                            <div class="card-body">
                                <div class="preview-course-video">
                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#previewModal">
                                        <img src="{{ $course->course_image ? asset('storage/upload/course_images/thumbnail/' . $course->course_image) : asset('images/no_image.jpg') }}" alt="course-img" class="w-100 rounded lazy" loading="lazy" onerror="this.src='{{ asset('images/no_image.jpg') }}'">
                                        <div class="preview-course-video-content">
                                            <div class="overlay"></div>
                                            <div class="play-button">
                                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="-307.4 338.8 91.8 91.8" xml:space="preserve">
                                                    <g>
                                                        <circle class="st0" cx="-261.5" cy="384.7" r="45.9" fill="rgba(0,0,0,0.7)"></circle>
                                                        <path class="st1" d="M-272.9,363.2l35.8,20.7c0.7,0.4,0.7,1.3,0,1.7l-35.8,20.7c-0.7,0.4-1.5-0.1-1.5-0.9V364C-274.4,363.3-273.5,362.8-272.9,363.2z" fill="#fff"></path>
                                                    </g>
                                                </svg>
                                            </div>
                                            <p class="fs-15 font-weight-bold text-white pt-3">Preview this course</p>
                                        </div>
                                    </a>
                                </div>

                                @php
                                    $finalPrice = $course->discount_price !== null
                                        ? max(0, $course->selling_price - $course->discount_price)
                                        : $course->selling_price;
                                    $discountPercentage = ($course->selling_price > 0 && $course->discount_price !== null)
                                        ? round(($course->discount_price / $course->selling_price) * 100)
                                        : 0;
                                    $isInCart = \Darryldecode\Cart\Facades\CartFacade::get($course->id) !== null;
                                @endphp

                                <div class="preview-course-feature-content pt-40px">
                                    <p class="d-flex align-items-center pb-2">
                                        @if ($finalPrice < $course->selling_price)
                                            <span class="fs-35 font-weight-semi-bold text-black">{{ number_format($finalPrice, 2) }} TND</span>
                                            <span class="before-price mx-1">{{ number_format($course->selling_price, 2) }} TND</span>
                                        @else
                                            <span class="fs-35 font-weight-semi-bold text-black">{{ number_format($finalPrice, 2) }} TND</span>
                                        @endif
                                        @if ($discountPercentage > 0)
                                            <span class="price-discount">{{ $discountPercentage }}% off</span>
                                        @endif
                                    </p>
                                    <p class="preview-price-discount-text pb-35px">
                                        <span class="text-color-3">4 days</span> left at this price!
                                    </p>
                                    <div class="buy-course-btn-box">
                                        @if ($hasPurchased)
                                            <a href="{{ route('course.start', [$course->id, \Str::slug($course->course_name)]) }}" class="btn theme-btn flex-grow-1 mr-3">
                                                <i class="la la-play-circle fs-18 mr-1"></i> Start Learning
                                            </a>
                                        @else
                                            <button class="btn theme-btn flex-grow-1 mr-3 add-to-cart" data-course-id="{{ $course->id }}" {{ $isInCart ? 'data-in-cart="true"' : '' }}>
                                                <i class="la la-shopping-cart fs-18 mr-1"></i> {{ $isInCart ? 'In Cart' : 'Add to Cart' }}
                                            </button>
                                        @endif
                                    </div>
                                    <div class="preview-course-incentives">
                                        <h3 class="card-title fs-18 pb-2">This course includes</h3>
                                        <ul class="generic-list-item pb-3">
                                            <li><i class="la la-play-circle-o mr-2 text-color"></i>{{ $course->duration ?? 'N/A' }} learning hours</li>
                                            <li><i class="la la-file mr-2 text-color"></i>{{ $course->resources ?? 'N/A' }} resources</li>
                                            <li><i class="la la-file mr-2 text-color"></i>Certificate: {{ $course->certificate ?? 'N/A' }}</li>
                                            <li><i class="la la-key mr-2 text-color"></i>Full lifetime access</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-item">
                            <div class="card-body">
                                <h3 class="card-title fs-18 pb-2">Course Categories</h3>
                                <div class="divider"><span></span></div>
                                <ul class="generic-list-item">
                                    @forelse ($categories ?? [] as $cat)
                                        <li><a href="{{ url('category/'.$cat->id.'/'.$cat->category_slug) }}">{{ $cat->category_name }}</a></li>
                                    @empty
                                        <li>No categories available</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- RELATED COURSE AREA -->
    <section class="related-course-area bg-gray pt-60px pb-60px">
        <div class="container">
            <div class="related-course-wrap">
                <h3 class="fs-28 font-weight-semi-bold pb-35px">More Courses by 
                    @if ($instructor)
                        <a href="{{ route('instructor.details', $instructor->id) }}" class="text-color hover-underline">{{ $instructor->name }}</a>
                    @else
                        Unknown Instructor
                    @endif
                </h3>
                <div class="view-more-carousel-2 owl-action-styled">
                    @foreach ($instructorCourses ?? [] as $inscourse)
                        @php
                            $insFinalPrice = $inscourse->discount_price !== null
                                ? max(0, $inscourse->selling_price - $inscourse->discount_price)
                                : $inscourse->selling_price;
                            $insDiscountPercentage = ($inscourse->selling_price > 0 && $inscourse->discount_price !== null)
                                ? round(($inscourse->discount_price / $inscourse->selling_price) * 100)
                                : 0;
                            $insRating = $inscourse->reviews->avg('rating') ?? 0;
                            $insReviewsCount = $inscourse->reviews->count();
                            $insIsWishlisted = auth()->check() && \App\Models\Wishlist::where('trackable_type', 'App\Models\User')
                                ->where('trackable_id', auth()->id())
                                ->where('course_id', $inscourse->id)
                                ->exists();
                            $insIsInCart = \Darryldecode\Cart\Facades\CartFacade::get($inscourse->id) !== null;
                        @endphp
                        <div class="card card-item card-preview" data-tooltip-content="#tooltip_content_{{ $inscourse->id }}">
                            <div class="card-image">
                                <a href="{{ url('course/details/'.$inscourse->id.'/'.$inscourse->course_name_slug) }}" class="d-block">
                                    <img class="card-img-top lazy" src="{{ $inscourse->course_image ? asset('storage/upload/course_images/thumbnail/' . $inscourse->course_image) : asset('images/no_image.jpg') }}" alt="Card image cap" loading="lazy" onerror="this.src='{{ asset('images/no_image.jpg') }}'">
                                </a>
                                <div class="course-badge-labels">
                                    @if ($inscourse->bestseller == 1)
                                        <div class="course-badge">Bestseller</div>
                                    @endif
                                    @if ($insDiscountPercentage > 0)
                                        <div class="course-badge blue">{{ $insDiscountPercentage }}%</div>
                                    @else
                                        <div class="course-badge blue">New</div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $inscourse->label ?? 'No Label' }}</h6>
                                <h5 class="card-title"><a href="{{ url('course/details/'.$inscourse->id.'/'.$inscourse->course_name_slug) }}">{{ $inscourse->course_name }}</a></h5>
                                <p class="card-text"><a href="{{ $instructor ? route('instructor.details', $instructor->id) : '#' }}">{{ $instructor->name ?? 'Unknown Instructor' }}</a></p>
                                <div class="rating-wrap d-flex align-items-center py-2">
                                    <div class="review-stars">
                                        <span class="rating-number">{{ number_format($insRating, 1) }}</span>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="la la-star{{ $i <= floor($insRating) ? '' : '-o' }}"></span>
                                        @endfor
                                    </div>
                                    <span class="rating-total pl-1">({{ number_format($insReviewsCount) }})</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    @if ($insFinalPrice < $inscourse->selling_price)
                                        <p class="card-price text-black font-weight-bold">{{ number_format($insFinalPrice, 2) }} TND <span class="before-price font-weight-medium">{{ number_format($inscourse->selling_price, 2) }} TND</span></p>
                                    @else
                                        <p class="card-price text-black font-weight-bold">{{ number_format($insFinalPrice, 2) }} TND</p>
                                    @endif
                                    @auth
                                        <button class="wishlist-btn icon-element icon-element-sm shadow-sm cursor-pointer border-0 bg-transparent {{ $insIsWishlisted ? 'wishlisted' : '' }}"
                                                data-course-id="{{ $inscourse->id }}"
                                                title="{{ $insIsWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                                            <i class="la {{ $insIsWishlisted ? 'la-heart' : 'la-heart-o' }}"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}" class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Login to add to Wishlist">
                                            <i class="la la-heart-o"></i>
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Tooltip Templates -->
    @foreach ($instructorCourses ?? [] as $inscourse)
        @php
            $insFinalPrice = $inscourse->discount_price !== null
                ? max(0, $inscourse->selling_price - $inscourse->discount_price)
                : $inscourse->selling_price;
            $insRating = $inscourse->reviews->avg('rating') ?? 0;
            $insReviewsCount = $inscourse->reviews->count();
            $goals = App\Models\CourseGoal::where('goalable_type', 'App\Models\Course')->where('goalable_id', $inscourse->id)->orderBy('id', 'DESC')->get();
            $insHasPurchased = Auth::check() && App\Models\Order::where('user_id', Auth::id())
                ->where('course_id', $inscourse->id)
                ->where('payment_status', 'paid')
                ->exists();
            $insIsWishlisted = auth()->check() && \App\Models\Wishlist::where('trackable_type', 'App\Models\User')
                ->where('trackable_id', auth()->id())
                ->where('course_id', $inscourse->id)
                ->exists();
            $insIsInCart = \Darryldecode\Cart\Facades\CartFacade::get($inscourse->id) !== null;
        @endphp
        <div class="tooltip_templates" style="display: none;">
            <div id="tooltip_content_{{ $inscourse->id }}">
                <div class="card card-item">
                    <div class="card-body">
                        <p class="card-text pb-2">By <a href="{{ $instructor ? route('instructor.details', $instructor->id) : '#' }}">{{ $instructor->name ?? 'Unknown Instructor' }}</a></p>
                        <h5 class="card-title pb-1"><a href="{{ url('course/details/'.$inscourse->id.'/'.$inscourse->course_name_slug) }}">{{ $inscourse->course_name }}</a></h5>
                        <div class="d-flex align-items-center pb-1">
                            @if ($inscourse->bestseller == 1)
                                <h6 class="ribbon fs-14 mr-2">Bestseller</h6>
                            @endif
                            <p class="text-success fs-14 font-weight-medium">Updated <span class="font-weight-bold pl-1">{{ $inscourse->updated_at ? \Carbon\Carbon::parse($inscourse->updated_at)->format('F Y') : 'N/A' }}</span></p>
                        </div>
                        <div class="rating-wrap d-flex align-items-center py-2">
                            <div class="review-stars">
                                <span class="rating-number">{{ number_format($insRating, 1) }}</span>
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="la la-star{{ $i <= floor($insRating) ? '' : '-o' }}"></span>
                                @endfor
                            </div>
                            <span class="rating-total pl-1">({{ number_format($insReviewsCount) }})</span>
                        </div>
                        <ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet d-flex align-items-center fs-14">
                            <li>{{ $inscourse->duration ?? 'N/A' }}</li>
                            <li>{{ $inscourse->label ?? 'All Levels' }}</li>
                        </ul>
                        <p class="card-text pt-1 fs-14 lh-22">{!! $inscourse->description ?? 'No description available.' !!}</p>
                        <ul class="generic-list-item fs-14 py-3">
                            @foreach ($goals->take(3) as $goal)
                                <li><i class="la la-check mr-1 text-black"></i> {{ $goal->goal_name }}</li>
                            @endforeach
                            @if ($goals->isEmpty())
                                <li><i class="la la-check mr-1 text-black"></i> Learn key skills for this course</li>
                                <li><i class="la la-check mr-1 text-black"></i> Boost your knowledge</li>
                                <li><i class="la la-check mr-1 text-black"></i> Practical exercises included</li>
                            @endif
                        </ul>
                        <div class="d-flex justify-content-between align-items-center">
                            @if ($insHasPurchased)
                                <a href="{{ route('course.start', [$inscourse->id, $inscourse->course_name_slug]) }}" class="btn theme-btn flex-grow-1 mr-3">
                                    <i class="la la-play-circle mr-1 fs-18"></i> Start Learning
                                </a>
                            @else
                                <button class="btn theme-btn flex-grow-1 mr-3 add-to-cart" data-course-id="{{ $inscourse->id }}" {{ $insIsInCart ? 'data-in-cart="true"' : '' }}>
                                    <i class="la la-shopping-cart mr-1 fs-18"></i> {{ $insIsInCart ? 'In Cart' : 'Add to Cart' }}
                                </button>
                            @endif
                            @auth
                                <button class="wishlist-btn icon-element icon-element-sm shadow-sm cursor-pointer border-0 bg-transparent {{ $insIsWishlisted ? 'wishlisted' : '' }}"
                                        data-course-id="{{ $inscourse->id }}"
                                        title="{{ $insIsWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                                    <i class="la {{ $insIsWishlisted ? 'la-heart' : 'la-heart-o' }}"></i>
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Login to add to Wishlist">
                                    <i class="la la-heart-o"></i>
                                </a>
                            @endauth
                        </div>
                        <div id="cart-message-{{ $inscourse->id }}" class="cart-message"></div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modals -->
    <div class="modal fade modal-container" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-gray">
                    <h5 class="modal-title fs-19 font-weight-semi-bold" id="shareModalTitle">Share this course</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="la la-times"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="copy-to-clipboard">
                        <span class="success-message">Copied!</span>
                        <div class="input-group">
                            <input type="text" class="form-control form--control copy-input pl-3" value="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}">
                            <div class="input-group-append">
                                <button class="btn theme-btn theme-btn-sm copy-btn shadow-none"><i class="la la-copy mr-1"></i> Copy</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-top-gray">
                    <ul class="social-icons social-icons-styled">
                        <li><a href="#" class="facebook-bg"><i class="la la-facebook"></i></a></li>
                        <li><a href="#" class="twitter-bg"><i class="la la-twitter"></i></a></li>
                        <li><a href="#" class="instagram-bg"><i class="la la-instagram"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-container" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-gray">
                    <div class="pr-2">
                        <p class="pb-2 font-weight-semi-bold">Course Preview</p>
                        <h5 class="modal-title fs-19 font-weight-semi-bold lh-24" id="previewModalTitle">{{ $course->course_name }}</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="la la-times"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <video controls crossorigin playsinline poster="{{ $course->course_image ? asset('storage/upload/course_images/thumbnail/' . $course->course_image) : asset('images/no_image.jpg') }}" id="player">
                        <source src="{{ $course->video ? asset('storage/upload/course_images/video/' . $course->video) : '' }}" type="video/mp4" />
                        <p>Your browser doesn't support HTML5 video. Here is a <a href="{{ $course->video ? asset('storage/upload/course_images/video/' . $course->video) : '#' }}">link to the video</a> instead.</p>
                    </video>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/tooltipster.bundle.min.js') }}"></script>
    <script>
    $(document).ready(function() {
        console.log('jQuery loaded and document ready');

        // Section toggle for accordion
        $('.section-toggle').off('click').on('click', function() {
            const content = $(this).closest('.card').find('.section-content');
            const plusIcon = $(this).find('.la-plus');
            const minusIcon = $(this).find('.la-minus');
            if (content.is(':visible')) {
                content.hide();
                plusIcon.show();
                minusIcon.hide();
            } else {
                content.show();
                plusIcon.hide();
                minusIcon.show();
            }
        });

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
                            $button.find('.swapping-btn').text($button.find('.swapping-btn').data('text-original'));
                        } else {
                            $button.addClass('wishlisted');
                            $button.find('i').removeClass('la-heart-o').addClass('la-heart');
                            $button.attr('title', 'Remove from Wishlist');
                            $button.find('.swapping-btn').text($button.find('.swapping-btn').data('text-swap'));
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
                    $button.prop('disabled', true).html('<i class="la la-shopping-cart fs-18 mr-1"></i> ' + (isInCart ? 'Removing...' : 'Adding...'));
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
                            $button.html('<i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart');
                        } else {
                            // Add to cart
                            $button.data('in-cart', true);
                            $button.html('<i class="la la-shopping-cart fs-18 mr-1"></i> In Cart');
                        }
                        // Update all buttons for this course
                        $('.add-to-cart[data-course-id="' + courseId + '"]').each(function() {
                            $(this).data('in-cart', !isInCart).prop('disabled', !isInCart)
                                .html('<i class="la la-shopping-cart fs-18 mr-1"></i> ' + (isInCart ? 'Add to Cart' : 'In Cart'));
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
                    $button.prop('disabled', false).html('<i class="la la-shopping-cart fs-18 mr-1"></i> ' + (isInCart ? 'In Cart' : 'Add to Cart'));
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
                                    .html('<i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart');
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
@endsection