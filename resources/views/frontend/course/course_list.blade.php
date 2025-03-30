@extends('frontend.master')

@section('title')
Course List | Easy Learning
@endsection

@section('home')
<!-- ================================
    START BREADCRUMB AREA
================================= -->
<section class="breadcrumb-area section-padding img-bg-2">
    <div class="overlay"></div>
    <div class="container">
        <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
            <div class="section-heading">
                <h2 class="section__title text-white">Course List</h2>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li>Course List</li>
            </ul>
        </div><!-- end breadcrumb-content -->
    </div><!-- end container -->
</section><!-- end breadcrumb-area -->
<!-- ================================
    END BREADCRUMB AREA
================================= -->

<!--======================================
        START COURSE AREA
======================================-->
<section class="course-area section--padding">
    <div class="container">
        <!-- Formulaire de filtration -->
        <div class="filter-bar mb-4">
            <form method="GET" action="{{ route('course.list') }}" class="d-flex flex-wrap align-items-center justify-content-between w-100">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="select-container select--container mr-3 mb-2">
                        <select name="category_id" class="select-container-select" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="select-container select--container mr-3 mb-2">
                        <select name="label" class="select-container-select" onchange="this.form.submit()">
                            <option value="">All Levels</option>
                            <option value="Beginner" {{ request('label') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="Intermediate" {{ request('label') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="Expert" {{ request('label') == 'Expert' ? 'selected' : '' }}>Expert</option>
                        </select>
                    </div>
                    <div class="select-container select--container mr-3 mb-2">
                        <select name="price" class="select-container-select" onchange="this.form.submit()">
                            <option value="">All Prices</option>
                            <option value="free" {{ request('price') == 'free' ? 'selected' : '' }}>Free</option>
                            <option value="paid" {{ request('price') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    <div class="select-container select--container mr-3 mb-2">
                        <select name="rating" class="select-container-select" onchange="this.form.submit()">
                            <option value="">All Ratings</option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4.0 & Up</option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3.0 & Up</option>
                            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2.0 & Up</option>
                        </select>
                    </div>
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input" id="bestseller" name="bestseller" value="1" {{ request('bestseller') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="custom-control-label" for="bestseller">Bestseller Only</label>
                    </div>
                </div>
                <p class="fs-14">We found <span class="text-black">{{ $courses->total() }}</span> courses available for you</p>
            </form>
        </div><!-- end filter-bar -->

        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    @foreach ($courses as $course)
                        @php
                            $finalPrice = $course->discount_price !== null
                                ? max(0, $course->selling_price - $course->discount_price)
                                : $course->selling_price;
                            $discountPercentage = ($course->selling_price > 0 && $course->discount_price !== null)
                                ? round(($course->discount_price / $course->selling_price) * 100)
                                : 0;
                            $instructor = $course->courseable instanceof \App\Models\Instructor ? $course->courseable : null;
                        @endphp
                        <div class="col-lg-4 col-md-6 responsive-column-half">
                            <div class="card card-item card-preview" data-tooltip-content="#tooltip_content_{{ $course->id }}">
                                <div class="card-image">
                                    <a href="{{ route('course.details', [$course->id, $course->course_name_slug]) }}" class="d-block">
                                        <img class="card-img-top lazy" src="{{ asset('storage/upload/course_images/thumbnail/' . $course->course_image) }}" alt="{{ $course->course_title }}" onerror="this.src='{{ asset('images/default-course.jpg') }}'">
                                    </a>
                                    <div class="course-badge-labels">
                                        @if ($course->bestseller == 1)
                                            <div class="course-badge">Bestseller</div>
                                        @endif
                                        @if ($course->discount_price !== null && $course->discount_price < $course->selling_price)
                                            <div class="course-badge blue">-{{ $discountPercentage }}%</div>
                                        @elseif ($course->discount_price === null)
                                            <div class="course-badge blue">New</div>
                                        @endif
                                    </div>
                                </div><!-- end card-image -->
                                <div class="card-body">
                                    <h5 class="card-title pb-1">
                                        <a href="{{ route('course.details', [$course->id, $course->course_name_slug]) }}">{{ $course->course_title }}</a>
                                    </h5>
                                    <p class="card-text">
                                        @if ($instructor)
                                            <a href="{{ route('instructor.details', $instructor->id) }}">{{ $instructor->name }}</a>
                                        @else
                                            Unknown Instructor
                                        @endif
                                    </p>
                                    <div class="rating-wrap d-flex align-items-center py-2">
                                        <div class="review-stars">
                                            <span class="rating-number">{{ number_format($course->rating, 1) }}</span>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="la la-star{{ $i <= floor($course->rating) ? '' : '-o' }}"></span>
                                            @endfor
                                        </div>
                                        <span class="rating-total pl-1">({{ number_format($course->reviews_count) }})</span>
                                    </div><!-- end rating-wrap -->
                                    @if ($finalPrice < $course->selling_price)
                                        <p class="card-price text-black font-weight-bold">{{ number_format($finalPrice, 2) }} TND
                                            <span class="before-price font-weight-medium">{{ number_format($course->selling_price, 2) }} TND</span>
                                        </p>
                                    @else
                                        <p class="card-price text-black font-weight-bold">{{ number_format($finalPrice, 2) }} TND</p>
                                    @endif
                                </div><!-- end card-body -->
                            </div><!-- end card -->

                            <!-- Contenu du tooltip affiché au survol -->
                            <div class="tooltip_templates" style="display: none;">
                                <div id="tooltip_content_{{ $course->id }}">
                                    <div class="card-body">
                                        <p class="card-text pb-2">
                                            By 
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
                                                <span class="rating-number">{{ number_format($course->rating, 1) }}</span>
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span class="la la-star{{ $i <= floor($course->rating) ? '' : '-o' }}"></span>
                                                @endfor
                                            </div>
                                            <span class="rating-total pl-1">({{ number_format($course->reviews_count) }})</span>
                                        </div><!-- end rating-wrap -->
                                        <ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet d-flex align-items-center fs-14">
                                            <li>{{ $course->duration ?? 'N/A' }}</li>
                                            <li>{{ $course->label ?? 'All Levels' }}</li>
                                        </ul>
                                        <p class="card-text pt-1 fs-14 lh-22">{{ $course->description ?? 'No description available.' }}</p>
                                        <ul class="generic-list-item fs-14 py-3">
                                            @foreach ($course->goals->take(3) as $goal)
                                                <li><i class="la la-check mr-1 text-black"></i> {{ $goal->goal_name }}</li>
                                            @endforeach
                                            @if ($course->goals->isEmpty())
                                                <li><i class="la la-check mr-1 text-black"></i> Learn key skills for this course</li>
                                                <li><i class="la la-check mr-1 text-black"></i> Boost your knowledge</li>
                                                <li><i class="la la-check mr-1 text-black"></i> Practical exercises included</li>
                                            @endif
                                        </ul>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <form action="{{ route('cart.add', $course->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn theme-btn w-100">
                                                    <i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart
                                                </button>
                                            </form> 
                                            <div class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Add to Wishlist"><i class="la la-heart-o"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end tooltip_templates -->
                        </div><!-- end col-lg-4 -->
                    @endforeach
                </div><!-- end row -->
                <div class="text-center pt-3">
                    {{ $courses->links() }}
                    <p class="fs-14 pt-2">Showing {{ $courses->firstItem() }}–{{ $courses->lastItem() }} of {{ $courses->total() }} results</p>
                </div>
            </div><!-- end col-lg-12 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end courses-area -->
<!--======================================
        END COURSE AREA
======================================-->

<!-- Script pour initialiser Tooltipster -->
<script>
    $(document).ready(function() {
        $('.card-preview').tooltipster({
            theme: 'tooltipster-shadow',
            interactive: true,
            contentAsHTML: true,
            maxWidth: 400
        });
    });
</script>
@endsection