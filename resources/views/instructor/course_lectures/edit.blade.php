@extends('Instructor.layout.Instructor_layout')

@section('instructor')
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
                @method('PUT')
                <input type="hidden" name="course_id" value="{{ $lecture->course_id }}">

                <h5>Lecture Content (Visible on Platform)</h5>
                <div class="border p-3 mb-3 bg-light">
                    <div class="form-group col-md-12">
                        <label for="lecture_title" class="form-label">Lecture Title</label>
                        <input type="text" name="lecture_title" class="form-control @error('lecture_title') is-invalid @enderror" id="lecture_title" value="{{ old('lecture_title', $lecture->lecture_title) }}" required>
                        @error('lecture_title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group col-md-12 mt-3">
                        <label for="url" class="form-label">Video URL</label>
                        <input type="text" name="url" class="form-control @error('url') is-invalid @enderror" id="url" value="{{ old('url', $lecture->url) }}">
                        @error('url')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group col-md-12 mt-3">
                        <label for="video" class="form-label">Upload Main Video (MP4/WebM)</label>
                        <input type="file" name="video" class="form-control @error('video') is-invalid @enderror" id="video" accept="video/mp4,video/webm">
                        @if ($lecture->video)
                            <p>Current video: <a href="{{ asset('storage/' . $lecture->video) }}" target="_blank">View</a></p>
                        @endif
                        @error('video')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group col-md-12 mt-3">
                        <label for="content" class="form-label">Lecture Content</label>
                        <textarea name="content" class="form-control @error('content') is-invalid @enderror" id="content">{{ old('content', $lecture->content) }}</textarea>
                        @error('content')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <h5>Additional Resources (Downloadable by Students)</h5>
                <div class="border p-3 bg-light">
                    <div class="form-group col-md-12">
                        <label for="additional_video" class="form-label">Upload Additional Video (MP4/WebM)</label>
                        <input type="file" name="additional_video" class="form-control @error('additional_video') is-invalid @enderror" id="additional_video" accept="video/mp4,video/webm">
                        @if ($lecture->additional_video)
                            <p>Current additional video: <a href="{{ asset('storage/' . $lecture->additional_video) }}" target="_blank">View</a></p>
                        @endif
                        @error('additional_video')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group col-md-12 mt-3">
                        <label for="file_path" class="form-label">Upload Resource File (PDF/DOC/JPG/PNG)</label>
                        <input type="file" name="file_path" class="form-control @error('file_path') is-invalid @enderror" id="file_path" accept=".pdf,.doc,.docx,image/jpeg,image/png">
                        @if ($lecture->file_path)
                            <p>Current file: <a href="{{ asset('storage/' . $lecture->file_path) }}" target="_blank">Download</a></p>
                        @endif
                        @error('file_path')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group col-md-12 mt-3">
                        <label for="external_link" class="form-label">External Resource Link</label>
                        <input type="text" name="external_link" class="form-control @error('external_link') is-invalid @enderror" id="external_link" value="{{ old('external_link', $lecture->external_link) }}">
                        @error('external_link')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group col-md-12 mt-3">
                        <label for="resources_description" class="form-label">Resources Description</label>
                        <textarea name="resources_description" class="form-control @error('resources_description') is-invalid @enderror" id="resources_description">{{ old('resources_description', $lecture->resources_description) }}</textarea>
                        @error('resources_description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="d-md-flex d-grid align-items-center gap-3">
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
@endsection