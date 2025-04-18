@extends('User.layout.user_layout')

@section('title')
    My Reports | Easy Learning
@endsection

@section('userdashboard')
    <!-- Dépendances spécifiques à DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <div class="container py-4">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Reports</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <a href="{{ route('report') }}" class="btn btn-primary">Submit New Report</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success mb-3">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                @endif

                <div class="table-responsive">
                    <table id="reportsTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Course</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Submitted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $key => $report)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $report->title }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $report->type)) }}</td>
                                <td>{{ $report->course ? $report->course->course_title : 'N/A' }}</td>
                                <td>{{ Str::limit($report->description, 50) }}</td>
                                <td>
                                    <span class="badge 
                                        {{ $report->status == 'pending' ? 'bg-warning' : 
                                           ($report->status == 'fixed' ? 'bg-success' : 'bg-danger') }}">
                                        {{ $report->status == 'pending' ? 'Pending Review' : 
                                           ($report->status == 'fixed' ? 'Resolved' : 'Not Resolved') }}
                                    </span>
                                </td>
                                <td>{{ $report->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No reports found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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
                    "pageLength": 10
                });
            });
        </script>
    @endpush
@endsection