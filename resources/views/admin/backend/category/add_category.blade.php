@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">Add Category</li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-4">
      <h5 class="mb-4">Add Category</h5>
      <form id="myForm" action="{{ route('admin.store.category') }}" method="POST" class="row g-3" enctype="multipart/form-data">
        @csrf

        <div class="form-group col-md-6">
          <label for="input1" class="form-label">Category Name</label>
          <input type="text" name="category_name" class="form-control" id="input1" value="{{ old('category_name') }}">
          @error('category_name')
          <span class="text-danger">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group col-md-6">
          <label for="image" class="form-label">Category Image</label>
          <input class="form-control" name="image" type="file" id="image">
          @error('image')
          <span class="text-danger">{{ $message }}</span>
          @enderror
        </div>

        <div class="col-md-6">
          <img id="showImage" src="{{ asset('upload/no_image.jpg') }}" alt="No Image" class="rounded-circle p-1 bg-primary" width="80">
        </div>

        <div class="col-md-12">
          <div class="d-md-flex d-grid align-items-center gap-3">
            <button type="submit" class="btn btn-primary px-4">Save Category</button>
            <a href="{{ route('admin.all.category') }}" class="btn btn-secondary px-4">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
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
  });
</script>

@endsection