@extends('admin.layout.Admin_layout')
@section('admin')

<!-- Dépendances CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
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
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $section === 'list' ? 'Instructor Management' : 'Instructor Details' }}
                    </li>
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
            <!-- Messages Flash -->
            @if (session('message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- List Section -->
            @if ($section === 'list')
                <h4 class="card-title mb-4">All Instructors</h4>
                <div class="table-responsive">
                    <table id="instructorsTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allinstructor as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->name ?? 'N/A' }}</td>
                                    <td>{{ $item->email ?? 'N/A' }}</td>
                                    <td>{{ $item->phone ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $item->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $item->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle large-checkbox" 
                                                       type="checkbox" 
                                                       id="statusToggle_{{ $item->id }}" 
                                                       data-instructor-id="{{ $item->id }}" 
                                                       {{ $item->status == 1 ? 'checked' : '' }}>
                                            </div>
                                            <a href="{{ route('admin.instructors.index', ['section' => 'show', 'id' => $item->id]) }}" 
                                               class="btn btn-sm btn-info" title="View Details">
                                                <i class="lni lni-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No instructors found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            <!-- Show Section -->
            @elseif ($section === 'show' && $instructor)
                <h4 class="card-title mb-4">Instructor Details: {{ $instructor->name ?? 'N/A' }}</h4>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <td>{{ $instructor->id }}</td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{ $instructor->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $instructor->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $instructor->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>CV</th>
                                <td>
                                    @if ($instructor->cv)
                                        <a href="{{ asset('storage/' . $instructor->cv) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bx bx-download"></i> View/Download CV
                                        </a>
                                    @else
                                        <span class="text-muted">No CV uploaded</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge {{ $instructor->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $instructor->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $instructor->created_at ? $instructor->created_at->format('d M Y, H:i') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated</th>
                                <td>{{ $instructor->updated_at ? $instructor->updated_at->format('d M Y, H:i') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3">Courses ({{ $instructor->courses->count() }})</h5>
                        @forelse ($instructor->courses as $course)
                            <p>{{ $course->course_name ?? 'N/A' }}</p>
                        @empty
                            <p class="text-muted">No courses assigned to this instructor.</p>
                        @endforelse
                    </div>
                </div>
            @else
                <div class="alert alert-warning">Instructor not found.</div>
            @endif
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
        // Initialiser DataTables uniquement pour la section 'list'
        @if ($section === 'list')
            $('#instructorsTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    "emptyTable": "No instructors available in table"
                },
                "order": [[0, 'asc']] // Tri par ID croissant par défaut
            });
        @endif

        // Gestion du toggle de statut via AJAX
        $('.status-toggle').on('change', function() {
            const $checkbox = $(this);
            const instructorId = $checkbox.data('instructor-id');
            const newStatus = $checkbox.is(':checked') ? 1 : 0;

            $checkbox.prop('disabled', true);

            $.ajax({
                url: "{{ route('admin.instructors.updateStatus') }}",
                method: "POST",
                data: {
                    instructor_id: instructorId,
                    status: newStatus,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    toastr.success(response.message);
                    const $badge = $checkbox.closest('tr').find('.badge');
                    if (newStatus === 1) {
                        $badge.removeClass('bg-danger').addClass('bg-success').text('Active');
                    } else {
                        $badge.removeClass('bg-success').addClass('bg-danger').text('Inactive');
                    }
                    $checkbox.prop('disabled', false);
                },
                error: function(xhr) {
                    toastr.error('Failed to update status: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    $checkbox.prop('checked', !newStatus); // Revenir à l'état précédent
                    $checkbox.prop('disabled', false);
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