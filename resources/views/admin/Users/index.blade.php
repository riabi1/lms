@extends('admin.layout.Admin_layout')
@section('admin')

<div class="page-content">
    <!-- Breadcrumb with a modern touch -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0 bg-light rounded-3 shadow-sm">
                    <li class="breadcrumb-item">
                        <a href="javascript:;" class="text-primary"><i class="bx bx-home-alt"></i> Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active text-dark" aria-current="page">All Users</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Card with a header and subtle shadow -->
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h5 class="mb-0"><i class="bx bx-users me-2"></i>Users Overview</h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-hover table-bordered align-middle" style="width:100%">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="py-3">#</th>
                            <th class="py-3">Name</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Phone</th>
                            <th class="py-3">Address</th>
                            <th class="py-3">Registration Date</th>
                            <th class="py-3">Courses Purchased</th>
                            <th class="py-3">Preferences</th>
                            <th class="py-3">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $key => $user)
                        <tr>
                            <td class="fw-bold text-center">{{ $key + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm rounded-circle bg-primary text-white me-2">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                    {{ $user->name }}
                                </div>
                            </td>
                            <td>
                                <a href="mailto:{{ $user->email }}" class="text-primary text-decoration-none">
                                    {{ $user->email }}
                                </a>
                            </td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>{{ $user->address ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-success-subtle text-success">
                                    {{ $user->created_at->format('d M Y, H:i') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info">
                                    {{ $user->orders_count }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $preferenceIds = json_decode($user->preference ?? '[]', true);
                                    $preferenceNames = collect($preferenceIds)->map(function($id) use ($categories) {
                                        return $categories[$id]->category_name ?? 'Unknown';
                                    })->implode(', ');
                                @endphp
                                <span class="badge bg-warning-subtle text-warning">
                                    {{ $preferenceNames ?: 'None' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary">
                                    {{ $user->grade ?? 'N/A' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bx bx-info-circle me-2"></i>No users found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light text-muted text-center py-3">
            Total Users: {{ $users->count() }}
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Destroy existing DataTable instance if it exists
            if ($.fn.DataTable.isDataTable('#example')) {
                $('#example').DataTable().destroy();
            }
            
            // Initialize DataTable
            $('#example').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "lengthMenu": [5, 10, 25, 50],
                "pageLength": 10
            });
        });
    </script>
@endpush

@endsection