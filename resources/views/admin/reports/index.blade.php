@extends('admin.layout.Admin_layout')

@section('title', 'Manage Reports | Easy Learning')

@section('admin')
    <div class="page-content">
        <!-- Breadcrumb -->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">All Reports</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Reports Card -->
        <div class="card">
            <div class="card-body">
                <!-- Session Messages -->
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

                <!-- Reports Table -->
                <div class="table-responsive">
                    <table id="reportsTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Reporter</th>
                                <th>Course</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Resolution Notes</th>
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
                                    <td>
                                        <span class="description-tooltip" data-description="{{ $report->description }}">
                                            {{ \Illuminate\Support\Str::limit($report->description, 50) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            {{ $report->status == 'pending' ? 'bg-warning' : 
                                               ($report->status == 'fixed' ? 'bg-success' : 'bg-danger') }} 
                                            px-3 py-2">
                                            {{ $report->status == 'pending' ? 'Pending Review' : 
                                               ($report->status == 'fixed' ? 'Resolved' : 'Not Resolved') }}
                                        </span>
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($report->resolution_notes ?? 'N/A', 50) }}</td>
                                    <td>{{ $report->created_at->format('d M Y') }}</td>
                                    <td>
                                        <form action="{{ route('admin.reports.update', $report->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <div class="mb-2">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending Review</option>
                                                    <option value="fixed" {{ $report->status == 'fixed' ? 'selected' : '' }}>Resolved</option>
                                                    <option value="not_fixed" {{ $report->status == 'not_fixed' ? 'selected' : '' }}>Not Resolved</option>
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <textarea name="resolution_notes" class="form-control form-control-sm" rows="3" placeholder="Add resolution notes">{{ $report->resolution_notes }}</textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm px-4">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
        <style>
            .description-tooltip {
                position: relative;
                cursor: pointer;
            }
            .description-tooltip:hover::after {
                content: attr(data-description);
                position: absolute;
                background: #333;
                color: #fff;
                padding: 5px 10px;
                border-radius: 4px;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                white-space: pre-wrap;
                z-index: 1000;
                width: 200px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#reportsTable').DataTable({
                    paging: true,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    pageLength: 10,
                    language: {
                        search: "Search reports:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ reports",
                        paginate: {
                            previous: "Previous",
                            next: "Next"
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection