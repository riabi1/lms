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
                <a href="{{ route('home') }}" class="btn theme-btn mt-4">Return to Home</a>
            </div>
        </div>
    </section>
@endsection