@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<div class="page-content">
    <!-- Breadcrumb -->
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
    <!-- End Breadcrumb -->

    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">{{ $course->course_name }}</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <p><strong>Course Title:</strong> {{ $course->course_title }}</p>
                        <p><strong>Category:</strong> {{ $course->category ? $course->category->category_name : 'No Category' }}</p>
                        <p><strong>Subcategory:</strong> {{ $course->subcategory ? $course->subcategory->subcategory_name : 'No Subcategory' }}</p>
                        <p><strong>Price:</strong> {{ $course->selling_price ? number_format($course->selling_price, 2) . ' TND' : 'N/A' }}</p>
                        <p><strong>Discount Price:</strong> {{ $course->discount_price ? number_format($course->discount_price, 2) . ' TND' : 'N/A' }}</p>
                        <p><strong>Certificate:</strong> {{ $course->certificate ?? 'N/A' }}</p>
                        <p><strong>Label:</strong> {{ $course->label ?? 'N/A' }}</p>
                        <p><strong>Prerequisites:</strong> {{ $course->prerequisites ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <p><strong>Course Image:</strong></p>
                        <img src="{{ $course->course_image ? asset('upload/course_images/thumbnail/' . $course->course_image) : asset('upload/no_image.jpg') }}" 
                             alt="{{ $course->course_name }}" 
                             style="max-width: 300px; height: auto;" 
                             class="img-fluid rounded">
                    </div>
                    
                    <div class="mb-3">
                        <p><strong>Course Video:</strong></p>
                        @if ($course->video)
                            <video width="300" height="200" controls class="img-fluid rounded">
                                <source src="{{ asset('upload/course_images/video/' . $course->video) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <p class="text-muted">No video available</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <p><strong>Description:</strong></p>
                <div class="border p-3 rounded bg-light">
                    {!! $course->description ?? '<p class="text-muted">No description available</p>' !!}
                </div>
            </div>

            <div class="mt-4">
                <p><strong>Course Goals:</strong></p>
                @forelse ($course->goals as $goal)
                    <ul class="list-unstyled">
                        <li><i class="bx bx-check text-success"></i> {{ $goal->goal_name }}</li>
                    </ul>
                @empty
                    <p class="text-muted">No goals defined for this course.</p>
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