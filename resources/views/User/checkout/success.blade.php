@extends('frontend.master')

@section('title')
    Payment Successful | Easy Learning
@endsection

@section('home')
    <section class="cart-area section-padding">
        <div class="container">
            <div class="text-center">
                <h2 class="section__title mb-4">Payment Successful!</h2>
                <p class="fs-18 mb-3">Thank you for your purchase. Your order has been successfully processed.</p>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (isset($invoice))
                    <a href="{{ route('invoice.download', $invoice->id) }}" class="btn theme-btn mt-4">
                        <i class="fas fa-download"></i> Download Invoice ({{ $invoice->invoice_number }})
                    </a>
                @endif
                <a href="{{ route('home') }}" class="btn theme-btn mt-4">Return to Home</a>
            </div>
        </div>
    </section>
@endsection

<!-- Optional: Include Font Awesome for the download icon -->
@section('scripts')
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endsection