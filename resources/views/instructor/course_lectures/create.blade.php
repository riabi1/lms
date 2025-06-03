@extends('Instructor.layout.Instructor_layout')

@section('instructor')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.course_sections.index', $course->id) }}">{{ $course->course_name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Lecture</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('instructor.course_sections.index', $course->id) }}" class="btn btn-primary px-5">Back to Sections</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">Add New Lecture to {{ $course->course_name }}</h5>

            @if (session('message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form id="lectureForm" action="{{ route('instructor.course_lectures.store', $course->id) }}" method="POST" class="row g-3" enctype="multipart/form-data">
                @csrf

                <div class="col-md-12">
                    <h6 class="mb-3">Lecture Section</h6>
                    <div class="form-group">
                        <label for="section_id" class="form-label">Section <span class="text-danger">*</span></label>
                        <select name="section_id" class="form-control @error('section_id') is-invalid @enderror" id="section_id" required>
                            <option value="" disabled {{ old('section_id') ? '' : 'selected' }}>Select a section</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}" {{ old('section_id', $section->id ?? '') == $section->id ? 'selected' : '' }}>
                                    {{ $section->section_title }}
                                </option>
                            @endforeach
                        </select>
                        @error('section_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12">
                    <h6 class="mb-3">Main Lecture Content (Visible on Platform)</h6>
                    <div class="border p-3 mb-3 bg-light rounded">
                        <div class="form-group">
                            <label for="lecture_title" class="form-label">Lecture Title <span class="text-danger">*</span></label>
                            <input type="text" name="lecture_title" class="form-control @error('lecture_title') is-invalid @enderror" 
                                   id="lecture_title" value="{{ old('lecture_title') }}" placeholder="Enter lecture title" required>
                            @error('lecture_title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="form-group mt-3">
                            <label for="video" class="form-label">Upload Main Video (MP4/WebM, max 100MB)</label>
                            <input type="file" name="video" class="form-control @error('video') is-invalid @enderror" 
                                   id="video" accept="video/mp4,video/webm">
                            @error('video')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label for="content" class="form-label">Lecture Content</label>
                            <textarea name="content" class="form-control @error('content') is-invalid @enderror" 
                                      id="content" rows="5" placeholder="Enter lecture content">{{ old('content') }}</textarea>
                            @error('content')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <h6 class="mb-3">Additional Resources (Downloadable by Students)</h6>
                    <div class="border p-3 bg-light rounded">
                        <div class="form-group mt-3">
                            <label for="file_path" class="form-label">Upload Resource File (PDF/DOC/JPG/PNG, max 20MB)</label>
                            <input type="file" name="file_path" class="form-control @error('file_path') is-invalid @enderror" 
                                   id="file_path" accept=".pdf,.doc,.docx,image/jpeg,image/png">
                            @error('file_path')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label for="additional_external_link" class="form-label">Additional Resource Link</label>
                            <input type="url" name="additional_external_link" class="form-control @error('additional_external_link') is-invalid @enderror" 
                                   id="additional_external_link" value="{{ old('additional_external_link') }}" placeholder="https://example.com/resource">
                            @error('additional_external_link')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label for="resources_description" class="form-label">Resources Description</label>
                            <textarea name="resources_description" class="form-control @error('resources_description') is-invalid @enderror" 
                                      id="resources_description" rows="4" placeholder="Describe the resources">{{ old('resources_description') }}</textarea>
                            @error('resources_description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mt-4">
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary px-4">Save Lecture</button>
                        <a href="{{ route('instructor.course_sections.index', $course->id) }}" class="btn btn-secondary px-4">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#lectureForm').validate({
        rules: {
            section_id: { required: true },
            lecture_title: { required: true, maxlength: 255 },
            external_link: { url: true },
            additional_external_link: { url: true },
            video: { accept: "video/mp4,video/webm", filesize: 104857600 },
            file_path: { accept: ".pdf,.doc,.docx,image/jpeg,image/png", filesize: 20971520 },
            resources_description: { maxlength: 1000 }
        },
        messages: {
            section_id: { required: "Please select a section" },
            lecture_title: { required: "Please enter a lecture title", maxlength: "Title cannot exceed 255 characters" },
            external_link: { url: "Please enter a valid URL" },
            additional_external_link: { url: "Please enter a valid URL" },
            video: { accept: "Only MP4 or WebM files are allowed", filesize: "File must be less than 100MB" },
            file_path: { accept: "Only PDF, DOC, JPG, or PNG files are allowed", filesize: "File must be less than 20MB" },
            resources_description: { maxlength: "Description cannot exceed 1000 characters" }
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        }
    });

    $.validator.addMethod('filesize', function(value, element, param) {
        return this.optional(element) || (element.files[0] && element.files[0].size <= param);
    });
});
</script>
@endsection