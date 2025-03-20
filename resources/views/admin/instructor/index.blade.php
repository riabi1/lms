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
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Instructor Management</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <!-- Navigation Bar -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $section === 'list' ? 'active' : '' }}" href="{{ route('admin.instructors.index', ['section' => 'list']) }}">List</a>
        </li>
        @if ($section === 'show' && $instructor)
        <li class="nav-item">
            <a class="nav-link active" href="#">Show: {{ $instructor->name }}</a>
        </li>
        @endif
    </ul>

    <!-- Content Sections -->
    <div class="card">
        <div class="card-body">
            <!-- List Section -->
            @if ($section === 'list')
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
                            <td>{{ $item->phone ?? 'N/A' }}</td>
                            <td>
                                @if ($item->status == 1)
                                    <span class="btn btn-success">Active</span>
                                @else
                                    <span class="btn btn-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="form-check-danger form-check form-switch d-inline-block">
                                    <input class="form-check-input status-toggle large-checkbox" 
                                           type="checkbox" 
                                           id="flexSwitchCheckCheckedDanger_{{ $item->id }}" 
                                           data-instructor-id="{{ $item->id }}" 
                                           {{ $item->status ? 'checked' : '' }}>
                                    <label class="form-check-label" for="flexSwitchCheckCheckedDanger_{{ $item->id }}"></label>
                                </div>
                                <a href="{{ route('admin.instructors.index', ['section' => 'show', 'id' => $item->id]) }}" class="btn btn-info btn-sm"><i class="lni lni-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Show Section -->
            @elseif ($section === 'show' && $instructor)
            <h4 class="mb-3">Instructor Details: {{ $instructor->name }}</h4>
            <table class="table">
                <tr>
                    <th>Name</th>
                    <td>{{ $instructor->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $instructor->email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $instructor->phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @if ($instructor->status == 1)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                </tr>
            </table>
            @endif
        </div>
    </div>
</div>

<!-- Toastr Notifications -->
@if (session('message'))
<script>
    toastr.{{ session('alert-type', 'info') }}("{{ session('message') }}");
</script>
@endif

<!-- AJAX for Status Toggle -->
<script>
    $(document).ready(function(){
        $('.status-toggle').on('change', function(){
            var instructorId = $(this).data('instructor-id');
            var isChecked = $(this).is(':checked');
            var $checkbox = $(this);

            $checkbox.prop('disabled', true);

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
                    $checkbox.prop('disabled', false);
                },
                error: function(xhr) {
                    toastr.error('Failed to update status. Please try again.');
                    $checkbox.prop('checked', !isChecked);
                    $checkbox.prop('disabled', false);
                }
            });
        });
    });
</script>

@endsection