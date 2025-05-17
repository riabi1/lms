@extends('frontend.master')

@section('title')
    Checkout | Easy Learning
@endsection

@section('home')
    <section class="breadcrumb-area section-padding img-bg-2">
        <div class="overlay"></div>
        <div class="container">
            <div class="breadcrumb-content d-flex align-items-center justify-content-between">
                <h2 class="section__title text-white">Checkout</h2>
                <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex align-items-center">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Checkout</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="cart-area section-padding">
        <div class="container">
            @if (session('success') || session('error'))
                <div class="alert helped {{ session('success') ? 'alert-success' : 'alert-danger' }}">
                    {{ session('success') ?: session('error') }}
                </div>
            @endif

            <div class="row">
                <div class="col-lg-7">
                    <h3 class="fs-24 pb-4">Order Summary</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Course</th>
                                    <th>Instructor</th>
                                    <th class="text-right">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cartItems as $item)
                                    <tr>
                                        <td>
                                            <img 
                                                src="{{ isset($item->options['image']) && $item->options['image'] ? asset('upload/course_images/thumbnail/' . $item->options['image']) : asset('images/default-course.jpg') }}"
                                                alt="{{ e($item->cartable->course_name ?? $item->name) }}"
                                                class="lazy rounded"
                                                style="width: 60px; height: auto;"
                                                loading="lazy"
                                                onerror="this.src='{{ asset('images/default-course.jpg') }}'"
                                            >
                                        </td>
                                        <td>{{ e($item->cartable->course_name ?? $item->name) }}</td>
                                        <td>{{ e($item->cartable->courseable->name ?? (isset($item->options['instructor_name']) ? $item->options['instructor_name'] : 'Unknown')) }}</td>
                                        <td class="text-right">
                                            {{ number_format($adjustedPrices[$item->cartable_id] ?? $item->price, 2) }} USD
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="bg-gray p-4 rounded">
                        <h4 class="fs-20 pb-3">Payment Details</h4>
                        <div class="mb-3">
                            <p class="d-flex justify-content-between">
                                <span>Subtotal:</span>
                                <span>{{ number_format($subtotal, 2) }} USD</span>
                            </p>
                            @if ($couponDiscount > 0)
                                <p class="d-flex justify-content-between">
                                    <span>Coupon Discount:</span>
                                    <span>-{{ number_format($couponDiscount, 2) }} USD</span>
                                </p>
                            @endif
                            <h5 class="d-flex justify-content-between mt-2 border-top pt-2">
                                <strong>Total:</strong>
                                <strong>{{ number_format($total, 2) }} USD</strong>
                            </h5>
                        </div>

                        <ul class="nav nav-tabs mb-3" id="paymentTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="stripe-tab" data-bs-toggle="tab" data-bs-target="#stripe" type="button" role="tab" aria-controls="stripe" aria-selected="true">Stripe</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="paypal-tab" data-bs-toggle="tab" data-bs-target="#paypal" type="button" role="tab" aria-controls="paypal" aria-selected="false">PayPal</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="paymentTabContent">
                            <div class="tab-pane fade show active" id="stripe" role="tabpanel" aria-labelledby="stripe-tab">
                                <form action="{{ route('pay.stripe') }}" method="POST" id="stripe-payment-form">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="card-element" class="form-label">Credit or Debit Card</label>
                                        <p class="text-muted small mb-2">Enter your card details below to pay securely via Stripe.</p>
                                        <div id="card-element" class="form-control p-2" style="border: 1px solid #ced4da; border-radius: 4px;"></div>
                                        <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                                    </div>
                                    <button type="submit" class="btn theme-btn w-100" id="stripe-submit">
                                        <i class="la la-credit-card"></i> Pay {{ number_format($total, 2) }} USD with Stripe
                                    </button>
                                    <p class="text-muted small mt-2">Test with card: 4242 4242 4242 4242, any future date, any CVC.</p>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="paypal" role="tabpanel" aria-labelledby="paypal-tab">
                                <div>
                                    <a href="{{ route('pay.paypal') }}" class="btn theme-btn w-100">
                                        <i class="la la-paypal"></i> Pay {{ number_format($total, 2) }} USD with PayPal
                                    </a>
                                    <p class="text-muted small mt-2">You will be redirected to PayPal to complete your payment.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded');
            var stripe = Stripe('{{ env('STRIPE_KEY') }}');
            if (!stripe) {
                console.error('Stripe not initialized');
                return;
            }

            var elements = stripe.elements();
            var card = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#32325d',
                        '::placeholder': {
                            color: '#aab7c4'
                        }
                    },
                    invalid: {
                        color: '#dc3545',
                        iconColor: '#dc3545'
                    }
                },
                hidePostalCode: true
            });

            var cardElement = document.getElementById('card-element');
            if (cardElement) {
                card.mount('#card-element');
                console.log('Card element mounted');
            } else {
                console.error('Card element not found');
            }

            card.on('change', function(event) {
                var displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            var form = document.getElementById('stripe-payment-form');
            var submitButton = document.getElementById('stripe-submit');
            if (form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    submitButton.disabled = true;
                    submitButton.textContent = 'Processing...';

                    stripe.createToken(card).then(function(result) {
                        if (result.error) {
                            document.getElementById('card-errors').textContent = result.error.message;
                            submitButton.disabled = false;
                            submitButton.textContent = 'Pay {{ number_format($total, 2) }} USD with Stripe';
                        } else {
                            var tokenInput = document.createElement('input');
                            tokenInput.setAttribute('type', 'hidden');
                            tokenInput.setAttribute('name', 'stripeToken');
                            tokenInput.setAttribute('value', result.token.id);
                            form.appendChild(tokenInput);
                            form.submit();
                        }
                    });
                });
            }
        });
    </script>
@endsection