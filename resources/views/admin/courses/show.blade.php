@extends('admin.layout.Admin_layout')
@section('admin')

<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">All Courses</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $course->course_name ?? 'Course Details' }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('admin.courses.index') }}" class="btn btn-primary">Back to Courses</a>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="container">
        <!-- Messages Flash -->
        @if (session('message'))
            <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    @if ($course->course_image && file_exists(public_path('upload/course_images/thumbnail/' . $course->course_image)))
                        <img src="{{ asset('upload/course_images/thumbnail/' . $course->course_image) }}" 
                             class="rounded-circle p-1 border" 
                             width="90" 
                             height="90" 
                             alt="{{ $course->course_name ?? 'Course Image' }}">
                    @else
                        <div class="rounded-circle p-1 border bg-light d-flex align-items-center justify-content-center" 
                             style="width: 90px; height: 90px;">
                            <span class="text-muted">No Image</span>
                        </div>
                    @endif
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mt-0">{{ $course->course_name ?? 'N/A' }}</h5>
                        <p class="mb-0">{{ $course->course_title ?? 'No title provided' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Course Information</h5>
                        <table class="table mb-0">
                            <tbody>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td>{{ $course->category->category_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Subcategory:</strong></td>
                                    <td>{{ $course->subcategory->subcategory_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Instructor:</strong></td>
                                    <td>{{ $course->courseable?->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Label:</strong></td>
                                    <td>{{ $course->label ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Video:</strong></td>
                                    <td>
                                        @if ($course->video && file_exists(public_path('upload/course_images/video/' . $course->video)))
                                            <video width="300" height="200" controls>
                                                <source src="{{ asset('upload/course_images/video/' . $course->video) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        @else
                                            <span class="text-muted">No video available</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Pricing & Status</h5>
                        <table class="table mb-0">
                            <tbody>
                                <tr>
                                    <td><strong>Certificate:</strong></td>
                                    <td>{{ $course->certificate ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Selling Price:</strong></td>
                                    <td>{{ number_format($course->selling_price ?? 0, 2) }} TND</td>
                                </tr>
                                <tr>
                                    <td><strong>Discount Price:</strong></td>
                                    <td>{{ $course->discount_price ? number_format($course->discount_price, 2) . ' TND' : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge {{ $course->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $course->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection