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
                    <li class="breadcrumb-item"><a href="{{ route('instructor.course_sections.index', $course->id) }}">{{ $course->course_name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $section->section_title }} Lectures</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('instructor.course_lectures.create', [$course->id, $section->id]) }}" class="btn btn-primary">Add Lecture</a>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="card">
        <div class="card-body">
            <h5 class="mb-4">{{ $course->course_name }} - Lectures in {{ $section->section_title }}</h5>

            <!-- Messages Flash -->
            @if (session('message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Lecture Title</th>
                            <th>Section</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lectures as $key => $lecture)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $lecture->lecture_title }}</td>
                                <td>{{ $lecture->section->section_title }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('instructor.course_lectures.show', [$course->id, $lecture->id]) }}" class="btn btn-sm btn-primary">View</a>
                                        <a href="{{ route('instructor.course_lectures.edit', [$course->id, $lecture->id]) }}" class="btn btn-sm btn-info">Edit</a>
                                        <form action="{{ route('instructor.course_lectures.destroy', [$course->id, $lecture->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this lecture?');">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    No lectures available for this section. 
                                    <a href="{{ route('instructor.course_lectures.create', [$course->id, $section->id]) }}" class="alert-link">Add a lecture</a> to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <a href="{{ route('instructor.course_sections.show', [$course->id, $section->id]) }}" class="btn btn-secondary">Back to Section</a>
            </div>
        </div>
    </div>
</div>

@endsection