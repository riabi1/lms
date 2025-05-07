@extends('Instructor.layout.Instructor_layout')

@section('instructor')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

<div class="page-content p-4">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4 px-4">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0 bg-light rounded-3 shadow-sm">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}" class="text-danger"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active text-dark" aria-current="page">Add Course</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-gradient-red text-white py-3 rounded-top-4">
                <h5 class="mb-0"><i class="bx bx-book-add me-2"></i>Add New Course</h5>
            </div>
            <div class="card-body p-4">
                @if (session('message'))
                    <div class="alert alert-{{ session('alert-type') }} alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form id="myForm" action="{{ route('instructor.courses.store') }}" method="POST" class="row g-4" enctype="multipart/form-data">
                    @csrf

                    <!-- Section 1: Course Details -->
                    <h6 class="fw-bold mb-3">Course Details</h6>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="course_name" class="form-label fw-bold">Course Name <span class="text-danger">*</span></label>
                            <input type="text" name="course_name" class="form-control shadow-sm @error('course_name') is-invalid @enderror" id="course_name" value="{{ old('course_name') }}" required>
                            @error('course_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="course_title" class="form-label fw-bold">Course Title <span class="text-danger">*</span></label>
                            <input type="text" name="course_title" class="form-control shadow-sm @error('course_title') is-invalid @enderror" id="course_title" value="{{ old('course_title') }}" required>
                            @error('course_title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-bold">Course Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select shadow-sm @error('category_id') is-invalid @enderror" required>
                                <option value="" selected disabled>Select a category</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="subcategory_id" class="form-label fw-bold">Course Subcategory <span class="text-danger">*</span></label>
                            <select name="subcategory_id" id="subcategory_id" class="form-select shadow-sm @error('subcategory_id') is-invalid @enderror" required>
                                <option value="" selected>Select a subcategory</option>
                            </select>
                            @error('subcategory_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <hr>

                    <!-- Section 2: Media Uploads -->
                    <h6 class="fw-bold mb-3">Media Uploads</h6>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="image" class="form-label fw-bold">Course Image</label>
                            <input class="form-control shadow-sm @error('image') is-invalid @enderror" name="image" type="file" id="image" accept="image/jpeg,image/png,image/jpg">
                            <small class="text-muted">Max 2MB, JPEG/PNG/JPG</small>
                            @error('image')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Image Preview</label>
                            <img id="showImage" src="{{ asset('upload/no_image.jpg') }}" alt="Preview" class="rounded-circle shadow-sm p-1 bg-light" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <div class="col-md-6">
                            <label for="video" class="form-label fw-bold">Course Intro Video</label>
                            <input type="file" name="video" class="form-control shadow-sm @error('video') is-invalid @enderror" id="video" accept="video/mp4,video/avi,video/mov">
                            <small class="text-muted">Max 100MB, MP4/AVI/MOV</small>
                            @error('video')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <hr>

                    <!-- Section 3: Course Configuration -->
                    <h6 class="fw-bold mb-3">Course Configuration</h6>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="certificate" class="form-label fw-bold">Certificate Available</label>
                            <select name="certificate" class="form-select shadow-sm @error('certificate') is-invalid @enderror">
                                <option value="" selected disabled>Select an option</option>
                                <option value="yes" {{ old('certificate') == 'yes' ? 'selected' : '' }}>Yes</option>
                                <option value="no" {{ old('certificate') == 'no' ? 'selected' : '' }}>No</option>
                            </select>
                            @error('certificate')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="label" class="form-label fw-bold">Course Level</label>
                            <select name="label" class="form-select shadow-sm @error('label') is-invalid @enderror">
                                <option value="" selected disabled>Select an option</option>
                                <option value="Beginner" {{ old('label') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="Intermediate" {{ old('label') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="Advanced" {{ old('label') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                            @error('label')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Course Goals</label>
                            <div class="row add_item">
                                <div class="col-md-6">
                                    <input type="text" name="CourseGoals[]" class="form-control shadow-sm mb-2" placeholder="Enter a goal" value="{{ old('CourseGoals.0') }}">
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-success btn-sm addeventmore"><i class="fa fa-plus-circle"></i> Add More</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <!-- Section 4: Pricing -->
                    <h6 class="fw-bold mb-3">Pricing</h6>
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <label for="selling_price" class="form-label fw-bold">Course Price</label>
                            <input type="number" name="selling_price" class="form-control shadow-sm @error('selling_price') is-invalid @enderror" id="selling_price" value="{{ old('selling_price') }}" min="0">
                            @error('selling_price')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="discount_price" class="form-label fw-bold">Discount Price</label>
                            <input type="number" name="discount_price" class="form-control shadow-sm @error('discount_price') is-invalid @enderror" id="discount_price" value="{{ old('discount_price') }}" min="0">
                            @error('discount_price')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                            <div class="col-md-3">
                            <label for="resources" class="form-label fw-bold">Resources</label>
                            <input type="text" name="resources" class="form-control shadow-sm @error('resources') is-invalid @enderror" id="resources" value="{{ old('resources') }}">
                            @error('resources')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <hr>

                    <!-- Section 5: Additional Information -->
                    <h6 class="fw-bold mb-3">Additional Information</h6>
                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <label for="prerequisites" class="form-label fw-bold">Course Prerequisites</label>
                            <textarea name="prerequisites" class="form-control shadow-sm @error('prerequisites') is-invalid @enderror" id="prerequisites" placeholder="Enter prerequisites..." rows="3">{{ old('prerequisites') }}</textarea>
                            @error('prerequisites')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="description" class="form-label fw-bold">Course Description</label>
                            <textarea name="description" class="form-control shadow-sm @error('description') is-invalid @enderror" id="description" rows="5">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <hr>

                    <!-- Submit Button -->
                    <div class="col-md-12 text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm">Save Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Template for Adding Course Goals -->
<div style="visibility: hidden">
    <div class="whole_extra_item_add" id="whole_extra_item_add">
        <div class="row whole_extra_item_delete mb-2">
            <div class="col-md-6">
                <input type="text" name="CourseGoals[]" class="form-control shadow-sm" placeholder="Enter a goal">
            </div>
            <div class="col-md-6">
                <button type="button" class="btn btn-danger btn-sm removeeventmore"><i class="fa fa-minus-circle"></i> Remove</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Add/Remove Course Goals
        $(document).on("click", ".addeventmore", function() {
            var whole_extra_item_add = $("#whole_extra_item_add").html();
            $(this).closest(".add_item").append(whole_extra_item_add);
        });
        $(document).on("click", ".removeeventmore", function() {
            $(this).closest(".whole_extra_item_delete").remove();
        });

        // Image Preview
        $('#image').on('change', function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#showImage').attr('src', e.target.result);
            };
            reader.readAsDataURL(e.target.files[0]);
        });

        // Load Subcategories via AJAX
        $('#category_id').on('change', function() {
            var category_id = $(this).val();
            var $subcategorySelect = $('#subcategory_id');
            $subcategorySelect.empty().append('<option value="" selected>Select a subcategory</option>');

            if (category_id) {
                $.ajax({
                    url: "{{ route('instructor.subcategory.ajax', '') }}/" + category_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        if (data.length > 0) {
                            $.each(data, function(key, value) {
                                $subcategorySelect.append(`<option value="${value.id}">${value.subcategory_name}</option>`);
                            });
                        } else {
                            $subcategorySelect.append('<option value="">No subcategories found</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        $subcategorySelect.append('<option value="">Error loading subcategories</option>');
                    }
                });
            }
        });

        // Form Validation
        $('#myForm').validate({
            rules: {
                course_name: { required: true },
                course_title: { required: true },
                category_id: { required: true },
                subcategory_id: { required: true },
                image: { accept: "image/jpeg,image/png,image/jpg" },
                video: { accept: "video/mp4,video/avi,video/mov" },
                selling_price: { number: true, min: 0 },
                discount_price: { 
                    number: true, 
                    min: 0,
                    lessThan: '#selling_price'
                },
                'CourseGoals[]': { maxlength: 255 }
            },
            messages: {
                course_name: 'Please enter the course name',
                course_title: 'Please enter the course title',
                category_id: 'Please select a category',
                subcategory_id: 'Please select a subcategory',
                image: 'Please upload a valid image (JPEG, PNG, JPG)',
                video: 'Please upload a valid video (MP4, AVI, MOV)',
                selling_price: { number: 'Must be a number', min: 'Cannot be negative' },
                discount_price: { 
                    number: 'Must be a number', 
                    min: 'Cannot be negative',
                    lessThan: 'Must be less than selling price'
                },
                'CourseGoals[]': 'Goal cannot exceed 255 characters'
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.col-md-3, .col-md-6, .col-md-12').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });

        // Custom Validation Method
        $.validator.addMethod('lessThan', function(value, element, param) {
            var sellingPrice = $(param).val();
            return !value || !sellingPrice || parseFloat(value) < parseFloat(sellingPrice);
        }, 'Discount price must be less than selling price');
    });
</script>
@endsection