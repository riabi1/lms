@extends('frontend.master')

@section('title')
{{ $subcategory->subcategory_name }} | Easy Learning
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
                <h2 class="section__title text-white">{{ $subcategory->subcategory_name }}</h2>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('category/'.$subcategory->category->id.'/'.$subcategory->category->category_slug) }}">{{ $subcategory->category->category_name }}</a></li>
                <li>{{ $subcategory->subcategory_name }}</li>
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
            <form method="GET" action="{{ route('subcategory.course', [$subcategory->id, $subcategory->subcategory_slug]) }}" class="d-flex flex-wrap align-items-center justify-content-between w-100">
                <div class="d-flex flex-wrap align-items-center">
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
            <div class="col-lg-4">
                <div class="sidebar mb-5">
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
            <div class="col-lg-8">
                <div class="row">
                    @forelse ($courses as $course)
                        @php
                            $finalPrice = $course->discount_price !== null
                                ? max(0, $course->selling_price - $course->discount_price)
                                : $course->selling_price;
                            $discount = $course->selling_price && $course->discount_price !== null
                                ? round(($course->selling_price - $finalPrice) / $course->selling_price * 100)
                                : 0;
                            $rating = $course->reviews->avg('rating') ?? 0;
                            $reviewsCount = $course->reviews->count();
                        @endphp
                        <div class="col-lg-6 responsive-column-half">
                            <div class="card card-item card-preview">
                                <div class="card-image">
                                    <a href="{{ route('course.details', [$course->id, $course->course_name_slug]) }}" class="d-block">
                                        <img class="card-img-top lazy" src="{{ asset('storage/upload/course_images/thumbnail/' . ($course->course_image ?? 'default-course.jpg')) }}" alt="{{ $course->course_title }}" onerror="this.src='{{ asset('images/default-course.jpg') }}'">
                                    </a>
                                    <div class="course-badge-labels">
                                        @if ($course->bestseller == 1)
                                            <div class="course-badge">Bestseller</div>
                                        @endif
                                        @if ($discount > 0)
                                            <div class="course-badge blue">-{{ $discount }}%</div>
                                        @elseif ($course->discount_price === null)
                                            <div class="course-badge blue">New</div>
                                        @endif
                                    </div>
                                </div><!-- end card-image -->
                                <div class="card-body">
                                    <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $course->label ?? 'All Levels' }}</h6>
                                    <h5 class="card-title">
                                        <a href="{{ route('course.details', [$course->id, $course->course_name_slug]) }}">{{ $course->course_title }}</a>
                                    </h5>
                                    <p class="card-text">
                                        <a href="{{ route('instructor.details', $course->courseable->id) }}">{{ $course->courseable->name ?? 'Unknown Instructor' }}</a>
                                    </p>
                                    <div class="rating-wrap d-flex align-items-center py-2">
                                        <div class="review-stars">
                                            <span class="rating-number">{{ number_format($rating, 1) }}</span>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="la la-star{{ $i <= floor($rating) ? '' : '-o' }}"></span>
                                            @endfor
                                        </div>
                                        <span class="rating-total pl-1">({{ number_format($reviewsCount) }})</span>
                                    </div><!-- end rating-wrap -->
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="card-price text-black font-weight-bold">
                                            ${{ number_format($finalPrice, 2) }}
                                            @if ($discount > 0)
                                                <span class="before-price font-weight-medium">${{ number_format($course->selling_price, 2) }}</span>
                                            @endif
                                        </p>
                                        <div class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Add to Wishlist"><i class="la la-heart-o"></i></div>
                                    </div>
                                </div><!-- end card-body -->
                            </div><!-- end card -->
                        </div><!-- end col-lg-6 -->
                    @empty
                        <div class="col-lg-12">
                            <p class="text-center">No courses found in this subcategory.</p>
                        </div>
                    @endforelse
                </div><!-- end row -->
                <div class="text-center pt-3">
                    {{ $courses->links() }}
                    <p class="fs-14 pt-2">Showing {{ $courses->firstItem() ?? 0 }}–{{ $courses->lastItem() ?? 0 }} of {{ $courses->total() }} results</p>
                </div>
            </div><!-- end col-lg-8 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end courses-area -->
<!--======================================
        END COURSE AREA
======================================-->
@endsection