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
                    <li class="breadcrumb-item active" aria-current="page">Edit Section</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">Edit Section: {{ $section->section_title }}</h5>
            <form action="{{ route('instructor.course_sections.update', [$section->course_id, $section->id]) }}" method="POST" class="row g-3">
                @csrf
                @method('PATCH')
                <input type="hidden" name="course_id" value="{{ $section->course_id }}">

                <div class="form-group col-md-12">
                    <label for="section_title" class="form-label">Section Title</label>
                    <input type="text" name="section_title" class="form-control @error('section_title') is-invalid @enderror" id="section_title" value="{{ old('section_title', $section->section_title) }}">
                    @error('section_title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-12">
                    <div class="d-md-flex d-grid align-items-center gap-3">
                        <button type="submit" class="btn btn-primary px-4">Update Section</button>
                        <a href="{{ route('instructor.course_sections.index', $section->course_id) }}" class="btn btn-secondary px-4">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection