@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<!-- Scripts pour validation -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.course_sections.show', [$course->id, $lecture->section_id]) }}">{{ $course->course_name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Lecture</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('instructor.course_sections.show', [$course->id, $lecture->section_id]) }}" class="btn btn-primary px-5">Back to Section</a>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">Edit Lecture: {{ $lecture->lecture_title }}</h5>

            <!-- Messages Flash -->
            @if (session('message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form id="lectureForm" action="{{ route('instructor.course_lectures.update', [$course->id, $lecture->id]) }}" method="POST" class="row g-3" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <!-- Pas besoin de course_id en champ caché, car il est implicite via $course -->

                <div class="form-group col-md-12">
                    <label for="section_id" class="form-label">Section <span class="text-danger">*</span></label>
                    <select name="section_id" class="form-control @error('section_id') is-invalid @enderror" id="section_id" required>
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}" {{ old('section_id', $lecture->section_id) == $section->id ? 'selected' : '' }}>
                                {{ $section->section_title }}
                            </option>
                        @endforeach
                    </select>
                    @error('section_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <h5>Lecture Content (Visible on Platform)</h5>
                <div class="border p-3 mb-3 bg-light">
                    <div class="form-group col-md-12">
                        <label for="lecture_title" class="form-label">Lecture Title <span class="text-danger">*</span></label>
                        <input type="text" name="lecture_title" class="form-control @error('lecture_title') is-invalid @enderror" 
                               id="lecture_title" value="{{ old('lecture_title', $lecture->lecture_title) }}" placeholder="Enter lecture title" required>
                        @error('lecture_title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group col-md-12 mt-3">
                        <label for="url" class="form-label">Video URL</label>
                        <input type="url" name="url" class="form-control @error('url') is-invalid @enderror" 
                               id="url" value="{{ old('url', $lecture->url) }}" placeholder="https://example.com/video">
                        @error('url')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group col-md-12 mt-3">
                        <label for="video" class="form-label">Upload Main Video (MP4/WebM, max 100MB)</label>
                        <input type="file" name="video" class="form-control @error('video') is-invalid @enderror" 
                               id="video" accept="video/mp4,video/webm">
                        @if ($lecture->video)
                            <p class="mt-2">Current video: <a href="{{ asset('storage/' . $lecture->video) }}" target="_blank">View</a></p>
                        @endif
                        @error('video')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group col-md-12 mt-3">
                        <label for="content" class="form-label">Lecture Content</label>
                        <textarea name="content" class="form-control @error('content') is-invalid @enderror" 
                                  id="content" placeholder="Enter lecture content">{{ old('content', $lecture->content) }}</textarea>
                        @error('content')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <h5>Additional Resources (Downloadable by Students)</h5>
                <div class="border p-3 bg-light">
                    <div class="form-group col-md-12">
                        <label for="additional_video" class="form-label">Upload Additional Video (MP4/WebM, max 100MB)</label>
                        <input type="file" name="additional_video" class="form-control @error('additional_video') is-invalid @enderror" 
                               id="additional_video" accept="video/mp4,video/webm">
                        @if ($lecture->additional_video)
                            <p class="mt-2">Current additional video: <a href="{{ asset('storage/' . $lecture->additional_video) }}" target="_blank">View</a></p>
                        @endif
                        @error('additional_video')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group col-md-12 mt-3">
                        <label for="file_path" class="form-label">Upload Resource File (PDF/DOC/JPG/PNG, max 20MB)</label>
                        <input type="file" name="file_path" class="form-control @error('file_path') is-invalid @enderror" 
                               id="file_path" accept=".pdf,.doc,.docx,image/jpeg,image/png">
                        @if ($lecture->file_path)
                            <p class="mt-2">Current file: <a href="{{ asset('storage/' . $lecture->file_path) }}" target="_blank">Download</a></p>
                        @endif
                        @error('file_path')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group col-md-12 mt-3">
                        <label for="external_link" class="form-label">External Resource Link</label>
                        <input type="url" name="external_link" class="form-control @error('external_link') is-invalid @enderror" 
                               id="external_link" value="{{ old('external_link', $lecture->external_link) }}" placeholder="https://example.com/resource">
                        @error('external_link')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group col-md-12 mt-3">
                        <label for="resources_description" class="form-label">Resources Description</label>
                        <textarea name="resources_description" class="form-control @error('resources_description') is-invalid @enderror" 
                                  id="resources_description" placeholder="Describe the resources">{{ old('resources_description', $lecture->resources_description) }}</textarea>
                        @error('resources_description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="d-md-flex d-grid align-items-center gap-3">
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                        <a href="{{ route('instructor.course_sections.show', [$course->id, $lecture->section_id]) }}" class="btn btn-secondary px-4">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script de validation -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#lectureForm').validate({
            rules: {
                section_id: { required: true },
                lecture_title: { required: true, maxlength: 255 },
                url: { url: true },
                video: { accept: "video/mp4,video/webm", filesize: 104857600 }, // 100MB
                additional_video: { accept: "video/mp4,video/webm", filesize: 104857600 }, // 100MB
                file_path: { accept: ".pdf,.doc,.docx,image/jpeg,image/png", filesize: 20971520 }, // 20MB
                external_link: { url: true },
                resources_description: { maxlength: 1000 }
            },
            messages: {
                section_id: { required: "Please select a section" },
                lecture_title: { required: "Please enter a lecture title", maxlength: "Title cannot exceed 255 characters" },
                url: { url: "Please enter a valid URL" },
                video: { accept: "Only MP4 or WebM files are allowed", filesize: "File must be less than 100MB" },
                additional_video: { accept: "Only MP4 or WebM files are allowed", filesize: "File must be less than 100MB" },
                file_path: { accept: "Only PDF, DOC, JPG, or PNG files are allowed", filesize: "File must be less than 20MB" },
                external_link: { url: "Please enter a valid URL" },
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

        // Validation personnalisée pour la taille des fichiers
        $.validator.addMethod('filesize', function(value, element, param) {
            return this.optional(element) || (element.files[0] && element.files[0].size <= param);
        });
    });
</script>

@endsection