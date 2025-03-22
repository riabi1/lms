@extends('frontend.master')

@section('home')
<!-- ================================
    START BREADCRUMB AREA
================================= -->
<section class="breadcrumb-area section-padding img-bg-2">
    <div class="overlay"></div>
    <div class="container">
        <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
            <div class="section-heading">
                <h2 class="section__title text-white">Checkout</h2>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>Pages</li>
                <li>Checkout</li>
            </ul>
        </div><!-- end breadcrumb-content -->
    </div><!-- end container -->
</section><!-- end breadcrumb-area -->
<!-- ================================
    END BREADCRUMB AREA
================================= -->

<!-- ================================
    START CART AREA
================================= -->
<section class="cart-area section--padding">
    <div class="container">
        @if (session('success') || session('info') || session('error'))
            <div class="alert {{ session('success') ? 'alert-success' : (session('info') ? 'alert-info' : 'alert-danger') }}">
                {{ session('success') ?: session('info') ?: session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-lg-7">
                <div class="card card-item">
                    <div class="card-body">
                        <h3 class="card-title fs-22 pb-3">Billing Details</h3>
                        <div class="divider"><span></span></div>
                        <form method="POST" id="checkoutForm" action="{{ route('checkout.process') }}">
                            @csrf
                            <input type="hidden" name="payment_method" value="stripe">

                            <div class="input-box col-lg-6">
                                <label class="label-text">First Name</label>
                                <div class="form-group">
                                    <input class="form-control form--control" type="text" name="name" value="{{ Auth::user()->name }}" required>
                                    <span class="la la-user input-icon"></span>
                                </div>
                            </div><!-- end input-box -->
                            <div class="input-box col-lg-6">
                                <label class="label-text">Email</label>
                                <div class="form-group">
                                    <input class="form-control form--control" type="email" name="email" value="{{ Auth::user()->email }}" required>
                                    <span class="la la-envelope input-icon"></span>
                                </div>
                            </div><!-- end input-box -->
                            <div class="input-box col-lg-12">
                                <label class="label-text">Address</label>
                                <div class="form-group">
                                    <input class="form-control form--control" type="text" name="address" value="{{ Auth::user()->address ?? '' }}">
                                    <span class="la la-map-marker input-icon"></span>
                                </div>
                            </div><!-- end input-box -->
                            <div class="input-box col-lg-12">
                                <label class="label-text">Phone Number</label>
                                <div class="form-group">
                                    <input id="phone" class="form-control form--control" type="tel" name="phone" value="{{ Auth::user()->phone ?? '' }}">
                                    <span class="la la-phone input-icon"></span>
                                </div>
                            </div><!-- end input-box -->
                    </div><!-- end card-body -->
                </div><!-- end card -->

                <div class="card card-item">
                    <div class="card-body">
                        <h3 class="card-title fs-22 pb-3">Payment Method</h3>
                        <div class="divider"><span></span></div>
                        <div class="input-box">
                            <label class="label-text">Credit or Debit Card (Stripe)</label>
                            <p class="fs-14 text-muted mb-3">Enter your card details below. Required fields include: Card Number, Expiration Date (MM/YY), and CVC (3-4 digits on the back of your card).</p>
                            <div class="form-group">
                                <div id="card-element" class="form-control form--control" style="padding: 10px; border: 1px solid #e5e7eb; border-radius: 4px;"></div>
                                <div id="card-errors" class="text-danger mt-2" role="alert"></div>
                            </div>
                            <p class="fs-12 text-muted mt-2">Example: Card Number: 4242 4242 4242 4242, Exp: 12/25, CVC: 123</p>
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col-lg-7 -->

            <div class="col-lg-5">
                <div class="card card-item">
                    <div class="card-body">
                        <h3 class="card-title fs-22 pb-3">Order Details</h3>
                        <div class="divider"><span></span></div>
                        <div class="order-details-lists">
                            @forelse ($cart as $id => $item)
                                <input type="hidden" name="course_id[]" value="{{ $id }}">
                                <input type="hidden" name="course_title[]" value="{{ $item['name'] }}">
                                <input type="hidden" name="price[]" value="{{ $item['price'] }}">
                                <input type="hidden" name="instructor_id[]" value="{{ $item['instructor_id'] ?? '' }}">
                                <input type="hidden" name="adjusted_price[]" value="{{ $adjustedPrices[$id] ?? $item['price'] }}">

                                <div class="media media-card border-bottom border-bottom-gray pb-3 mb-3">
                                    <a href="{{ url('course/details/'.$id.'/'.Str::slug($item['name'] ?? 'course')) }}" class="media-img">
                                        <img src="{{ !empty($item['image']) ? Storage::url('upload/course_images/thumbnail/' . $item['image']) : url('upload/no_image.jpg') }}" 
                                             alt="{{ $item['name'] }}" 
                                             style="width: 60px; height: auto;">
                                    </a>
                                    <div class="media-body">
                                        <h5 class="fs-15 pb-2"><a href="{{ url('course/details/'.$id.'/'.Str::slug($item['name'] ?? 'course')) }}">{{ $item['name'] }}</a></h5>
                                        <p class="text-black font-weight-semi-bold lh-18">${{ number_format($adjustedPrices[$id] ?? $item['price'], 2) }}</p>
                                    </div>
                                </div><!-- end media -->
                            @empty
                                <p class="text-muted">No items in cart.</p>
                            @endforelse
                        </div><!-- end order-details-lists -->
                        <a href="{{ route('cart') }}" class="btn-text"><i class="la la-edit mr-1"></i>Edit Cart</a>
                    </div><!-- end card-body -->
                </div><!-- end card -->

                <div class="card card-item">
                    <div class="card-body">
                        <h3 class="card-title fs-22 pb-3">Order Summary</h3>
                        <div class="divider"><span></span></div>
                        <ul class="generic-list-item generic-list-item-flash fs-15">
                            <li class="d-flex align-items-center justify-content-between font-weight-semi-bold">
                                <span class="text-black">Subtotal:</span>
                                <span>${{ number_format($subtotal, 2) }}</span>
                            </li>
                            @if (!empty($coupons))
                                @foreach ($coupons as $coupon)
                                    <li class="d-flex align-items-center justify-content-between">
                                        <span class="text-black">Coupon ({{ $coupon['coupon_name'] }}):</span>
                                        <span>-${{ number_format($coupon['discount_amount'], 2) }}</span>
                                    </li>
                                @endforeach
                                <li class="d-flex align-items-center justify-content-between font-weight-semi-bold">
                                    <span class="text-black">Total Discount:</span>
                                    <span>-${{ number_format($couponDiscount, 2) }}</span>
                                </li>
                            @endif
                            <li class="d-flex align-items-center justify-content-between font-weight-bold">
                                <span class="text-black">Total:</span>
                                <span>${{ number_format($total, 2) }}</span>
                            </li>
                        </ul>
                        <input type="hidden" name="total" value="{{ $total }}">

                        <div class="btn-box border-top border-top-gray pt-3">
                            <p class="fs-14 lh-22 mb-2">Aduca is required by law to collect applicable transaction taxes for purchases made in certain tax jurisdictions.</p>
                            <p class="fs-14 lh-22 mb-3">By completing your purchase you agree to these <a href="#" class="text-color hover-underline">Terms of Service.</a></p>
                            <button type="submit" class="btn theme-btn w-100">Pay with Stripe <i class="la la-arrow-right icon ml-1"></i></button>
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col-lg-5 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section>
<!-- ================================
    END CART AREA
================================= -->

<!-- Script pour Stripe -->
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stripe = Stripe('{{ env('STRIPE_KEY') }}');
        const elements = stripe.elements();
        const card = elements.create('card', {
            style: {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': { color: '#aab7c4' }
                },
                invalid: { color: '#fa755a', iconColor: '#fa755a' }
            }
        });
        card.mount('#card-element');

        const form = document.getElementById('checkoutForm');
        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            const { token, error } = await stripe.createToken(card);
            if (error) {
                document.getElementById('card-errors').textContent = error.message;
            } else {
                const hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);
                form.submit();
            }
        });
    });
</script>
@endsection