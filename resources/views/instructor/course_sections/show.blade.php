@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3"> 
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.course_sections.index', $section->course_id) }}">{{ $section->course->course_name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $section->section_title }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('instructor.course_sections.edit', [$section->course_id, $section->id]) }}" class="btn btn-primary">Edit Section</a>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">{{ $section->section_title }}</h5>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Course:</strong> {{ $section->course->course_name }}</p>
                </div>
            </div>

            <div class="mt-4">
                <h6>Lectures:</h6>
                @forelse ($section->lectures as $lecture)
                    <div class="card mb-2">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <span>{{ $lecture->lecture_title }}</span>
                            <div>
                                <a href="{{ route('instructor.course_lectures.show', [$section->course_id, $lecture->id]) }}" class="btn btn-primary btn-sm">View</a>
                                <a href="{{ route('instructor.course_lectures.edit', [$section->course_id, $lecture->id]) }}" class="btn btn-info btn-sm">Edit</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p>No lectures available for this section.</p>
                @endforelse
            </div>

            <div class="mt-4">
                <a href="{{ route('instructor.course_sections.index', $section->course_id) }}" class="btn btn-secondary">Back to Sections</a>
                <a href="{{ route('instructor.course_lectures.create', $section->course_id) }}" class="btn btn-primary">Add Lecture</a>
            </div>
        </div>
    </div>
</div>

@endsection