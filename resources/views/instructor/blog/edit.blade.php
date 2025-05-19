@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Blog Post</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h5 class="mb-4 text-primary">Edit Blog Post</h5>
            <form id="myForm" action="{{ route('instructor.blog.update', $post->id) }}" method="POST" class="row g-3" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group col-md-6">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" id="title" value="{{ old('title', $post->title) }}" placeholder="Enter post title">
                    @error('title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="blog_category_id" class="form-label">Category</label>
                    <select name="blog_category_id" class="form-control" id="blog_category_id">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('blog_category_id', $post->blog_category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('blog_category_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-12">
                    <label for="content" class="form-label">Content</label>
                    <textarea name="content" class="form-control" id="content" rows="5" placeholder="Write your post content...">{{ old('content', $post->content) }}</textarea>
                    @error('content')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="image" class="form-label">Image</label>
                    <input class="form-control" name="image" type="file" id="image" accept="image/*">
                    @error('image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="video" class="form-label">Video/Reel (MP4, WebM)</label>
                    <input class="form-control" name="video" type="file" id="video" accept="video/mp4,video/webm">
                    @error('video')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <img id="showImage" src="{{ $post->image ? asset('upload/blog-posts/' . $post->image) : asset('upload/no_image.jpg') }}" alt="{{ $post->title }}" class="img-fluid rounded p-1 border" style="max-width: 150px; max-height: 150px; object-fit: cover;">
                </div>

                <div class="col-md-6">
                    <video id="showVideo" class="img-fluid rounded p-1 border" style="max-width: 150px; max-height: 150px; {{ $post->video ? '' : 'display: none;' }}" controls>
                        <source src="{{ $post->video ? asset('upload/blog-posts/' . $post->video) : '' }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>

                <div class="col-md-12">
                    <div class="d-md-flex d-grid align-items-center gap-3">
                        <button type="submit" class="btn btn-primary px-4">Update Post</button>
                        <a href="{{ route('instructor.blog.index') }}" class="btn btn-secondary px-4">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    $(document).ready(function() {
        // TinyMCE initialization
        tinymce.init({
            selector: '#content',
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | removeformat | help',
            height: 400,
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });

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

        // Video preview
        $('#video').change(function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#showVideo').attr('src', e.target.result);
                    $('#showVideo').css('display', 'block');
                };
                reader.readAsDataURL(file);
            } else {
                $('#showVideo').css('display', 'none');
            }
        });
    });
</script>

@endsection