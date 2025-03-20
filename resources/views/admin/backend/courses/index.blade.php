@extends('admin.layout.Admin_layout')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
  .large-checkbox {
    transform: scale(1.5);
  }
</style>

<div class="page-content">
  <!--breadcrumb-->
  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">All Courses</li>
        </ol>
      </nav>
    </div>
   
  </div>
  <!--end breadcrumb-->

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table id="example" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr>
              <th>Sl</th>
              <th>Image</th>
              <th>Course Name</th>
              <th>Instructor</th>
              <th>Category</th>
              <th>Price</th>
              <th>Action</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($courses as $key => $item)
            <tr>
              <td>{{ $key + 1 }}</td>
              <td><img src="{{ asset('storage/upload/course_images/thumbnail/' . $item->course_image) }}" alt="" style="width: 70px; height:40px;"></td>
              <td>{{ $item->course_name }}</td>
              <td>{{ $item->instructor->name }}</td>
              <td>{{ $item->category->category_name }}</td>
              <td>{{ $item->selling_price }}</td>
              <td>
                <a href="{{ route('admin.courses.show', $item->id) }}" class="btn btn-info"><i class="lni lni-eye"></i></a>
              </td>
              <td>
                <div class="form-check-danger form-check form-switch">
                  <input class="form-check-input status-toggle large-checkbox" type="checkbox" id="flexSwitchCheckCheckedDanger{{ $item->id }}" data-course-id="{{ $item->id }}" {{ $item->status ? 'checked' : '' }}>
                  <label class="form-check-label" for="flexSwitchCheckCheckedDanger{{ $item->id }}"></label>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('.status-toggle').on('change', function() {
      let $toggle = $(this);
      let courseId = $toggle.data('course-id');
      let isChecked = $toggle.is(':checked');
      let originalState = !isChecked;

      $toggle.prop('disabled', true);

      $.ajax({
        url: "{{ route('admin.update.course.status') }}",
        method: "POST",
        data: {
          course_id: courseId,
          is_checked: isChecked ? 1 : 0,
          _token: "{{ csrf_token() }}"
        },
        success: function(response) {
          toastr.success(response.message);
          $toggle.prop('disabled', false);
        },
        error: function(xhr) {
          toastr.error('Failed to update status');
          $toggle.prop('checked', originalState);
          $toggle.prop('disabled', false);
        }
      });
    });
  });
</script>

@endsection