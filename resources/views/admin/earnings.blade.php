@extends('admin.layout.Admin_layout')
@section('admin')

<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Earnings</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Total Earnings Card -->
    <div class="card mb-4">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <h5 class="card-title">Total Earnings</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Total Earnings</th>
                            <th>Currency</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ number_format($totalEarnings, 2) }}</td>
                            <td>USD</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Earnings by Instructor Card -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Earnings by Instructor</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Instructor Name</th>
                            <th>Email</th>
                            <th>Total Earnings</th>
                            <th>Currency</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($earningsByInstructor as $key => $earning)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $earning->name }}</td>
                                <td>{{ $earning->email }}</td>
                                <td>{{ number_format($earning->total_earnings, 2) }}</td>
                                <td>USD</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No earnings data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection