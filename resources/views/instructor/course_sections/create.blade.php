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
                    <li class="breadcrumb-item"><a href="{{ route('instructor.course_sections.index', $course->id) }}">{{ $course->course_name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Section</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">Add New Section for {{ $course->course_name }}</h5>

            <!-- Messages Flash -->
            @if (session('message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form id="sectionForm" action="{{ route('instructor.course_sections.store', $course->id) }}" method="POST" class="row g-3">
                @csrf
                <!-- Pas besoin de champ caché course_id, car il est dans l'URL et implicite via $course -->

                <div class="form-group col-md-12">
                    <label for="section_title" class="form-label">Section Title <span class="text-danger">*</span></label>
                    <input type="text" name="section_title" class="form-control @error('section_title') is-invalid @enderror" 
                           id="section_title" value="{{ old('section_title') }}" placeholder="Enter section title">
                    @error('section_title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-12">
                    <div class="d-md-flex d-grid align-items-center gap-3">
                        <button type="submit" class="btn btn-primary px-4">Save Section</button>
                        <a href="{{ route('instructor.course_sections.index', $course->id) }}" class="btn btn-secondary px-4">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script de validation -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#sectionForm').validate({
            rules: {
                section_title: {
                    required: true,
                    maxlength: 255
                }
            },
            messages: {
                section_title: {
                    required: "Please enter a section title",
                    maxlength: "Section title cannot exceed 255 characters"
                }
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
    });
</script>

@endsection