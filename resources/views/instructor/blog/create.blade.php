@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Create Blog Post</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">Create Blog Post</h5>
            <form id="myForm" action="{{ route('instructor.blog.store') }}" method="POST" class="row g-3" enctype="multipart/form-data">
                @csrf

                <div class="form-group col-md-6">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}" placeholder="Enter post title">
                    @error('title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="blog_category_id" class="form-label">Category</label>
                    <select name="blog_category_id" class="form-control" id="blog_category_id">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('blog_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('blog_category_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-12">
                    <label for="content" class="form-label">Content (max 500 characters)</label>
                    <textarea name="content" class="form-control" id="content" rows="3" maxlength="500" placeholder="Write a short description...">{{ old('content') }}</textarea>
                    <small class="text-muted">Remaining characters: <span id="charCount">500</span></small>
                    @error('content')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="image" class="form-label">Image</label>
                    <input class="form-control" name="image" type="file" id="image">
                    @error('image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <img id="showImage" src="{{ asset('upload/no_image.jpg') }}" alt="Preview" class="rounded-circle p-1 bg-primary" style="width: 100px; height: 100px; object-fit: cover;">
                </div>

                <div class="col-md-12">
                    <div class="d-md-flex d-grid align-items-center gap-3">
                        <button type="submit" class="btn btn-primary px-4">Save Post</button>
                        <a href="{{ route('instructor.blog.index') }}" class="btn btn-secondary px-4">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Image preview
        $('#image').change(function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#showImage').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // Character counter
        $('#content').on('input', function() {
            const remaining = 500 - $(this).val().length;
            $('#charCount').text(remaining);
        });
    });
</script>

@endsection