@extends('admin.layout.Admin_layout')
@section('admin')

<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">All Orders</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Order #{{ $order->id }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <!-- Actions supplémentaires si nécessaires -->
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Order #{{ $order->id }}</h4>

            <!-- Messages Flash -->
            @if (session('message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">Student Information</h5>
                    <p><strong>Name:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h5 class="mb-3">Course Information</h5>
                    <p><strong>Title:</strong> {{ $order->course_title ?? 'N/A' }}</p>
                    <p><strong>Instructor:</strong> {{ $order->instructor->name ?? 'N/A' }}</p>
                    <p><strong>Price:</strong> ${{ number_format($order->price ?? 0, 2) }}</p>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">Payment Information</h5>
                    <p>
                        <strong>Payment Status:</strong> 
                        <span class="badge {{ $order->payment_status === 'completed' ? 'bg-success' : ($order->payment_status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                            {{ ucfirst($order->payment_status ?? 'N/A') }}
                        </span>
                    </p>
                    <!-- Ajouter d'autres détails de paiement si disponibles, ex. méthode ou ID de transaction -->
                </div>
                <div class="col-md-6">
                    <h5 class="mb-3">Order Details</h5>
                    <p><strong>Order Date:</strong> {{ $order->created_at ? $order->created_at->format('d M Y, H:i') : 'N/A' }}</p>
                    <p><strong>Last Updated:</strong> {{ $order->updated_at ? $order->updated_at->format('d M Y, H:i') : 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary px-5">Back to Orders</a>
                <!-- Ajouter des actions supplémentaires si nécessaire -->
            </div>
        </div>
    </div>
</div>

@endsection