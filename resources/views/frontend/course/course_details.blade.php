@extends('frontend.master')
@section('home')

@section('title')
{{ $course->course_name }} | Easy Learning
@endsection

<!-- ================================
    START BREADCRUMB AREA
================================= -->
<section class="breadcrumb-area pt-50px pb-50px bg-white pattern-bg">
  <div class="container">
    <div class="col-lg-8 mr-auto">
      <div class="breadcrumb-content">
        <ul class="generic-list-item generic-list-item-arrow d-flex flex-wrap align-items-center">
          <li><a href="{{ url('/') }}">Home</a></li>
          <li><a href="{{ $course->category ? url('category/'.$course->category->id.'/'.$course->category->category_slug) : '#' }}">{{ $course->category->category_name ?? 'Uncategorized' }}</a></li>
          <li><a href="{{ $course->subcategory ? url('subcategory/'.$course->subcategory->id.'/'.$course->subcategory->subcategory_slug) : '#' }}">{{ $course->subcategory->subcategory_name ?? 'No Subcategory' }}</a></li>
        </ul>
        <div class="section-heading">
          <h2 class="section__title">{{ $course->course_name }}</h2>
          <p class="section__desc pt-2 lh-30">{{ $course->course_title }}</p>
        </div><!-- end section-heading -->
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
          $reviewcount = App\Models\Review::where('course_id', $course->id)->where('status', 1)->latest()->get();
          $average = App\Models\Review::where('course_id', $course->id)->where('status', 1)->avg('rating');
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
        </div><!-- end d-flex -->
        <p class="pt-2 pb-1">Created by <a href="{{ route('instructor.details', $course->instructor_id) }}" class="text-color hover-underline">{{ $course->instructor->name ?? 'Unknown Instructor' }}</a></p>
        <div class="d-flex flex-wrap align-items-center">
          <p class="pr-3 d-flex align-items-center">
            <svg class="svg-icon-color-gray mr-1" width="16px" viewBox="0 0 24 24">
              <path d="M23 12l-2.44-2.78.34-3.68-3.61-.82-1.89-3.18L12 3 8.6 1.54 6.71 4.72l-3.61.81.34 3.68L1 12l2.44 2.78-.34 3.69 3.61.82 1.89 3.18L12 21l3.4 1.46 1.89-3.18 3.61-.82-.34-3.68L23 12zm-10 5h-2v-2h2v2zm0-4h-2V7h2v6z"></path>
            </svg>
            Last updated {{ $course->updated_at ? \Carbon\Carbon::parse($course->updated_at)->format('M d Y') : 'N/A' }}
          </p>
        </div><!-- end d-flex -->
        <div class="bread-btn-box pt-3">
          <button class="btn theme-btn theme-btn-sm theme-btn-transparent lh-28 mr-2 mb-2">
            <i class="la la-heart-o mr-1"></i>
            <span class="swapping-btn" data-text-swap="Wishlisted" data-text-original="Wishlist">Wishlist</span>
          </button>
          <button class="btn theme-btn theme-btn-sm theme-btn-transparent lh-28 mr-2 mb-2" data-toggle="modal" data-target="#shareModal">
            <i class="la la-share mr-1"></i>Share
          </button>
          <button class="btn theme-btn theme-btn-sm theme-btn-transparent lh-28 mb-2" data-toggle="modal" data-target="#reportModal">
            <i class="la la-flag mr-1"></i>Report abuse
          </button>
        </div>
      </div><!-- end breadcrumb-content -->
    </div><!-- end col-lg-8 -->
  </div><!-- end container -->
</section><!-- end breadcrumb-area -->
<!-- ================================
    END BREADCRUMB AREA
================================= -->

<!--======================================
        START COURSE DETAILS AREA
======================================-->
<section class="course-details-area pb-20px">
  <div class="container">
    <div class="row">
      <div class="col-lg-8 pb-5">
        <div class="course-details-content-wrap pt-90px">
          <div class="course-overview-card bg-gray p-4 rounded">
            <h3 class="fs-24 font-weight-semi-bold pb-3">What you'll learn</h3>
            <ul class="generic-list-item overview-list-item">
              @forelse ($goals as $goal)
              <li><i class="la la-check mr-1 text-black"></i> {{ trim($goal) }}</li>
              @empty
              <li>No goals specified for this course.</li>
              @endforelse
            </ul>
          </div><!-- end course-overview-card -->
       
          <div class="course-overview-card">
            <h3 class="fs-24 font-weight-semi-bold pb-3">Requirements</h3>
            <ul class="generic-list-item generic-list-item-bullet fs-15">
              <li>{{ $course->prerequisites }}</li>
            </ul>
          </div><!-- end course-overview-card -->
          
          <div class="course-overview-card">
            <h3 class="fs-24 font-weight-semi-bold pb-3">Description</h3>
            <p class="fs-15 pb-2">{!! $course->description !!}</p>
            <div class="collapse" id="collapseMore">
              <h4 class="fs-20 font-weight-semi-bold py-2">Who this course is for:</h4>
              <p class="fs-15 pb-2">{{ $course->prerequisites }}</p>
            </div>
            <a class="collapse-btn collapse--btn fs-15" data-toggle="collapse" href="#collapseMore" role="button" aria-expanded="false" aria-controls="collapseMore">
              <span class="collapse-btn-hide">Show more<i class="la la-angle-down ml-1 fs-14"></i></span>
              <span class="collapse-btn-show">Show less<i class="la la-angle-up ml-1 fs-14"></i></span>
            </a>
          </div><!-- end course-overview-card -->

          <div class="course-overview-card">
            <div class="curriculum-header d-flex align-items-center justify-content-between pb-4">
              <h3 class="fs-24 font-weight-semi-bold">Course content</h3>
              <div class="curriculum-duration fs-15">
                <span class="curriculum-total__text mr-2"><strong class="text-black font-weight-semi-bold">Total:</strong> {{ $course->lectures->count() }} lectures</span>
                <span class="curriculum-total__hours"><strong class="text-black font-weight-semi-bold">Total hours:</strong> {{ $course->duration }}</span>
              </div>
            </div>

            <div class="curriculum-content">
              <div id="accordion" class="generic-accordion">
                @foreach ($course->sections as $sec)
                <div class="card">
                  <div class="card-header" id="heading{{ $sec->id }}">
                    <button class="btn btn-link d-flex align-items-center justify-content-between w-100 section-toggle">
                      <span class="d-flex align-items-center">
                        <i class="la la-plus mr-2"></i>
                        <i class="la la-minus mr-2" style="display: none;"></i>
                        {{ $sec->section_title }}
                      </span>
                      <span class="fs-15 text-gray font-weight-medium">
                        {{ $sec->lectures->count() }} {{ $sec->lectures->count() === 1 ? 'lecture' : 'lectures' }}
                      </span>
                    </button>
                  </div><!-- end card-header -->
                  <div id="collapse{{ $sec->id }}" class="section-content" style="display: none;">
                    <div class="card-body">
                      <ul class="generic-list-item">
                        @foreach ($sec->lectures as $lect)
                        <li class="curriculum-content">
                          <div class="d-flex align-items-center justify-content-between">
                            <span>
                              <i class="la la-play-circle mr-2"></i>
                              {{ $lect->lecture_title }}
                            </span>
                            <span class="text-muted">{{ $lect->duration ?? '03:09' }}</span>
                          </div>
                        </li>
                        @endforeach
                      </ul>
                    </div><!-- end card-body -->
                  </div><!-- end section-content -->
                </div><!-- end card -->
                @endforeach
              </div><!-- end generic-accordion -->
            </div><!-- end curriculum-content -->
          </div><!-- end course-overview-card -->

          <div class="course-overview-card pt-4">
            <h3 class="fs-24 font-weight-semi-bold pb-4">About the instructor</h3>
            <div class="instructor-wrap">
              <div class="media media-card">
                <div class="instructor-img">
                  <a href="{{ route('instructor.details', $course->instructor_id) }}" class="media-img d-block">
                    <img class="lazy" src="{{ $course->instructor->photo ? asset('storage/upload/instructor_images/' . $course->instructor->photo) : asset('images/no_image.jpg') }}" alt="Instructor image" loading="lazy" onerror="this.src='{{ asset('images/no_image.jpg') }}'">
                  </a>
                  <ul class="generic-list-item pt-3">
                    <li><i class="la la-play-circle-o mr-2 text-color-3"></i> {{ $instructorCourses->count() }} Courses</li>
                    <li><a href="{{ route('instructor.details', $course->instructor_id) }}">View all Courses</a></li>
                  </ul>
                </div><!-- end instructor-img -->
                <div class="media-body">
                  <h5><a href="{{ route('instructor.details', $course->instructor_id) }}">{{ $course->instructor->name ?? 'Unknown Instructor' }}</a></h5>
                  <span class="d-block lh-18 pt-2 pb-3">Joined {{ $course->instructor && $course->instructor->created_at ? \Carbon\Carbon::parse($course->instructor->created_at)->diffForHumans() : 'N/A' }}</span>
                  <p class="text-black lh-18 pb-3">{{ $course->instructor->email ?? 'No email available' }}</p>
                </div>
              </div>
            </div><!-- end instructor-wrap -->
          </div><!-- end course-overview-card -->

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
                  </div><!-- end rating-wrap -->
                </div><!-- end review-rating-summary -->
                <div class="media-body">
                  @php
                  $reviewcount = App\Models\Review::where('course_id', $course->id)
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
                    </div><!-- end review-bars__fill -->
                    <div class="review-bars__percent">{{ number_format($ratingInfo['percent'], 2) }}%</div>
                  </div><!-- end review-bars -->
                  @endforeach
                  @else
                  <p>No Reviews Available</p>
                  @endif
                </div><!-- end media-body -->
              </div>
            </div><!-- end feedback-wrap -->
          </div><!-- end course-overview-card -->
          
          <div class="course-overview-card pt-4">
            <h3 class="fs-24 font-weight-semi-bold pb-4">Reviews</h3>
            <div class="review-wrap">
              @php
              $reviews = App\Models\Review::where('course_id', $course->id)->where('status', 1)->latest()->limit(5)->get();
              @endphp
              @foreach ($reviews as $item)
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
                  <p class="pb-2">{{ $item->comment }}</p>
                </div>
              </div><!-- end media -->
              @endforeach
            </div><!-- end review-wrap -->
          </div><!-- end course-overview-card -->

          @guest
          <p><b>For Add Course Review. You need to login first <a href="{{ route('login') }}">Login Here</a></b></p>
          @else
          <div class="course-overview-card pt-4">
            <h3 class="fs-24 font-weight-semi-bold pb-4">Add a Review</h3>
            <form method="post" action="{{ route('store.review') }}" class="row">
              @csrf
              <div class="leave-rating-wrap pb-4">
                <div class="leave-rating leave--rating">
                  <input type="radio" name="rate" id="star5" value="5" />
                  <label for="star5"></label>
                  <input type="radio" name="rate" id="star4" value="4" />
                  <label for="star4"></label>
                  <input type="radio" name="rate" id="star3" value="3" />
                  <label for="star3"></label>
                  <input type="radio" name="rate" id="star2" value="2" />
                  <label for="star2"></label>
                  <input type="radio" name="rate" id="star1" value="1" />
                  <label for="star1"></label>
                </div><!-- end leave-rating -->
              </div>
              <input type="hidden" name="course_id" value="{{ $course->id }}">
              <input type="hidden" name="instructor_id" value="{{ $course->instructor_id }}">
              <div class="input-box col-lg-12">
                <label class="label-text">Message</label>
                <div class="form-group">
                  <textarea class="form-control form--control pl-3" name="comment" placeholder="Write Message" rows="5"></textarea>
                </div>
              </div><!-- end input-box -->
              <div class="btn-box col-lg-12">
                <button class="btn theme-btn" type="submit">Submit Review</button>
              </div><!-- end btn-box -->
            </form>
          </div><!-- end course-overview-card -->
          @endguest
        </div><!-- end course-details-content-wrap -->
      </div><!-- end col-lg-8 -->
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
                      <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="-307.4 338.8 91.8 91.8" style="enable-background:new -307.4 338.8 91.8 91.8;" xml:space="preserve">
                        <style type="text/css">
                          .st0 { fill: #ffffff; border-radius: 100px; }
                          .st1 { fill: #000000; }
                        </style>
                        <g>
                          <circle class="st0" cx="-261.5" cy="384.7" r="45.9"></circle>
                          <path class="st1" d="M-272.9,363.2l35.8,20.7c0.7,0.4,0.7,1.3,0,1.7l-35.8,20.7c-0.7,0.4-1.5-0.1-1.5-0.9V364C-274.4,363.3-273.5,362.8-272.9,363.2z"></path>
                        </g>
                      </svg>
                    </div>
                    <p class="fs-15 font-weight-bold text-white pt-3">Preview this course</p>
                  </div>
                </a>
              </div><!-- end preview-course-video -->
              @php
              $finalPrice = $course->discount_price !== null
                ? max(0, $course->selling_price - $course->discount_price)
                : $course->selling_price;
              $discountPercentage = ($course->selling_price > 0 && $course->discount_price !== null)
                ? round(($course->discount_price / $course->selling_price) * 100)
                : 0;
              @endphp
              <div class="preview-course-feature-content pt-40px">
                <p class="d-flex align-items-center pb-2">
                  @if ($finalPrice < $course->selling_price)
                    <span class="fs-35 font-weight-semi-bold text-black">${{ number_format($finalPrice, 2) }}</span>
                    <span class="before-price mx-1">${{ number_format($course->selling_price, 2) }}</span>
                  @else
                    <span class="fs-35 font-weight-semi-bold text-black">${{ number_format($finalPrice, 2) }}</span>
                  @endif
                  @if ($discountPercentage > 0)
                    <span class="price-discount">{{ $discountPercentage }}% off</span>
                  @endif
                </p>
                <p class="preview-price-discount-text pb-35px">
                  <span class="text-color-3">4 days</span> left at this price!
                </p>
                <div class="buy-course-btn-box">
                  <form action="{{ route('cart.add', $course->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn theme-btn flex-grow-1 mr-3">
                      <i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart
                    </button>
                  </form>
                </div>
                <div class="preview-course-incentives">
                  <h3 class="card-title fs-18 pb-2">This course includes</h3>
                  <ul class="generic-list-item pb-3">
                    <li><i class="la la-play-circle-o mr-2 text-color"></i>{{ $course->duration }} learning hours</li>
                    <li><i class="la la-file mr-2 text-color"></i>{{ $course->resources }} resources</li>
                    <li><i class="la la-file mr-2 text-color"></i> certificate : {{ $course->certificate }} </li>
                    <li><i class="la la-key mr-2 text-color"></i>Full lifetime access</li>
                  </ul>
                </div><!-- end preview-course-incentives -->
              </div><!-- end preview-course-content -->
            </div>
          </div><!-- end card -->
          
          <div class="card card-item">
            <div class="card-body">
              <h3 class="card-title fs-18 pb-2">Course Categories</h3>
              <div class="divider"><span></span></div>
              <ul class="generic-list-item">
                @foreach ($categories as $cat)
                <li><a href="{{ url('category/'.$cat->id.'/'.$cat->category_slug) }}">{{ $cat->category_name }}</a></li>
                @endforeach
              </ul>
            </div>
          </div><!-- end card -->
        </div><!-- end sidebar -->
      </div><!-- end col-lg-4 -->
    </div><!-- end row -->
  </div><!-- end container -->
</section><!-- end course-details-area -->
<!--======================================
        END COURSE DETAILS AREA
======================================-->

<!--======================================
        START RELATED COURSE AREA
======================================-->
<section class="related-course-area bg-gray pt-60px pb-60px">
  <div class="container">
    <div class="related-course-wrap">
      <h3 class="fs-28 font-weight-semi-bold pb-35px">More Courses by <a href="{{ route('instructor.details', $course->instructor_id) }}" class="text-color hover-underline">{{ $course->instructor->name ?? 'Unknown Instructor' }}</a></h3>
      <div class="view-more-carousel-2 owl-action-styled">
        @foreach ($instructorCourses as $inscourse)
        @php
        $insFinalPrice = $inscourse->discount_price !== null
          ? max(0, $inscourse->selling_price - $inscourse->discount_price)
          : $inscourse->selling_price;
        $insDiscountPercentage = ($inscourse->selling_price > 0 && $inscourse->discount_price !== null)
          ? round(($inscourse->discount_price / $inscourse->selling_price) * 100)
          : 0;
        $insRating = $inscourse->reviews->avg('rating') ?? 0;
        $insReviewsCount = $inscourse->reviews->count();
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
          </div><!-- end card-image -->
          <div class="card-body">
            <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $inscourse->label }}</h6>
            <h5 class="card-title"><a href="{{ url('course/details/'.$inscourse->id.'/'.$inscourse->course_name_slug) }}">{{ $inscourse->course_name }}</a></h5>
            <p class="card-text"><a href="{{ route('instructor.details', $inscourse->instructor_id) }}">{{ $inscourse->instructor->name ?? 'Unknown Instructor' }}</a></p>
            <div class="rating-wrap d-flex align-items-center py-2">
              <div class="review-stars">
                <span class="rating-number">{{ number_format($insRating, 1) }}</span>
                @for ($i = 1; $i <= 5; $i++)
                  <span class="la la-star{{ $i <= floor($insRating) ? '' : '-o' }}"></span>
                @endfor
              </div>
              <span class="rating-total pl-1">({{ number_format($insReviewsCount) }})</span>
            </div><!-- end rating-wrap -->
            <div class="d-flex justify-content-between align-items-center">
              @if ($insFinalPrice < $inscourse->selling_price)
                <p class="card-price text-black font-weight-bold">${{ number_format($insFinalPrice, 2) }} <span class="before-price font-weight-medium">${{ number_format($inscourse->selling_price, 2) }}</span></p>
              @else
                <p class="card-price text-black font-weight-bold">${{ number_format($insFinalPrice, 2) }}</p>
              @endif
              <div class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Add to Wishlist"><i class="la la-heart-o"></i></div>
            </div>
          </div><!-- end card-body -->
        </div><!-- end card -->
        @endforeach
      </div><!-- end view-more-carousel -->
    </div><!-- end related-course-wrap -->
  </div><!-- end container -->
</section><!-- end related-course-area -->
<!--======================================
        END RELATED COURSE AREA
======================================-->

<!-- Tooltip Templates -->
@foreach ($instructorCourses as $inscourse)
  @php
  $insFinalPrice = $inscourse->discount_price !== null
    ? max(0, $inscourse->selling_price - $inscourse->discount_price)
    : $inscourse->selling_price;
  $insRating = $inscourse->reviews->avg('rating') ?? 0;
  $insReviewsCount = $inscourse->reviews->count();
  $goals = App\Models\Course_goal::where('course_id', $inscourse->id)->orderBy('id', 'DESC')->get();
  @endphp
  <div class="tooltip_templates" style="display: none;">
    <div id="tooltip_content_{{ $inscourse->id }}">
      <div class="card card-item">
        <div class="card-body">
          <p class="card-text pb-2">By <a href="{{ route('instructor.details', $inscourse->instructor_id) }}">{{ $inscourse->instructor->name ?? 'Unknown Instructor' }}</a></p>
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
          <p class="card-text pt-1 fs-14 lh-22">{{ $inscourse->description ?? 'No description available.' }}</p>
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
            <form action="{{ route('cart.add', $inscourse->id) }}" method="POST">
              @csrf
              <button type="submit" class="btn theme-btn flex-grow-1 mr-3">
                <i class="la la-shopping-cart mr-1 fs-18"></i> Add to Cart
              </button>
            </form>
            <div class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Add to Wishlist"><i class="la la-heart-o"></i></div>
          </div>
        </div>
      </div><!-- end card -->
    </div>
  </div><!-- end tooltip_templates -->
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
      </div><!-- end modal-header -->
      <div class="modal-body">
        <div class="copy-to-clipboard">
          <span class="success-message">Copied!</span>
          <div class="input-group">
            <input type="text" class="form-control form--control copy-input pl-3" value="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}">
            <div class="input-group-append">
              <button class="btn theme-btn theme-btn-sm copy-btn shadow-none"><i class="la la-copy mr-1"></i> Copy</button>
            </div>
          </div>
        </div><!-- end copy-to-clipboard -->
      </div><!-- end modal-body -->
      <div class="modal-footer justify-content-center border-top-gray">
        <ul class="social-icons social-icons-styled">
          <li><a href="#" class="facebook-bg"><i class="la la-facebook"></i></a></li>
          <li><a href="#" class="twitter-bg"><i class="la la-twitter"></i></a></li>
          <li><a href="#" class="instagram-bg"><i class="la la-instagram"></i></a></li>
        </ul>
      </div><!-- end modal-footer -->
    </div><!-- end modal-content-->
  </div><!-- end modal-dialog -->
</div><!-- end modal -->

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
      </div><!-- end modal-header -->
      <div class="modal-body">
        <video controls crossorigin playsinline poster="{{ $course->course_image ? asset('storage/upload/course_images/thumbnail/' . $course->course_image) : asset('images/no_image.jpg') }}" id="player">
          <source src="{{ $course->video ? asset('storage/upload/course_images/video/' . $course->video) : '' }}" type="video/mp4" />
          <p>Your browser doesn't support HTML5 video. Here is a <a href="{{ $course->video ? asset('storage/upload/course_images/video/' . $course->video) : '#' }}">link to the video</a> instead.</p>
        </video>
      </div><!-- end modal-body -->
    </div><!-- end modal-content -->
  </div><!-- end modal-dialog -->
</div><!-- end modal -->

<div class="modal fade modal-container" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header border-bottom-gray">
        <div class="pr-2">
          <h5 class="modal-title fs-19 font-weight-semi-bold lh-24" id="reportModalTitle">Report Abuse</h5>
          <p class="pt-1 fs-14 lh-24">Flagged content is reviewed by Aduca staff to determine whether it violates Terms of Service or Community Guidelines. If you have a question or technical issue, please contact our
            <a href="contact.html" class="text-color hover-underline">Support team here</a>.
          </p>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="la la-times"></span>
        </button>
      </div><!-- end modal-header -->
      <div class="modal-body">
        <form method="post">
          <div class="input-box">
            <label class="label-text">Select Report Type</label>
            <div class="form-group">
              <div class="select-container w-auto">
                <select class="select-container-select">
                  <option value>-- Select One --</option>
                  <option value="1">Inappropriate Course Content</option>
                  <option value="2">Inappropriate Behavior</option>
                  <option value="3">Aduca Policy Violation</option>
                  <option value="4">Spammy Content</option>
                  <option value="5">Other</option>
                </select>
              </div>
            </div>
          </div>
          <div class="input-box">
            <label class="label-text">Write Message</label>
            <div class="form-group">
              <textarea class="form-control form--control pl-3" name="message" placeholder="Provide additional details here..." rows="5"></textarea>
            </div>
          </div>
          <div class="btn-box text-right pt-2">
            <button type="button" class="btn font-weight-medium mr-3" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn theme-btn theme-btn-sm lh-30">Submit <i class="la la-arrow-right icon ml-1"></i></button>
          </div>
        </form>
      </div><!-- end modal-body -->
    </div><!-- end modal-content -->
  </div><!-- end modal-dialog -->
</div><!-- end modal -->

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
  $('.section-toggle').on('click', function() {
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

  $('.card-preview').tooltipster({
    theme: 'tooltipster-shadow',
    interactive: true,
    contentAsHTML: true,
    maxWidth: 400,
    side: 'right',
    distance: 10
  });
});
</script>

@endsection