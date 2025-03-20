@php
$popularCourses = App\Models\Course::where('status', 1)->orderBy('id', 'ASC')->limit(6)->get();
@endphp

<section class="course-area pb-90px">
  <div class="course-wrapper">
    <div class="container">
      <div class="section-heading text-center">
        <h5 class="ribbon ribbon-lg mb-2">Learn on your schedule</h5>
        <h2 class="section__title">Students are viewing</h2>
        <span class="section-divider"></span>
      </div><!-- end section-heading -->
      <div class="course-carousel owl-action-styled owl--action-styled mt-30px">
        @forelse ($popularCourses as $course)
        <div class="card card-item card-preview" data-tooltip-content="#tooltip_content_{{ $course->id }}">
          <div class="card-image">
            <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}" class="d-block">
              <img class="card-img-top" src="{{ asset('storage/upload/course_images/thumbnail/' . $course->course_image) }}" alt="{{ $course->course_name }}" onerror="this.src='{{ asset('images/default-course.jpg') }}'">
            </a>
            <div class="course-badge-labels">
              @if ($course->bestseller == 1)
              <div class="course-badge">Bestseller</div>
              @endif
              @if ($course->highestrated == 1)
              <div class="course-badge sky-blue">Highest Rated</div>
              @endif
              @if ($course->discount_price == null && $course->selling_price == 0)
              <div class="course-badge green">Free</div>
              @elseif ($course->discount_price == null)
              <div class="course-badge blue">New</div>
              @else
              @php
              $amount = $course->selling_price - $course->discount_price;
              $discount = $course->selling_price > 0 ? ($amount / $course->selling_price) * 100 : 0;
              @endphp
              <div class="course-badge blue">{{ round($discount) }}%</div>
              @endif
            </div>
          </div><!-- end card-image -->
          <div class="card-body">
            <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $course->label ?? 'All Levels' }}</h6>
            <h5 class="card-title">
              <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]) }}">{{ $course->course_name }}</a>
            </h5>
            <p class="card-text">
              <a href="{{ route('instructor.details', $course->instructor_id) }}">{{ $course->instructor->name ?? 'Unknown Instructor' }}</a>
            </p>
            <div class="rating-wrap d-flex align-items-center py-2">
              <div class="review-stars">
                <span class="rating-number">{{ $course->rating ?? '4.0' }}</span>
                @for ($i = 1; $i <= 5; $i++)
                <span class="la la-star{{ $i <= ($course->rating ?? 4) ? '' : '-o' }}"></span>
                @endfor
              </div>
              <span class="rating-total pl-1">({{ $course->enroll_count ?? '0' }})</span>
            </div><!-- end rating-wrap -->
            <div class="d-flex justify-content-between align-items-center">
              @if ($course->discount_price == null)
              <p class="card-price text-black font-weight-bold">${{ $course->selling_price == 0 ? 'Free' : number_format($course->selling_price, 2) }}</p>
              @else
              <p class="card-price text-black font-weight-bold">${{ number_format($course->discount_price, 2) }}
                <span class="before-price font-weight-medium">${{ number_format($course->selling_price, 2) }}</span>
              </p>
              @endif
              <div class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Add to Wishlist"><i class="la la-heart-o"></i></div>
            </div>
          </div><!-- end card-body -->
        </div><!-- end card -->
        @empty
        <div class="col-12 text-center">
          <h5 class="text-danger">No courses available at the moment.</h5>
        </div>
        @endforelse
      </div><!-- end course-carousel -->
    </div><!-- end container -->
  </div><!-- end course-wrapper -->
</section><!-- end courses-area -->