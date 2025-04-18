@extends('instructor.layout.instructor_layout')

@section('instructor')
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

<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">My Reports</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('instructor.reports.create') }}" class="btn btn-primary">Submit New Report</a>
        </div>
    </div>

    <!-- Reports Card -->
    <div class="card">
        <div class="card-body">
            <!-- Session Messages -->
            @if (session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
            @endif

            <!-- Reports Table -->
            <div class="table-responsive">
                <table id="reportsTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Course</th>
                            <th>Description</th>
                            <th>Status</th>
                          
                            <th>Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $index => $report)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $report->title }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $report->type)) }}</td>
                                <td>{{ $report->course?->course_title ?? 'N/A' }}</td>
                                <td>
                                    <span class="description-tooltip" data-description="{{ $report->description }}">
                                        {{ Str::limit($report->description, 50, '...') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if ($report->status === 'pending') bg-warning
                                        @elseif ($report->status === 'fixed') bg-success
                                        @else bg-danger @endif">
                                        {{ $report->status === 'pending' ? 'Pending Review' : 
                                           ($report->status === 'fixed' ? 'Resolved' : 'Not Resolved') }}
                                    </span>
                                </td>
                               
                                <td>{{ $report->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No reports found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

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
            });
        });
    </script>
@endpush
@endsection