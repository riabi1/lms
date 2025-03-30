@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<!-- Scripts nécessaires -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Course</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">Edit Course: {{ $course->course_name }}</h5>

            <form id="myForm" action="{{ route('instructor.courses.update', $course->id) }}" method="POST" class="row g-3" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="form-group col-md-6">
                    <label for="course_name" class="form-label">Course Name <span class="text-danger">*</span></label>
                    <input type="text" name="course_name" class="form-control @error('course_name') is-invalid @enderror" id="course_name" value="{{ old('course_name', $course->course_name) }}">
                    @error('course_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="course_title" class="form-label">Course Title <span class="text-danger">*</span></label>
                    <input type="text" name="course_title" class="form-control @error('course_title') is-invalid @enderror" id="course_title" value="{{ old('course_title', $course->course_title) }}">
                    @error('course_title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="category_id" class="form-label">Course Category <span class="text-danger">*</span></label>
                    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                        <option value="" selected disabled>Select a category</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $course->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="subcategory_id" class="form-label">Course Subcategory <span class="text-danger">*</span></label>
                    <select name="subcategory_id" id="subcategory_id" class="form-select @error('subcategory_id') is-invalid @enderror">
                        <option value="" selected>Select a subcategory</option>
                        @foreach ($subcategories as $subcat)
                            <option value="{{ $subcat->id }}" {{ old('subcategory_id', $course->subcategory_id) == $subcat->id ? 'selected' : '' }}>{{ $subcat->subcategory_name }}</option>
                        @endforeach
                    </select>
                    @error('subcategory_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="certificate" class="form-label">Certificate Available</label>
                    <select name="certificate" class="form-select @error('certificate') is-invalid @enderror">
                        <option value="" selected disabled>Select an option</option>
                        <option value="Yes" {{ old('certificate', $course->certificate) == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ old('certificate', $course->certificate) == 'No' ? 'selected' : '' }}>No</option>
                    </select>
                    @error('certificate')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="label" class="form-label">Course Label</label>
                    <select name="label" class="form-select @error('label') is-invalid @enderror">
                        <option value="" selected disabled>Select an option</option>
                        <option value="Beginner" {{ old('label', $course->label) == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="Intermediate" {{ old('label', $course->label) == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="Advanced" {{ old('label', $course->label) == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                    @error('label')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-3">
                    <label for="selling_price" class="form-label">Course Price</label>
                    <input type="number" step="0.01" name="selling_price" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" value="{{ old('selling_price', $course->selling_price) }}">
                    @error('selling_price')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-3">
                    <label for="discount_price" class="form-label">Discount Price</label>
                    <input type="number" step="0.01" name="discount_price" class="form-control @error('discount_price') is-invalid @enderror" id="discount_price" value="{{ old('discount_price', $course->discount_price) }}">
                    @error('discount_price')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-3">
                    <label for="duration" class="form-label">Duration</label>
                    <input type="text" name="duration" class="form-control @error('duration') is-invalid @enderror" id="duration" value="{{ old('duration', $course->duration) }}" placeholder="e.g., 10 hours">
                    @error('duration')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-12">
                    <label for="prerequisites" class="form-label">Course Prerequisites</label>
                    <textarea name="prerequisites" class="form-control @error('prerequisites') is-invalid @enderror" id="prerequisites" placeholder="Enter prerequisites..." rows="3">{{ old('prerequisites', $course->prerequisites) }}</textarea>
                    @error('prerequisites')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-12">
                    <label for="description" class="form-label">Course Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" id="myeditorinstance">{{ old('description', $course->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Course Image -->
                <div class="form-group col-md-6">
                    <label for="image" class="form-label">Course Image</label>
                    <input class="form-control @error('image') is-invalid @enderror" name="image" type="file" id="image" accept="image/jpeg,image/png,image/jpg">
                    @error('image')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label class="form-label">Current Image</label>
                    <img id="showImage" src="{{ $course->course_image ? asset('storage/upload/course_images/thumbnail/' . $course->course_image) : asset('upload/no_image.jpg') }}" 
                         alt="Course" class="rounded-circle p-1 bg-primary" style="width: 100px; height: 100px; object-fit: cover;">
                </div>

                <!-- Course Video -->
                <div class="form-group col-md-6">
                    <label for="video" class="form-label">Course Intro Video</label>
                    <input type="file" name="video" class="form-control @error('video') is-invalid @enderror" id="video" accept="video/mp4,video/avi,video/mov">
                    @error('video')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label class="form-label">Current Video</label>
                    @if ($course->video)
                        <video width="300" height="130" controls>
                            <source src="{{ asset('storage/upload/course_images/video/' . $course->video) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <p>No video available</p>
                    @endif
                </div>

                <!-- Course Goals -->
                <div class="form-group col-md-12">
                    <p class="mb-2">Course Goals</p>
                    <div class="row add_item">
                        @forelse ($goals as $item)
                            <div class="whole_extra_item_delete" id="whole_extra_item_delete">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="goals" class="form-label">Goal</label>
                                        <input type="text" name="course_goals[]" class="form-control" value="{{ $item->goal_name }}" placeholder="Enter a goal">
                                    </div>
                                    <div class="col-md-6" style="padding-top: 30px;">
                                        <span class="btn btn-danger btn-sm removeeventmore"><i class="fa fa-minus-circle"></i> Remove</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="whole_extra_item_delete" id="whole_extra_item_delete">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="goals" class="form-label">Goal</label>
                                        <input type="text" name="course_goals[]" class="form-control" placeholder="Enter a goal">
                                    </div>
                                    <div class="col-md-6" style="padding-top: 30px;">
                                        <span class="btn btn-danger btn-sm removeeventmore"><i class="fa fa-minus-circle"></i> Remove</span>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                        <div class="col-md-6">
                            <a class="btn btn-success addeventmore"><i class="fa fa-plus-circle"></i> Add More</a>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bestseller" value="1" id="bestseller" {{ old('bestseller', $course->bestseller) ? 'checked' : '' }}>
                            <label class="form-check-label" for="bestseller">BestSeller</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="featured" value="1" id="featured" {{ old('featured', $course->featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">Featured</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="highestrated" value="1" id="highestrated" {{ old('highestrated', $course->highestrated) ? 'checked' : '' }}>
                            <label class="form-check-label" for="highestrated">Highest Rated</label>
                        </div>
                    </div>
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

<!-- Add Multiple Goals Template -->
<div style="visibility: hidden">
    <div class="whole_extra_item_add" id="whole_extra_item_add">
        <div class="whole_extra_item_delete" id="whole_extra_item_delete">
            <div class="row mb-3">
                <div class="form-group col-md-6">
                    <label for="goals">Goal</label>
                    <input type="text" name="course_goals[]" class="form-control" placeholder="Enter a goal">
                </div>
                <div class="form-group col-md-6" style="padding-top: 30px">
                    <span class="btn btn-danger btn-sm removeeventmore"><i class="fa fa-minus-circle"></i> Remove</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script type="text/javascript">
    $(document).ready(function() {
        // Ajouter/Supprimer des objectifs
        var counter = {{ $goals->count() }};
        $(document).on("click", ".addeventmore", function() {
            var whole_extra_item_add = $("#whole_extra_item_add").html();
            $(this).closest(".add_item").append(whole_extra_item_add);
            counter++;
        });
        $(document).on("click", ".removeeventmore", function(event) {
            $(this).closest(".whole_extra_item_delete").remove();
            counter--;
        });

        // Prévisualisation de l'image
        $('#image').change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#showImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files[0]);
        });

        // Chargement initial des sous-catégories
        var initialCategoryId = $('#category_id').val();
        if (initialCategoryId) {
            $.ajax({
                url: "{{ route('instructor.subcategory.ajax', '') }}/" + initialCategoryId,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    var $subcategorySelect = $('select[name="subcategory_id"]');
                    $subcategorySelect.empty();
                    $subcategorySelect.append('<option value="" selected>Select a subcategory</option>');
                    $.each(data, function(key, value) {
                        var selected = value.id == "{{ old('subcategory_id', $course->subcategory_id) }}" ? 'selected' : '';
                        $subcategorySelect.append('<option value="' + value.id + '" ' + selected + '>' + value.subcategory_name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Initial AJAX Error:', status, error);
                }
            });
        }

        // Chargement des sous-catégories via AJAX lors du changement
        $('select[name="category_id"]').on('change', function() {
            var category_id = $(this).val();
            if (category_id) {
                $.ajax({
                    url: "{{ route('instructor.subcategory.ajax', '') }}/" + category_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        var $subcategorySelect = $('select[name="subcategory_id"]');
                        $subcategorySelect.empty();
                        $subcategorySelect.append('<option value="" selected>Select a subcategory</option>');
                        $.each(data, function(key, value) {
                            $subcategorySelect.append('<option value="' + value.id + '">' + value.subcategory_name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        $('select[name="subcategory_id"]').html('<option value="">Error loading subcategories</option>');
                    }
                });
            } else {
                $('select[name="subcategory_id"]').html('<option value="" selected>Select a subcategory</option>');
            }
        });

        // Validation du formulaire avec jQuery Validate
        $('#myForm').validate({
            rules: {
                course_name: { required: true },
                course_title: { required: true },
                category_id: { required: true },
                subcategory_id: { required: true },
                image: { accept: "image/jpeg,image/png,image/jpg" },
                video: { accept: "video/mp4,video/avi,video/mov" },
                selling_price: { number: true, min: 0 },
                discount_price: { number: true, min: 0 }
            },
            messages: {
                course_name: { required: 'Please enter the course name' },
                course_title: { required: 'Please enter the course title' },
                category_id: { required: 'Please select a category' },
                subcategory_id: { required: 'Please select a subcategory' },
                image: { accept: 'Please upload a valid image file (JPEG, PNG, JPG)' },
                video: { accept: 'Please upload a valid video file (MP4, AVI, MOV)' },
                selling_price: { number: 'Please enter a valid number', min: 'Price cannot be negative' },
                discount_price: { number: 'Please enter a valid number', min: 'Discount price cannot be negative' }
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