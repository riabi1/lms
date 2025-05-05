@extends('admin.layout.Admin_layout')

@section('admin')
    <div class="page-content">
        <!-- Breadcrumb -->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.coupon.index') }}">All Coupons</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Coupon Details</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <a href="{{ route('admin.coupon.index') }}" class="btn btn-sm btn-secondary">Back to Coupons</a>
            </div>
        </div>
        <!-- End Breadcrumb -->

        <div class="card">
            <div class="card-header py-3">
                <h5 class="mb-0">Coupon Details: {{ $coupon->code }}</h5>
            </div>
            <div class="card-body">
                @if (session('message'))
                    <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Coupon Code:</label>
                            <p class="mb-0">{{ $coupon->code }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Discount:</label>
                            <p class="mb-0">
                                {{ number_format($coupon->coupon_discount, 2) }}
                                {{ $coupon->discount_type === 'percentage' ? '%' : 'USD' }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Course:</label>
                            <p class="mb-0">{{ $coupon->couponable ? $coupon->couponable->course_name : 'Not Assigned' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Instructor:</label>
                            <p class="mb-0">{{ $coupon->couponable && $coupon->couponable->courseable ? $coupon->couponable->courseable->name : 'Not Assigned' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Validity:</label>
                            <p class="mb-0">{{ $coupon->coupon_validity ? \Carbon\Carbon::parse($coupon->coupon_validity)->format('d M Y') : 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Created At:</label>
                            <p class="mb-0">{{ $coupon->created_at ? $coupon->created_at->format('d M Y H:i') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('admin.coupon.index') }}" class="btn btn-primary">Back to List</a>
                </div>
            </div>
        </div>
    </div>
@endsection