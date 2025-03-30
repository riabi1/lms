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
          <li class="breadcrumb-item active" aria-current="page">All Pending Reviews</li>
        </ol>
      </nav>
    </div>
    <div class="ms-auto"></div>
  </div>
  <!--end breadcrumb-->

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table id="example" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr>
              <th>Sl</th>
              <th>Course Name</th>
              <th>Username</th>
              <th>Comment</th>
              <th>Rating</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($reviews as $key => $item)
            <tr>
              <td>{{ $key + 1 }}</td>
              <td>{{ $item->reviewable && $item->reviewable_type === 'App\Models\Course' ? $item->reviewable->course_name : 'N/A' }}</td>
              <td>{{ $item->user->name ?? 'N/A' }}</td>
              <td>{{ $item->comment }}</td>
              <td>
                @if($item->rating == null)
                <i class="bx bxs-star text-secondary"></i>
                <i class="bx bxs-star text-secondary"></i>
                <i class="bx bxs-star text-secondary"></i>
                <i class="bx bxs-star text-secondary"></i>
                <i class="bx bxs-star text-secondary"></i>
                @elseif ($item->rating == 1)
                <i class="bx bxs-star text-warning"></i>
                <i class="bx bxs-star text-secondary"></i>
                <i class="bx bxs-star text-secondary"></i>
                <i class="bx bxs-star text-secondary"></i>
                <i class="bx bxs-star text-secondary"></i>
                @elseif ($item->rating == 2)
                <i class="bx bxs-star text-warning"></i>
                <i class="bx bxs-star text-warning"></i>
                <i class="bx bxs-star text-secondary"></i>
                <i class="bx bxs-star text-secondary"></i>
                <i class="bx bxs-star text-secondary"></i>
                @elseif ($item->rating == 3)
                <i class="bx bxs-star text-warning"></i>
                <i class="bx bxs-star text-warning"></i>
                <i class="bx bxs-star text-warning"></i>
                <i class="bx bxs-star text-secondary"></i>
                <i class="bx bxs-star text-secondary"></i>
                @elseif ($item->rating == 4)
                <i class="bx bxs-star text-warning"></i>
                <i class="bx bxs-star text-warning"></i>
                <i class="bx bxs-star text-warning"></i>
                <i class="bx bxs-star text-warning"></i>
                <i class="bx bxs-star text-secondary"></i>
                @elseif ($item->rating == 5)
                <i class="bx bxs-star text-warning"></i>
                <i class="bx bxs-star text-warning"></i>
                <i class="bx bxs-star text-warning"></i>
                <i class="bx bxs-star text-warning"></i>
                <i class="bx bxs-star text-warning"></i>
                @endif
              </td>
              <td>
                <div class="form-check-danger form-check form-switch">
                  <input class="form-check-input status-toggle large-checkbox" type="checkbox" id="flexSwitchCheckCheckedDanger{{ $item->id }}" data-review-id="{{ $item->id }}" {{ $item->status ? 'checked' : '' }}>
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
      var $toggle = $(this);
      var reviewId = $toggle.data('review-id');
      var isChecked = $toggle.is(':checked');
      var originalState = !isChecked;

      $toggle.prop('disabled', true);

      $.ajax({
        url: "{{ route('admin.update.review.status') }}",
        method: "POST",
        data: {
          review_id: reviewId,
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