@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">Edit SubCategory</li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-4">
      <h5 class="mb-4">Edit SubCategory</h5>
      <form id="myForm" action="{{ route('admin.update.subcategory') }}" method="POST" class="row g-3">
        @csrf
        <input type="hidden" name="id" value="{{ $subcategory->id }}">

        <div class="form-group col-md-6">
          <label for="category_id" class="form-label">Category Name</label>
          <select name="category_id" class="form-select mb-3" id="category_id">
            <option value="" disabled>Open this select menu</option>
            @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ $cat->id == $subcategory->category_id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
            @endforeach
          </select>
          @error('category_id')
          <span class="text-danger">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group col-md-6">
          <label for="input1" class="form-label">SubCategory Name</label>
          <input type="text" name="subcategory_name" class="form-control" id="input1" value="{{ old('subcategory_name', $subcategory->subcategory_name) }}">
          @error('subcategory_name')
          <span class="text-danger">{{ $message }}</span>
          @enderror
        </div>

        <div class="col-md-12">
          <div class="d-md-flex d-grid align-items-center gap-3">
            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
            <a href="{{ route('admin.all.subcategory') }}" class="btn btn-secondary px-4">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#myForm').validate({
      rules: {
        category_id: {
          required: true
        },
        subcategory_name: {
          required: true,
          maxlength: 255
        }
      },
      messages: {
        category_id: {
          required: 'Please select a category'
        },
        subcategory_name: {
          required: 'Please enter a subcategory name',
          maxlength: 'Subcategory name must not exceed 255 characters'
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