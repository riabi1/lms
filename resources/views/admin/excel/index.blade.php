@extends('admin.layout.Admin_layout')
@section('admin')

<style>
    /* Custom Pastel Colors */
    .card-header-pastel {
        background-color: #e0e7ff; /* Soft Lavender */
        color: #4b5563; /* Dark Gray for contrast */
    }
    .btn-pastel {
        background-color: #B0DB9C; /* Mint Green */
        border-color: #B0DB9C; /* Slightly darker mint */
        color: #332D56; /* Dark Teal for contrast */
    }
    .btn-pastel:hover {
        background-color: #B0DB9C; /* Lighter Mint on hover */
        border-color: #B0DB9C;
        color: #064e3b;
    }
    .alert-pastel {
        background-color: #fef3f2; /* Pale Pink */
        border-color: #B0DB9C; /* Soft Pink */
        color: #831843; /* Dark Pink for text */
    }
    .btn-outline-pastel {
        border-color: #c7d2fe; /* Soft Blue */
        color: #4b5563; /* Dark Gray */
    }
    .btn-outline-pastel:hover {
        background-color: #e0e7ff; /* Soft Lavender */
        border-color: #a5b4fc;
        color: #312e81; /* Dark Indigo */
    }
</style>

<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                            <i class="bx bx-home-alt me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Excel Reports</li>
                </ol>
            </nav>
        </div>

    </div>

    <!-- Main Card -->
    <div class="card shadow-sm border-0">
        <div class="card-header card-header-pastel d-flex align-items-center">
            <i class="bx bx-file me-2"></i>
            <h5 class="mb-0">Export Reports</h5>
        </div>
        <div class="card-body p-4">
            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-pastel alert-dismissible fade show" role="alert">
                    <i class="bx bx-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Buttons Grid -->
            <div class="table-responsive">
                <div class="row g-3">
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ route('admin.excel.users') }}" class="btn btn-pastel w-100 d-flex align-items-center justify-content-start" data-bs-toggle="tooltip" title="Export all registered users">
                            <i class="bx bx-user me-2"></i> Export Users
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ route('admin.excel.instructors') }}" class="btn btn-pastel w-100 d-flex align-items-center justify-content-start" data-bs-toggle="tooltip" title="Export all instructors">
                            <i class="bx bx-chalkboard me-2"></i> Export Instructors
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ route('admin.excel.orders') }}" class="btn btn-pastel w-100 d-flex align-items-center justify-content-start" data-bs-toggle="tooltip" title="Export all orders">
                            <i class="bx bx-cart me-2"></i> Export Orders
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ route('admin.excel.courses') }}" class="btn btn-pastel w-100 d-flex align-items-center justify-content-start" data-bs-toggle="tooltip" title="Export all courses">
                            <i class="bx bx-book me-2"></i> Export Courses
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ route('admin.excel.enrollments') }}" class="btn btn-pastel w-100 d-flex align-items-center justify-content-start" data-bs-toggle="tooltip" title="Export all enrollments">
                            <i class="bx bx-group me-2"></i> Export Enrollments
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ route('admin.excel.payments') }}" class="btn btn-pastel w-100 d-flex align-items-center justify-content-start" data-bs-toggle="tooltip" title="Export all payments">
                            <i class="bx bx-credit-card me-2"></i> Export Payments
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ route('admin.excel.blog_posts') }}" class="btn btn-pastel w-100 d-flex align-items-center justify-content-start" data-bs-toggle="tooltip" title="Export all blog posts">
                            <i class="bx bx-news me-2"></i> Export Blog Posts
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ route('admin.excel.blog_categories') }}" class="btn btn-pastel w-100 d-flex align-items-center justify-content-start" data-bs-toggle="tooltip" title="Export all blog categories">
                            <i class="bx bx-category me-2"></i> Export Blog Categories
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ route('admin.excel.coupons') }}" class="btn btn-pastel w-100 d-flex align-items-center justify-content-start" data-bs-toggle="tooltip" title="Export all coupons">
                            <i class="bx bx-gift me-2"></i> Export Coupons
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Initialize Tooltips -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

@endsection