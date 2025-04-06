@extends('admin.layout.Admin_layout')
@section('admin')

<!-- Dépendances CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    .large-checkbox {
        transform: scale(1.5);
    }
    .course-image {
        width: 70px;
        height: 40px;
        object-fit: cover;
    }
</style>

<div class="page-content">
    <!-- Breadcrumb -->
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
    <!-- End Breadcrumb -->

    <div class="card">
        <div class="card-body">
            <!-- Messages Flash -->
            @if (session('message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table id="coursesTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Course Name</th>
                            <th>Instructor</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    @if ($item->course_image && Storage::exists('public/upload/course_images/thumbnail/' . $item->course_image))
                                        <img src="{{ asset('storage/upload/course_images/thumbnail/' . $item->course_image) }}" 
                                             alt="{{ $item->course_name }}" class="course-image">
                                    @else
                                        <span class="text-muted">No image</span>
                                    @endif
                                </td>
                                <td>{{ $item->course_name ?? 'N/A' }}</td>
                                <td>{{ $item->courseable?->name ?? 'N/A' }}</td> <!-- Updated to match course details -->
                                <td>{{ $item->category->category_name ?? 'N/A' }}</td>
                                <td>{{ number_format($item->selling_price ?? 0, 2) }} TND</td> <!-- Adjusted currency to TND -->
                                <td>
                                    <span class="badge {{ $item->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle large-checkbox" 
                                                   type="checkbox" 
                                                   id="statusToggle{{ $item->id }}" 
                                                   data-course-id="{{ $item->id }}" 
                                                   {{ $item->status ? 'checked' : '' }}>
                                        </div>
                                        <a href="{{ route('admin.courses.show', $item->id) }}" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="lni lni-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No courses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialiser DataTables
        $('#coursesTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "emptyTable": "No courses available in table"
            },
            "order": [[0, 'asc']] // Tri par numéro par défaut
        });

        // Gestion du toggle de statut via AJAX
        $('.status-toggle').on('change', function() {
            const $toggle = $(this);
            const courseId = $toggle.data('course-id');
            const isChecked = $toggle.is(':checked');
            const originalState = !isChecked;

            $toggle.prop('disabled', true);

            $.ajax({
                url: "{{ route('admin.courses.updateStatus') }}",
                method: "POST",
                data: {
                    course_id: courseId,
                    is_checked: isChecked ? 1 : 0,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    toastr.success(response.message);
                    const $badge = $toggle.closest('tr').find('.badge');
                    if (isChecked) {
                        $badge.removeClass('bg-danger').addClass('bg-success').text('Active');
                    } else {
                        $badge.removeClass('bg-success').addClass('bg-danger').text('Inactive');
                    }
                    $toggle.prop('disabled', false);
                },
                error: function(xhr) {
                    toastr.error('Failed to update status: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    $toggle.prop('checked', originalState);
                    $toggle.prop('disabled', false);
                }
            });
        });

        // Afficher les notifications Toastr depuis la session
        @if (session('message'))
            toastr.{{ session('alert-type', 'info') }}("{{ session('message') }}");
        @endif
    });
</script>

@endsection