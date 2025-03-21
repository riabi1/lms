@php
$courses = App\Models\Course::where('status', 1)->orderBy('id', 'ASC')->limit(6)->get();
$categories = App\Models\Category::orderBy('category_name', 'ASC')->get();
@endphp

<section class="course-area pb-120px">
  <div class="container">
    <div class="section-heading text-center">
      <h5 class="ribbon ribbon-lg mb-2">Choose your desired courses</h5>
      <h2 class="section__title">The world's largest selection of courses</h2>
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
           @foreach($courses as $course)
    <div class="tooltip_templates">
        <div id="tooltip_content_{{ $course->id }}">
            <div class="card card-item">
                <div class="card-body">
                    <p class="card-text pb-2">By <a href="{{ route('instructor.details', $course->instructor_id) }}">{{ $course->instructor->name ?? 'Unknown Instructor' }}</a></p>
                    <h5 class="card-title pb-1"><a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}">{{ $course->course_name }}</a></h5>
                    <div class="d-flex align-items-center pb-1">
                        @if($course->bestseller == 1)
                            <h6 class="ribbon fs-14 mr-2">Bestseller</h6>
                        @else
                            <h6 class="ribbon fs-14 mr-2">New</h6>
                        @endif
                        <p class="text-success fs-14 font-weight-medium">Updated <span class="font-weight-bold pl-1">{{ $course->created_at ? $course->created_at->format('M d Y') : 'N/A' }}</span></p>
                    </div>
                    <ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet d-flex align-items-center fs-14">
                        <li>{{ $course->duration ?? 'N/A' }} total hours</li>
                        <li>{{ $course->label ?? 'N/A' }}</li>
                    </ul>
                    <p class="card-text pt-1 fs-14 lh-22">{{ $course->prerequisites ?? 'No prerequisites specified.' }}</p>
                    @php
                        $goals = App\Models\Course_goal::where('course_id', $course->id)->orderBy('id', 'DESC')->get();
                    @endphp
                    <ul class="generic-list-item fs-14 py-3">
                        @foreach($goals as $goal)
                            <li><i class="la la-check mr-1 text-black"></i> {{ $goal->goal_name }}</li>
                        @endforeach
                    </ul>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('cart.add', $course->id) }}" class="btn theme-btn flex-grow-1 mr-3"><i class="la la-shopping-cart mr-1 fs-18"></i> Add to Cart</a>
                        <div class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Add to Wishlist"><i class="la la-heart-o"></i></div>
                    </div>
                </div>
            </div><!-- end card -->
        </div>
    </div><!-- end tooltip_templates -->
@endforeach
          </div><!-- end row -->
        </div><!-- end tab-pane -->

        <!-- Category-wise Tabs -->
        @foreach ($categories as $category)
        <div class="tab-pane fade" id="category{{ $category->id }}" role="tabpanel" aria-labelledby="category-{{ $category->id }}-tab">
          <div class="row">
            @php
            $catwiseCourse = App\Models\Course::where('category_id', $category->id)
                            ->where('status', 1)
                            ->orderBy('id', 'DESC')
                            ->get();
            @endphp
            @forelse ($catwiseCourse as $course)
            <div class="col-lg-4 responsive-column-half">
              <div class="card card-item">
                <div class="card-image">
                  <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}" class="d-block">
                    <img class="card-img-top lazy" src="{{ asset('storage/upload/course_images/thumbnail/' . $course->course_image) }}" alt="Course image" onerror="this.src='{{ asset('images/default-course.jpg') }}'">
                  </a>
                  @php
                  $amount = $course->selling_price - $course->discount_price;
                  $discount = $course->selling_price > 0 ? ($amount / $course->selling_price) * 100 : 0;
                  @endphp
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
                    <div class="course-badge blue">{{ round($discount) }}%</div>
                    @endif
                  </div>
                </div><!-- end card-image -->
                <div class="card-body">
                  <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $course->label }}</h6>
                  <h5 class="card-title">
                    <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}">{{ $course->course_name }}</a>
                  </h5>
                  <div class="d-flex justify-content-between align-items-center">
                    @if ($course->discount_price == null)
                    <p class="card-price text-black font-weight-bold">${{ $course->selling_price }}</p>
                    @else
                    <p class="card-price text-black font-weight-bold">${{ $course->discount_price }}
                      <span class="before-price font-weight-medium">${{ $course->selling_price }}</span>
                    </p>
                    @endif
                  </div>
                </div><!-- end card-body -->
              </div><!-- end card -->
            </div><!-- end col-lg-4 -->
            @empty
            <div class="col-12">
              <h5 class="text-danger text-center">No Course Found</h5>
            </div>
            @endforelse
          </div><!-- end row -->
        </div><!-- end tab-pane -->
        @endforeach
      </div><!-- end tab-content -->
      <div class="more-btn-box mt-4 text-center">
        <a href="{{ route('courses.all') }}" class="btn theme-btn">Browse all Courses <i class="la la-arrow-right icon ml-1"></i></a>
      </div><!-- end more-btn-box -->
    </div><!-- end container -->
  </div><!-- end card-content-wrapper -->
</section><!-- end courses-area -->