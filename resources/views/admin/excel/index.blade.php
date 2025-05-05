@extends('admin.layout.Admin_layout')

@section('admin')
<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb.diagram-item active" aria-current="page">Excel Reports</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <!-- Export Options -->
    <div class="card">
        <div class="card-header bg-light">
            <h4 class="card-title mb-0">Export Data</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-12 text-end">
                    <a href="{{ route('admin.excel.all.export') }}" class="btn btn-dark btn-lg">
                        <i class="bx bx-download me-1"></i> Export All Data
                    </a>
                </div>
            </div>
            <table class="table table-hover table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Data Type</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Enrollments</td>
                        <td>Details of course enrollments by users</td>
                        <td>
                            <a href="{{ route('admin.excel.enrollments.export') }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-export me-1"></i> Export
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>Payments</td>
                        <td>Total payments received for orders</td>
                        <td>
                            <a href="{{ route('admin.excel.payments.export') }}" class="btn btn-info btn-sm">
                                <i class="bx bx-export me-1"></i> Export
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>Users</td>
                        <td>List of registered users</td>
                        <td>
                            <a href="{{ route('admin.excel.users.export') }}" class="btn btn-success btn-sm">
                                <i class="bx bx-export me-1"></i> Export
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>Instructors</td>
                        <td>List of instructors</td>
                        <td>
                            <a href="{{ route('admin.excel.instructors.export') }}" class="btn btn-warning btn-sm">
                                <i class="bx bx-export me-1"></i> Export
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>Orders</td>
                        <td>Details of all orders placed</td>
                        <td>
                            <a href="{{ route('admin.excel.orders.export') }}" class="btn btn-danger btn-sm">
                                <i class="bx bx-export me-1"></i> Export
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>Courses</td>
                        <td>List of available courses</td>
                        <td>
                            <a href="{{ route('admin.excel.courses.export') }}" class="btn btn-dark btn-sm">
                                <i class="bx bx-export me-1"></i> Export
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection