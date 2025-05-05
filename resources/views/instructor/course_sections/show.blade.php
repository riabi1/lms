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
                    <li class="breadcrumb-item active" aria-current="page">{{ $section->section_title }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('instructor.course_sections.edit', [$course->id, $section->id]) }}" class="btn btn-primary">Edit Section</a>
                <form action="{{ route('instructor.course_sections.destroy', [$course->id, $section->id]) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('Are you sure you want to delete this section and all its lectures?');"
                            title="Deletes section and all associated lectures">
                        Delete Section
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">{{ $section->section_title }}</h5>

            @if (session('message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Section Details -->
            <div class="mb-4">
                <h6>Section Details</h6>
                <div class="border p-3 bg-light rounded">
                    <p><strong>Course:</strong> {{ $course->course_name }}</p>
                    @if ($section->description)
                        <p><strong>Description:</strong></p>
                        <div>{!! nl2br(e($section->description)) !!}</div>
                    @else
                        <p><strong>Description:</strong> None</p>
                    @endif
                </div>
            </div>

            <!-- Lectures -->
            <div class="mb-4">
                <h6>Lectures</h6>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Lecture Title</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($section->lectures as $key => $lecture)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $lecture->lecture_title }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('instructor.course_lectures.show', [$course->id, $lecture->id]) }}" class="btn btn-sm btn-primary">View</a>
                                            <a href="{{ route('instructor.course_lectures.edit', [$course->id, $lecture->id]) }}" class="btn btn-sm btn-info">Edit</a>
                                            <form action="{{ route('instructor.course_lectures.destroy', [$course->id, $lecture->id]) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this lecture?');"
                                                        title="Delete this lecture">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">
                                        No lectures available. 
                                        <a href="{{ route('instructor.course_lectures.create', [$course->id, $section->id]) }}" class="alert-link">Add a lecture</a> to get started.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-4">
                <a href="{{ route('instructor.course_sections.index', $course->id) }}" class="btn btn-secondary">Back to Sections</a>
                <a href="{{ route('instructor.course_lectures.create', [$course->id, $section->id]) }}" class="btn btn-primary">Add Lecture</a>
            </div>
        </div>
    </div>
</div>
@endsection