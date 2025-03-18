@extends('instructor.instructor_dashboard')
@section('instructor')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3"> 
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.course_lectures.create', $lecture->course_id) }}">Lectures</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Lecture</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('instructor.course_lectures.create', $lecture->course_id) }}" class="btn btn-primary px-5">Back</a>  
        </div>
    </div>
    <!--end breadcrumb-->
 
    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">Edit Lecture</h5>
            <form id="myForm" action="{{ route('instructor.course_lectures.update', [$lecture->course_id, $lecture->id]) }}" method="POST" class="row g-3" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <input type="hidden" name="course_id" value="{{ $lecture->course_id }}">

                <div class="form-group col-md-6">
                    <label for="lecture_title" class="form-label">Lecture Title</label>
                    <input type="text" name="lecture_title" class="form-control @error('lecture_title') is-invalid @enderror" id="lecture_title" value="{{ old('lecture_title', $lecture->lecture_title) }}">
                    @error('lecture_title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="url" class="form-label">Video URL</label>
                    <input type="text" name="url" class="form-control @error('url') is-invalid @enderror" id="url" value="{{ old('url', $lecture->url) }}">
                    @error('url')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="video" class="form-label">Upload Video (Optional)</label>
                    <input type="file" name="video" class="form-control @error('video') is-invalid @enderror" id="video" accept="video/*">
                    @if ($lecture->video)
                        <p>Current video: <a href="{{ asset($lecture->video) }}" target="_blank">View</a></p>
                    @endif
                    @error('video')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-12">
                    <label for="content" class="form-label">Lecture Content</label>
                    <textarea name="content" class="form-control @error('content') is-invalid @enderror" id="content">{{ old('content', $lecture->content) }}</textarea>
                    @error('content')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-12">
                    <div class="d-md-flex d-grid align-items-center gap-3">
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection