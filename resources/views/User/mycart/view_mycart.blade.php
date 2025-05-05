@extends('frontend.master')

@section('title')
    Shopping Cart | Easy Learning
@endsection

@section('home')
    <section class="breadcrumb-area section-padding img-bg-2">
        <div class="overlay"></div>
        <div class="container">
            <div class="breadcrumb-content d-flex align-items-center justify-content-between">
                <h2 class="section__title text-white">Shopping Cart</h2>
                <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex align-items-center">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Pages</li>
                    <li>Shopping Cart</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="cart-area section-padding">
        <div class="container">
            @if (session('success') || session('info') || session('error'))
                <div class="alert {{ session('success') ? 'alert-success' : (session('info') ? 'alert-info' : 'alert-danger') }}">
                    {{ session('success') ?: session('info') ?: session('error') }}
                </div>
            @endif

            <div id="cart-content">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 10%;">Image</th>
                            <th style="width: 50%;">Course Details</th>
                            <th class="text-right" style="width: 20%;">Price</th>
                            <th class="text-center" style="width: 20%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="cart-items">
                        @forelse ($cartItems as $item)
                            <tr id="cart-row-{{ $item->id }}">
                                <td class="text-center align-middle">
                                    <img src="{{ isset($item->attributes['image']) ? asset('storage/upload/course_images/thumbnail/' . $item->attributes['image']) : asset('images/no_image.jpg') }}" 
                                         alt="{{ e($item->name) }}" 
                                         class="rounded lazy" 
                                         style="width: 75px; height: auto;"
                                         loading="lazy"
                                         onerror="this.src='{{ asset('images/no_image.jpg') }}'">
                                </td>
                                <td class="align-middle">
                                    <strong>{{ e($item->name) }}</strong><br>
                                    By <a href="{{ isset($item->attributes['instructor_id']) ? route('instructor.details', $item->attributes['instructor_id']) : '#' }}">
                                        {{ e($item->attributes['instructor_name'] ?? 'Unknown Instructor') }}
                                    </a>
                                </td>
                                <td class="text-right align-middle">
                                    @if (isset($item->attributes['selling_price']) && isset($item->attributes['discount_price']) && $item->attributes['discount_price'] > 0)
                                        <del>{{ number_format($item->attributes['selling_price'], 2) }} USD</del><br>
                                        {{ number_format($item->price, 2) }} USD
                                    @else
                                        {{ number_format($item->price, 2) }} USD
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <button class="btn btn-danger btn-sm remove-from-cart" data-id="{{ $item->id }}">Remove</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Cart is empty.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($cartItems->count() > 0)
                    @php
                        $courseIds = $cartItems->pluck('id')->toArray();
                        $hasCoupons = \App\Models\Coupon::where('couponable_type', 'App\\Models\\Course')
                            ->whereIn('couponable_id', $courseIds)
                            ->where('coupon_validity', '>=', \Carbon\Carbon::today()->format('Y-m-d'))
                            ->where('status', 1)
                            ->exists();
                    @endphp

                    <div class="row pt-4">
                        <div class="col-lg-8">
                            @if ($hasCoupons)
                                <form action="{{ route('coupon.apply') }}" method="POST" id="coupon-form">
                                    @csrf
                                    <div class="input-group">
                                        <input class="form-control" type="text" name="code" placeholder="Enter coupon code" required>
                                        <button type="submit" class="btn theme-btn">Apply Coupon</button>
                                    </div>
                                    @error('code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </form>
                            @endif
                            @if (!empty($coupons))
                                <div class="mt-3" id="coupon-list">
                                    <h6>Applied Coupons:</h6>
                                    <ul class="list-unstyled">
                                        @foreach ($coupons as $coupon)
                                            <li class="d-flex justify-content-between align-items-center mb-2">
                                                <span>{{ e($coupon['code']) }} (-{{ number_format($coupon['discount_amount'], 2) }} USD)</span>
                                                <a href="{{ route('coupon.remove', $coupon['code']) }}" class="btn btn-warning btn-sm">Remove</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-4">
                            <div class="bg-gray p-4 mt-4" id="cart-summary">
                                <p>Subtotal: <span id="subtotal">{{ number_format($subtotal, 2) }} USD</span></p>
                                @if ($couponDiscount > 0)
                                    <p id="coupon-discount-container">Total Coupon Discount: <span id="coupon-discount">-{{ number_format($couponDiscount, 2) }} USD</span></p>
                                @endif
                                <h4>Total: <span id="total-price">{{ number_format($total, 2) }} USD</span></h4>
                                <a href="{{ route('checkout.create') }}" class="btn theme-btn w-100 mt-3">Checkout <i class="la la-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @parent
    <script>
    $(document).ready(function() {
        // Prevent double event binding
        $('.remove-from-cart').off('click').on('click', function(e) {
            e.preventDefault();
            var courseId = $(this).data('id');
            var row = $('#cart-row-' + courseId);
            var cartItem = $('#cart-item-' + courseId); // Header dropdown item

            $.ajax({
                url: '{{ route("cart.remove", ":id") }}'.replace(':id', courseId),
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                        return;
                    }
                    if (response.success) {
                        // Remove from cart page and header
                        row.remove();
                        if (cartItem.length) {
                            cartItem.remove();
                        }

                        // Update totals
                        $('#subtotal').text(response.subtotal + ' USD');
                        $('#total-price').text(response.totalPrice + ' USD');

                        // Handle empty cart
                        if (response.cartCount === 0) {
                            $('#cart-items').html('<tr><td colspan="4" class="text-center">Cart is empty.</td></tr>');
                            $('#cart-summary').remove();
                            $('#coupon-list').remove();
                            if ($('#cartDropdown').length) {
                                $('#cartDropdown').html(
                                    '<li class="media media-card">' +
                                    '<div class="media-body fs-15 text-center">' +
                                    '<p class="text-muted lh-18">Your cart is empty</p>' +
                                    '</div></li>' +
                                    '<li class="mt-3">' +
                                    '<a href="{{ route('cart') }}" class="btn theme-btn w-100 py-2">Go to Cart <i class="la la-arrow-right icon ml-1"></i></a>' +
                                    '</li>'
                                );
                            }
                        } else if (response.couponDiscount > 0) {
                            if (!document.getElementById('coupon-discount')) {
                                $('#subtotal').after('<p id="coupon-discount-container">Total Coupon Discount: <span id="coupon-discount">-' + response.couponDiscount + ' USD</span></p>');
                            } else {
                                $('#coupon-discount').text('-' + response.couponDiscount + ' USD');
                            }
                        } else {
                            $('#coupon-list').remove();
                            $('#coupon-discount-container').remove();
                        }

                        // Update header cart count
                        if ($('#cartQty').length) {
                            $('#cartQty').text(response.cartCount);
                        }
                        if ($('#cartSubTotal').length) {
                            $('#cartSubTotal').text('USD ' + response.subtotal);
                        }

                        // Refresh header cart dropdown if it exists
                        if ($('#cartDropdown').length) {
                            $.ajax({
                                url: '{{ route("cart") }}',
                                method: 'GET',
                                success: function(html) {
                                    var $newCart = $(html).find('#cartDropdown').html();
                                    $('#cartDropdown').html($newCart);
                                },
                                error: function(xhr) {
                                    console.error('Cart dropdown refresh error:', xhr);
                                }
                            });
                        }

                        alert(response.message);
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('An error occurred while removing the item.');
                    console.error(xhr);
                }
            });
        });
    });
    </script>
@endsection