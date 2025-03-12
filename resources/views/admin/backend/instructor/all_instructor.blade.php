@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<!-- Add Toastr JS -->
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
                    <li class="breadcrumb-item active" aria-current="page">All Instructors</li>
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
                            <th>Instructor Name</th>
                            <th>Email</th> 
                            <th>Phone</th> 
                            <th>Status</th>  
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($allinstructor as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td> 
                            <td>{{ $item->phone ?? 'N/A' }}</td> <!-- Handle null phone -->
                            <td>
                                @if ($item->status == 1)
                                    <span class="btn btn-success">Active</span>
                                @else 
                                    <span class="btn btn-danger">Inactive</span>
                                @endif 
                            </td>
                            <td>
                                <div class="form-check-danger form-check form-switch">
                                    <input class="form-check-input status-toggle large-checkbox" 
                                           type="checkbox" 
                                           id="flexSwitchCheckCheckedDanger_{{ $item->id }}" 
                                           data-instructor-id="{{ $item->id }}" 
                                           {{ $item->status ? 'checked' : '' }}>
                                    <label class="form-check-label" for="flexSwitchCheckCheckedDanger_{{ $item->id }}"></label>
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
    $(document).ready(function(){
        $('.status-toggle').on('change', function(){
            var instructorId = $(this).data('instructor-id');
            var isChecked = $(this).is(':checked');

            $.ajax({
                url: "{{ route('admin.update.instructor.status') }}",
                method: "POST",
                data: {
                    instructor_id: instructorId,
                    status: isChecked ? 1 : 0,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error('Failed to update status. Please try again.');
                    // Revert checkbox state on error
                    $(this).prop('checked', !isChecked);
                }
            });
        });
    });
</script>

@endsection