@extends('instructor.instructor_dashboard')
@section('instructor')

<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3"> 
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $course->course_name }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('instructor.courses.edit', $course->id) }}" class="btn btn-primary">Edit Course</a>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">{{ $course->course_name }}</h5>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Course Title:</strong> {{ $course->course_title }}</p>
                    <p><strong>Category:</strong> {{ optional($course->category)->category_name ?? 'No Category' }}</p>
                    <p><strong>Subcategory:</strong> {{ optional($course->subCategory)->subcategory_name ?? 'No Subcategory' }}</p>
                    <p><strong>Price:</strong> ${{ $course->selling_price }}</p>
                    <p><strong>Discount Price:</strong> ${{ $course->discount_price }}</p>
                    <p><strong>Duration:</strong> {{ $course->duration }}</p>
                    <p><strong>Resources:</strong> {{ $course->resources }}</p>
                    <p><strong>Certificate:</strong> {{ $course->certificate }}</p>
                    <p><strong>Label:</strong> {{ $course->label }}</p>
                    <p><strong>Prerequisites:</strong> {{ $course->prerequisites }}</p>
                    <p><strong>Bestseller:</strong> {{ $course->bestseller ? 'Yes' : 'No' }}</p>
                    <p><strong>Featured:</strong> {{ $course->featured ? 'Yes' : 'No' }}</p>
                    <p><strong>Highest Rated:</strong> {{ $course->highestrated ? 'Yes' : 'No' }}</p>
                </div>

                <div class="col-md-6">
                    <p><strong>Course Image:</strong></p>
                    <img src="{{ asset($course->course_image) }}" alt="{{ $course->course_name }}" style="max-width: 300px;" onerror="this.src='{{ asset('upload/no_image.jpg') }}'">
                    <p class="mt-3"><strong>Course Video:</strong></p>
                    @if ($course->video)
                        <video width="300" height="200" controls>
                            <source src="{{ asset($course->video) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <p>No video available</p>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                <p><strong>Description:</strong></p>
                <div>{!! $course->description !!}</div>
            </div>

            <div class="mt-4">
                <p><strong>Course Goals:</strong></p>
                @forelse ($course->goals as $goal)
                    <li>{{ $goal->goal_name }}</li>
                @empty
                    <p>No goals defined for this course.</p>
                @endforelse
            </div>

            <div class="mt-4">
                <a href="{{ route('instructor.courses.index') }}" class="btn btn-secondary">Back to Courses</a>
                <a href="{{ route('instructor.course_sections.index', $course->id) }}" class="btn btn-warning">View Sections</a>
            </div>
        </div>
    </div>
</div>

@endsection