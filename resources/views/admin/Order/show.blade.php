@extends('admin.layout.Admin_layout')
@section('admin')


<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">All Orders</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Order Details</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Order #{{ $order->id }}</h4>
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">Student Information</h5>
                    <p><strong>Name:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h5 class="mb-3">Course Information</h5>
                    <p><strong>Title:</strong> {{ $order->course_title }}</p>
                    <p><strong>Instructor:</strong> {{ $order->instructor->name ?? 'N/A' }}</p> <!-- Devrait maintenant fonctionner -->
                    <p><strong>Price:</strong> ${{ number_format($order->price, 2) }}</p>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">Payment Information</h5>
                    <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
                 
                </div>
                <div class="col-md-6">
                    <h5 class="mb-3">Order Details</h5>
                    <p><strong>Order Date:</strong> {{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary px-5">Back to Orders</a>
        </div>
    </div>
</div>
@endsection