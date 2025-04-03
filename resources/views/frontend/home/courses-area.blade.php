@php
$courses = App\Models\Course::with(['courseable', 'reviews', 'goals'])->where('status', 1)->orderBy('id', 'ASC')->limit(6)->get();
$categories = App\Models\Category::orderBy('category_name', 'ASC')->get();
@endphp

<section class="course-area pb-120px">
  <div class="container">
    <div class="section-heading text-center">
      <h5 class="ribbon ribbon-lg mb-2">Choose your desired courses</h5>
      <h2 class="section__title">The world’s largest selection of courses</h2>
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
  </div><!-- end container -->

  <div class="card-content-wrapper bg-gray pt-50px pb-120px">
    <div class="container">
      <div class="tab-content" id="myTabContent">
        <!-- All Courses Tab -->
        <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
          <div class="row">
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
            $instructor = $course->courseable instanceof \App\Models\Instructor ? $course->courseable : null;
            @endphp
            <div class="col-lg-4 responsive-column-half">
              <div class="card card-item card-preview" data-tooltip-content="#tooltip_content_{{ $course->id }}">
                <div class="card-image">
                  <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}" class="d-block">
                    <img class="card-img-top lazy" src="{{ asset('storage/upload/course_images/thumbnail/' . $course->course_image) }}" alt="Course image" onerror="this.src='{{ asset('images/default-course.jpg') }}'">
                  </a>
                  <div class="course-badge-labels">
                    @if ($course->bestseller == 1)
                    <div class="course-badge">Bestseller</div>
                    @endif
                    @if ($course->highestrated == 1)
                    <div class="course-badge sky-blue">Highest Rated</div>
                    @endif
                    @if ($course->discount_price == null)
                    <div class="course-badge blue">New</div>
                    @else
                    <div class="course-badge blue">{{ $discountPercentage }}%</div>
                    @endif
                  </div>
                </div><!-- end card-image -->
                <div class="card-body">
                  <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $course->label }}</h6>
                  <h5 class="card-title">
                    <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}">{{ $course->course_name }}</a>
                  </h5>
                  <div class="d-flex justify-content-between align-items-center">
                    @if ($finalPrice < $course->selling_price)
                    <p class="card-price text-black font-weight-bold">{{ number_format($finalPrice, 2) }} TND
                      <span class="before-price font-weight-medium">{{ number_format($course->selling_price, 2) }} TND</span>
                    </p>
                    @else
                    <p class="card-price text-black font-weight-bold">{{ number_format($finalPrice, 2) }} TND</p>
                    @endif
                  </div>
                </div><!-- end card-body -->
              </div><!-- end card -->

              <!-- Contenu du tooltip -->
              <div class="tooltip_templates" style="display: none;">
                <div id="tooltip_content_{{ $course->id }}">
                  <div class="card-body">
                    <p class="card-text pb-2">By 
                      @if ($instructor)
                        <a href="{{ route('instructor.details', $instructor->id) }}">{{ $instructor->name }}</a>
                      @else
                        Unknown Instructor
                      @endif
                    </p>
                    <h5 class="card-title pb-1">
                      <a href="{{ route('course.details', [$course->id, $course->course_name_slug]) }}">{{ $course->course_title }}</a>
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
                      <li>{{ $course->duration ?? 'N/A' }}</li>
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
                       <form action="{{ route('cart.add', $course->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn theme-btn flex-grow-1 mr-3">
                          <i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart
                        </button>
                      </form>
                      <div class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Add to Wishlist"><i class="la la-heart-o"></i></div>
                    </div>
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
        </div><!-- end tab-pane -->

        <!-- Category-wise Tabs -->
        @foreach ($categories as $category)
        <div class="tab-pane fade" id="category{{ $category->id }}" role="tabpanel" aria-labelledby="category-{{ $category->id }}-tab">
          <div class="row">
            @php
            $catwiseCourse = App\Models\Course::with(['courseable', 'reviews', 'goals'])
                ->whereHas('subcategory', function ($query) use ($category) {
                    $query->where('category_id', $category->id);
                })
                ->where('status', 1)
                ->orderBy('id', 'DESC')
                ->get();
            @endphp
            @forelse ($catwiseCourse as $course)
            @php
            $finalPrice = $course->discount_price !== null
              ? max(0, $course->selling_price - $course->discount_price)
              : $course->selling_price;
            $discountPercentage = ($course->selling_price > 0 && $course->discount_price !== null)
              ? round(($course->discount_price / $course->selling_price) * 100)
              : 0;
            $rating = $course->reviews->avg('rating') ?? 0;
            $reviews_count = $course->reviews->count();
            $instructor = $course->courseable instanceof \App\Models\Instructor ? $course->courseable : null;
            @endphp
            <div class="col-lg-4 responsive-column-half">
              <div class="card card-item card-preview" data-tooltip-content="#tooltip_content_{{ $course->id }}">
                <div class="card-image">
                  <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}" class="d-block">
                    <img class="card-img-top lazy" src="{{ asset('storage/upload/course_images/thumbnail/' . $course->course_image) }}" alt="Course image" onerror="this.src='{{ asset('images/default-course.jpg') }}'">
                  </a>
                  <div class="course-badge-labels">
                    @if ($course->bestseller == 1)
                    <div class="course-badge">Bestseller</div>
                    @endif
                    @if ($course->highestrated == 1)
                    <div class="course-badge sky-blue">Highest Rated</div>
                    @endif
                    @if ($course->discount_price == null)
                    <div class="course-badge blue">New</div>
                    @else
                    <div class="course-badge blue">{{ $discountPercentage }}%</div>
                    @endif
                  </div>
                </div><!-- end card-image -->
                <div class="card-body">
                  <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $course->label }}</h6>
                  <h5 class="card-title">
                    <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}">{{ $course->course_name }}</a>
                  </h5>
                  <div class="d-flex justify-content-between align-items-center">
                    @if ($finalPrice < $course->selling_price)
                    <p class="card-price text-black font-weight-bold">{{ number_format($finalPrice, 2) }} TND
                      <span class="before-price font-weight-medium">{{ number_format($course->selling_price, 2) }} TND</span>
                    </p>
                    @else
                    <p class="card-price text-black font-weight-bold">{{ number_format($finalPrice, 2) }} TND</p>
                    @endif
                  </div>
                </div><!-- end card-body -->
              </div><!-- end card -->

              <!-- Contenu du tooltip -->
              <div class="tooltip_templates" style="display: none;">
                <div id="tooltip_content_{{ $course->id }}">
                  <div class="card-body">
                    <p class="card-text pb-2">By 
                      @if ($instructor)
                        <a href="{{ route('instructor.details', $instructor->id) }}">{{ $instructor->name }}</a>
                      @else
                        Unknown Instructor
                      @endif
                    </p>
                    <h5 class="card-title pb-1">
                      <a href="{{ route('course.details', [$course->id, $course->course_name_slug]) }}">{{ $course->course_title }}</a>
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
                      <li>{{ $course->duration ?? 'N/A' }}</li>
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
                      <form action="{{ route('cart.add', $course->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn theme-btn flex-grow-1 mr-3">
                          <i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart
                        </button>
                      </form>
                      <div class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Add to Wishlist"><i class="la la-heart-o"></i></div>
                    </div>
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
        </div><!-- end tab-pane -->
        @endforeach
      </div><!-- end tab-content -->
      <div class="more-btn-box mt-4 text-center">
        <a href="{{ route('course.list') }}" class="btn theme-btn">Browse all Courses <i class="la la-arrow-right icon ml-1"></i></a>
      </div><!-- end more-btn-box -->
    </div><!-- end container -->
  </div><!-- end card-content-wrapper -->
</section><!-- end courses-area -->

<!-- Script pour initialiser Tooltipster avec position à droite -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
});
</script>