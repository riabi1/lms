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
            @foreach ($courses as $course)
            <div class="col-lg-4 responsive-column-half">
              <div class="card card-item">
                <div class="card-image">
                  <a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}" class="d-block">
                    <img class="card-img-top lazy" src="{{ asset($course->course_image) }}" alt="Course image">
                  </a>
                  @php
                  $amount = $course->selling_price - $course->discount_price;
                  $discount = $course->selling_price > 0 ? ($amount/$course->selling_price) * 100 : 0;
                  @endphp
                  <div class="course-badge-labels">
                    @if ($course->bestseller == 1)
                    <div class="course-badge">Bestseller</div>
                    @endif
                    @if ($course->highestrated == 1)
                    <div class="course-badge sky-blue">Highest Rated</div>
                    @endif
                    @if ($course->discount_price == NULL)
                    <div class="course-badge blue">New</div>
                    @else
                    <div class="course-badge blue">{{ round($discount) }}%</div>
                    @endif
                  </div>
                </div><!-- end card-image -->
                <div class="card-body">
                  <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $course->label }}</h6>
                  <h5 class="card-title">
                    <a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}">{{ $course->course_name }}</a>
                  </h5>
                 
                  <div class="d-flex justify-content-between align-items-center">
                    @if ($course->discount_price == NULL)
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
                  <a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}" class="d-block">
                    <img class="card-img-top lazy" src="{{ asset($course->course_image) }}" alt="Course image">
                  </a>
                  @php
                  $amount = $course->selling_price - $course->discount_price;
                  $discount = $course->selling_price > 0 ? ($amount/$course->selling_price) * 100 : 0;
                  @endphp
                  <div class="course-badge-labels">
                    @if ($course->bestseller == 1)
                    <div class="course-badge">Bestseller</div>
                    @endif
                    @if ($course->highestrated == 1)
                    <div class="course-badge sky-blue">Highest Rated</div>
                    @endif
                    @if ($course->discount_price == NULL)
                    <div class="course-badge blue">New</div>
                    @else
                    <div class="course-badge blue">{{ round($discount) }}%</div>
                    @endif
                  </div>
                </div><!-- end card-image -->
                <div class="card-body">
                  <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $course->label }}</h6>
                  <h5 class="card-title">
                    <a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}">{{ $course->course_name }}</a>
                  </h5>
                  
                  <div class="d-flex justify-content-between align-items-center">
                    @if ($course->discount_price == NULL)
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
        <a href="#" class="btn theme-btn">Browse all Courses <i class="la la-arrow-right icon ml-1"></i></a>
      </div><!-- end more-btn-box -->
    </div><!-- end container -->
  </div><!-- end card-content-wrapper -->
</section><!-- end courses-area -->