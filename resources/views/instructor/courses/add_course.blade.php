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
                    <li class="breadcrumb-item active" aria-current="page">Add Course</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
 
    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">Add Course</h5>
            
            <form id="myForm" action="{{ route('instructor.store.course') }}" method="post" class="row g-3" enctype="multipart/form-data">
                @csrf

                <div class="form-group col-md-6">
                    <label for="course_name" class="form-label">Course Name</label>
                    <input type="text" name="course_name" class="form-control" id="course_name">
                </div>

                <div class="form-group col-md-6">
                    <label for="course_title" class="form-label">Course Title</label>
                    <input type="text" name="course_title" class="form-control" id="course_title">
                </div>

                <div class="form-group col-md-6">
                    <label for="image" class="form-label">Course Image</label>
                    <input class="form-control" name="image" type="file" id="image">
                </div>

                <div class="col-md-6"> 
                    <img id="showImage" src="{{ url('upload/no_image.jpg') }}" alt="Preview" class="rounded-circle p-1 bg-primary" width="100">  
                </div>

                <div class="form-group col-md-6">
                    <label for="video" class="form-label">Course Intro Video</label>
                    <input type="file" name="video" class="form-control" accept="video/mp4, video/webm">
                </div>

                <div class="form-group col-md-6"></div>

                <div class="form-group col-md-6">
                    <label for="category_id" class="form-label">Course Category</label>
                    <select name="category_id" id="category_id" class="form-select mb-3" aria-label="Default select example">
                        <option value="" selected disabled>Select a category</option>
                        @foreach ($categories as $cat) 
                            <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="subcategory_id" class="form-label">Course Subcategory</label>
                    <select name="subcategory_id" id="subcategory_id" class="form-select mb-3" aria-label="Default select example">
                        <option value="" selected>Select a subcategory</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="certificate" class="form-label">Certificate Available</label>
                    <select name="certificate" class="form-select mb-3" aria-label="Default select example">
                        <option value="" selected disabled>Select an option</option> 
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="label" class="form-label">Course Label</label>
                    <select name="label" class="form-select mb-3" aria-label="Default select example">
                        <option value="" selected disabled>Select an option</option> 
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="selling_price" class="form-label">Course Price</label>
                    <input type="text" name="selling_price" class="form-control" id="selling_price">
                </div>

                <div class="form-group col-md-3">
                    <label for="discount_price" class="form-label">Discount Price</label>
                    <input type="text" name="discount_price" class="form-control" id="discount_price">
                </div>

                <div class="form-group col-md-3">
                    <label for="duration" class="form-label">Duration</label>
                    <input type="text" name="duration" class="form-control" id="duration">
                </div>

                <div class="form-group col-md-3">
                    <label for="resources" class="form-label">Resources</label>
                    <input type="text" name="resources" class="form-control" id="resources">
                </div>

                <div class="form-group col-md-12">
                    <label for="prerequisites" class="form-label">Course Prerequisites</label>
                    <textarea name="prerequisites" class="form-control" id="prerequisites" placeholder="Prerequisites ..." rows="3"></textarea>
                </div>

                <div class="form-group col-md-12">
                    <label for="description" class="form-label">Course Description</label>
                    <textarea name="description" class="form-control" id="myeditorinstance"></textarea>
                </div>

                <p>Course Goals</p>
                <div class="row add_item">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="goals" class="form-label">Goals</label>
                            <input type="text" name="course_goals[]" id="goals" class="form-control" placeholder="Goals">
                        </div>
                    </div>
                    <div class="form-group col-md-6" style="padding-top: 30px;">
                        <a class="btn btn-success addeventmore"><i class="fa fa-plus-circle"></i> Add More..</a>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bestseller" value="1" id="bestseller">
                            <label class="form-check-label" for="bestseller">BestSeller</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="featured" value="1" id="featured">
                            <label class="form-check-label" for="featured">Featured</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="highestrated" value="1" id="highestrated">
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

<!-- Add Multiple Goals -->
<div style="visibility: hidden">
    <div class="whole_extra_item_add" id="whole_extra_item_add">
        <div class="whole_extra_item_delete" id="whole_extra_item_delete">
            <div class="container mt-2">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="goals">Goals</label>
                        <input type="text" name="course_goals[]" id="goals" class="form-control" placeholder="Goals">
                    </div>
                    <div class="form-group col-md-6" style="padding-top: 20px">
                        <span class="btn btn-success btn-sm addeventmore"><i class="fa fa-plus-circle">Add</i></span>
                        <span class="btn btn-danger btn-sm removeeventmore"><i class="fa fa-minus-circle">Remove</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script type="text/javascript">
    $(document).ready(function(){
        var counter = 0;
        $(document).on("click", ".addeventmore", function(){
            var whole_extra_item_add = $("#whole_extra_item_add").html();
            $(this).closest(".add_item").append(whole_extra_item_add);
            counter++;
        });
        $(document).on("click", ".removeeventmore", function(event){
            $(this).closest("#whole_extra_item_delete").remove();
            counter -= 1;
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $('select[name="category_id"]').on('change', function(){
            var category_id = $(this).val();
            console.log('Selected Category ID:', category_id); // Debug
            if (category_id) {
                $.ajax({
                    url: "{{ route('instructor.subcategory.ajax', '') }}/" + category_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        console.log('Subcategories:', data); // Debug
                        var $subcategorySelect = $('select[name="subcategory_id"]');
                        $subcategorySelect.empty();
                        $subcategorySelect.append('<option value="" selected>Select a subcategory</option>');
                        if (data.length > 0) {
                            $.each(data, function(key, value){
                                $subcategorySelect.append('<option value="' + value.id + '">' + value.subcategory_name + '</option>');
                            });
                        } else {
                            $subcategorySelect.append('<option value="">No subcategories found</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr.responseText); // Debug
                        $('select[name="subcategory_id"]').html('<option value="">Error loading subcategories</option>');
                    }
                });
            } else {
                $('select[name="subcategory_id"]').html('<option value="" selected>Select a subcategory</option>');
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $('#myForm').validate({
            rules: {
                course_name: { required: true },
                course_title: { required: true },
                category_id: { required: true },
                subcategory_id: { required: true }
            },
            messages: {
                course_name: { required: 'Please Enter Course Name' },
                course_title: { required: 'Please Enter Course Title' },
                category_id: { required: 'Please Select a Category' },
                subcategory_id: { required: 'Please Select a Subcategory' }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass){
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass){
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $('#image').change(function(e){
            var reader = new FileReader();
            reader.onload = function(e){
                $('#showImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files[0]);
        });
    });
</script>

@endsection