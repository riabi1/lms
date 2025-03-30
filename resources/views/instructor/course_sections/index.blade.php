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
                    <li class="breadcrumb-item active" aria-current="page">{{ $course->course_name }} Sections</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('instructor.course_sections.create', $course->id) }}" class="btn btn-primary">Add Section</a>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="card">
        <div class="card-body">
            <h5 class="mb-4">{{ $course->course_name }} - Sections</h5>

            <!-- Messages Flash -->
            @if (session('message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @forelse ($sections as $section)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $section->section_title }}</h6>
                            <div class="btn-group">
                                <a href="{{ route('instructor.course_sections.show', [$course->id, $section->id]) }}" class="btn btn-sm btn-primary">View</a>
                                <a href="{{ route('instructor.course_sections.edit', [$course->id, $section->id]) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('instructor.course_sections.destroy', [$course->id, $section->id]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this section and all its lectures?');">Delete</button>
                                </form>
                            </div>
                        </div>

                        <!-- Liste des lectures -->
                        <ul class="list-group mt-3">
                            @forelse ($section->lectures as $lecture)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $lecture->lecture_title }}</span>
                                    <div class="btn-group">
                                        <a href="{{ route('instructor.course_lectures.show', [$course->id, $lecture->id]) }}" class="btn btn-sm btn-primary">View</a>
                                        <a href="{{ route('instructor.course_lectures.edit', [$course->id, $lecture->id]) }}" class="btn btn-sm btn-info">Edit</a>
                                        <form action="{{ route('instructor.course_lectures.destroy', [$course->id, $lecture->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this lecture?');">Delete</button>
                                        </form>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">No lectures available for this section.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            @empty
                <div class="alert alert-info" role="alert">
                    No sections available for this course. <a href="{{ route('instructor.course_sections.create', $course->id) }}" class="alert-link">Add a section</a> to get started.
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection