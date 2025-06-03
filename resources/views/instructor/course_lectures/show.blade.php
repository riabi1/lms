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
                    <li class="breadcrumb-item"><a href="{{ route('instructor.course_sections.show', [$course->id, $lecture->section_id]) }}">{{ $course->course_name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $lecture->lecture_title }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('instructor.course_lectures.edit', [$course->id, $lecture->id]) }}" class="btn btn-primary">Edit Lecture</a>
                <form action="{{ route('instructor.course_lectures.destroy', [$course->id, $lecture->id]) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this lecture?');">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">{{ $lecture->lecture_title }}</h5>

            @if (session('message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Lecture Details -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Course:</strong> {{ $course->course_name }}</p>
                    <p><strong>Section:</strong> {{ $lecture->section->section_title }}</p>
                    @if ($lecture->url)
                        <p><strong>Video URL:</strong> <a href="{{ $lecture->url }}" target="_blank" rel="noopener noreferrer">{{ $lecture->url }}</a></p>
                    @endif
                </div>
                <div class="col-md-6">
                    @if ($lecture->video)
                        <p><strong>Uploaded Video:</strong></p>
                        <video width="100%" height="200" controls preload="metadata" class="rounded">
                            <source src="{{ asset('upload/lectures/videos/' . $lecture->video) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <p><strong>Uploaded Video:</strong> None</p>
                    @endif
                </div>
            </div>

            <!-- Lecture Content -->
            @if ($lecture->content)
                <div class="mb-4">
                    <h6>Lecture Content</h6>
                    <div class="border p-3 bg-light rounded">{!! nl2br(e($lecture->content)) !!}</div>
                </div>
            @endif

            <!-- Additional Resources -->
            <div class="mb-4">
                <h6>Additional Resources</h6>
                @if ($lecture->additional_video || $lecture->file_path ||$lecture->resources_description)
                    <div class="border p-3 bg-light rounded">
                        @if ($lecture->additional_video)
                            <p><strong>Additional Video:</strong></p>
                            <video width="100%" height="200" controls preload="metadata" class="rounded">
                                <source src="{{ asset('upload/lectures/videos/' . $lecture->additional_video) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @endif
                        @if ($lecture->file_path)
                            <p><strong>Resource File:</strong> <a href="{{ asset('upload/lectures/files/' . $lecture->file_path) }}" target="_blank" rel="noopener noreferrer">Download File</a></p>
                        @endif

                        @if ($lecture->resources_description)
                            <p><strong>Resources Description:</strong></p>
                            <div>{!! nl2br(e($lecture->resources_description)) !!}</div>
                        @endif
                    </div>
                @else
                    <p class="text-muted">No additional resources available.</p>
                @endif
            </div>

            <!-- Actions -->
            <div class="mt-4">
                <a href="{{ route('instructor.course_sections.show', [$course->id, $lecture->section_id]) }}" class="btn btn-secondary">Back to Section</a>
            </div>
        </div>
    </div>
</div>
@endsection