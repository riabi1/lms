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
                    <li class="breadcrumb-item active" aria-current="page">{{ $course->course_name }} Lectures</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('instructor.course_lectures.create', $course->id) }}" class="btn btn-primary">Add Lectures</a>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-body">
            <h5 class="mb-4">{{ $course->course_name }} - Lectures</h5>
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
                                    <a href="{{ route('instructor.course_lectures.show', [$course->id, $lecture->id]) }}" class="btn btn-primary btn-sm">View</a>
                                    <a href="{{ route('instructor.course_lectures.edit', [$course->id, $lecture->id]) }}" class="btn btn-info btn-sm">Edit</a>
                                    <form action="{{ route('instructor.course_lectures.destroy', [$course->id, $lecture->id]) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this lecture?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No lectures available for this course.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection