@extends('admin.layout.Admin_layout')

@section('title', 'Manage Reports | Easy Learning')

@section('admin')
    <div class="page-content">
        <!-- Breadcrumb -->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0 bg-light rounded-3 shadow-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}" class="text-red"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active text-dark" aria-current="page">Manage Reports</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-gradient-red text-white py-3">
                <h5 class="mb-0"><i class="bx bx-error me-2"></i>Manage Reports</h5>
            </div>
            <div class="card-body p-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    @if ($reports->isEmpty())
                        <p class="text-muted text-center my-4">No reports found.</p>
                    @else
                        <table id="reportsTable" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Reporter</th>
                                    <th>Course</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Submitted At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $key => $report)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $report->title }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $report->type)) }}</td>
                                        <td>
                                            {{ $report->reporter_type == 'App\Models\User' ? $report->reporter->name : $report->reporter->instructor_name ?? 'N/A' }}
                                            <small class="text-muted">({{ $report->reporter_type == 'App\Models\User' ? 'User' : 'Instructor' }})</small>
                                        </td>
                                        <td>{{ $report->course ? $report->course->course_title : 'N/A' }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($report->description, 50) }}</td>
                                        <td>
                                            <span class="badge 
                                                {{ $report->status == 'pending' ? 'bg-warning' : 
                                                   ($report->status == 'fixed' ? 'bg-success' : 'bg-danger') }} 
                                                px-3 py-2">
                                                {{ $report->status == 'pending' ? 'Pending' : 
                                                   ($report->status == 'fixed' ? 'Resolved' : 'Not Resolved') }}
                                            </span>
                                        </td>
                                        <td>{{ $report->created_at->format('d M Y') }}</td>
                                        <td>
                                            <form action="{{ route('admin.reports.update', $report->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="fixed" {{ $report->status == 'fixed' ? 'selected' : '' }}>Resolved</option>
                                                    <option value="not_fixed" {{ $report->status == 'not_fixed' ? 'selected' : '' }}>Not Resolved</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#reportsTable').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "pageLength": 10,
                    "language": {
                        "search": "Search reports:",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ reports",
                        "paginate": {
                            "previous": "Previous",
                            "next": "Next"
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection