@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3"> 
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.course_sections.index', $lecture->course_id) }}">{{ $lecture->course->course_name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $lecture->lecture_title }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('instructor.course_lectures.edit', [$lecture->course_id, $lecture->id]) }}" class="btn btn-primary">Edit Lecture</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">{{ $lecture->lecture_title }}</h5>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Section:</strong> {{ $lecture->section->section_title }}</p>
                    <p><strong>Course:</strong> {{ $lecture->course->course_name }}</p>
                    <p><strong>Video URL:</strong> <a href="{{ $lecture->url }}" target="_blank">{{ $lecture->url }}</a></p>
                    @if ($lecture->video)
                        <p><strong>Uploaded Video:</strong> <a href="{{ asset($lecture->video) }}" target="_blank">View Video</a></p>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                <p><strong>Content:</strong></p>
                <div>{{ $lecture->content }}</div>
            </div>

            <div class="mt-4">
                <a href="{{ route('instructor.course_sections.index', $lecture->course_id) }}" class="btn btn-secondary">Back to Sections</a>
            </div>
        </div>
    </div>
</div>

@endsection