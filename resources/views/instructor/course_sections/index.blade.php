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
                    <li class="breadcrumb-item active" aria-current="page">{{ $course->course_name }} Sections</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('instructor.course_lectures.create', $course->id) }}" class="btn btn-primary">Add Sections</a>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-body">
            <h5 class="mb-4">{{ $course->course_name }} - Sections</h5>
            @forelse ($sections as $section)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>{{ $section->section_title }}</h6>
                            <div>
                                <form action="{{ route('instructor.course_sections.destroy', [$course->id, $section->id]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this section?');">Delete</button>
                                </form>
                            </div>
                        </div>
                        <ul class="list-group mt-3">
                            @forelse ($section->lectures as $lecture)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $lecture->lecture_title }}
                                    <div>
                                        <a href="{{ route('instructor.course_lectures.show', [$course->id, $lecture->id]) }}" class="btn btn-primary btn-sm">View</a>
                                        <a href="{{ route('instructor.course_lectures.edit', [$course->id, $lecture->id]) }}" class="btn btn-info btn-sm">Edit</a>
                                        <form action="{{ route('instructor.course_lectures.destroy', [$course->id, $lecture->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this lecture?');">Delete</button>
                                        </form>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item">No lectures available.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            @empty
                <p>No sections available for this course.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection