@extends('frontend.master')
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
                        @forelse ($cart as $id => $item)
                            <tr id="cart-row-{{ $id }}">
                                <td class="text-center align-middle">
                                    <img src="{{ $item['image'] ? asset('storage/upload/course_images/thumbnail/' . $item['image']) : asset('upload/no_image.jpg') }}" 
                                         alt="{{ $item['name'] }}" 
                                         class="rounded lazy" 
                                         style="width: 75px; height: auto;">
                                </td>
                                <td class="align-middle">
                                    <strong>{{ $item['name'] }}</strong><br>
                                    By <a href="{{ $item['instructor_id'] ? route('instructor.details', $item['instructor_id']) : '#' }}">
                                        {{ $item['instructor_name'] }}
                                    </a>
                                </td>
                                <td class="text-right align-middle">
                                    @if (isset($item['selling_price']) && isset($item['discount_price']) && $item['discount_price'] > 0)
                                        <del>{{ number_format($item['selling_price'], 2) }} TND</del><br>
                                        {{ number_format($item['price'], 2) }} TND
                                    @else
                                        {{ number_format($item['price'], 2) }} TND
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <button class="btn btn-danger btn-sm remove-from-cart" data-id="{{ $id }}">Remove</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Cart is empty.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if (!empty($cart))
                    <div class="d-flex justify-content-between pt-4">
                        <div>
                            <form action="{{ route('coupon.apply') }}" method="POST" id="coupon-form">
                                @csrf
                                <div class="input-group">
                                    <input class="form-control" type="text" name="coupon_name" placeholder="Enter coupon code" required>
                                    <button type="submit" class="btn theme-btn">Apply Coupon</button>
                                </div>
                            </form>
                            @if (!empty($coupons))
                                <div class="mt-3" id="coupon-list">
                                    <h6>Applied Coupons:</h6>
                                    <ul class="list-unstyled">
                                        @foreach ($coupons as $coupon)
                                            <li class="d-flex justify-content-between align-items-center mb-2">
                                                <span>{{ $coupon['coupon_name'] }} (-{{ number_format($coupon['discount_amount'], 2) }} TND)</span>
                                                <form action="{{ route('coupon.remove', $coupon['coupon_name']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-warning btn-sm">Remove</button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-4 ml-auto">
                        <div class="bg-gray p-4 mt-4" id="cart-summary">
                            <p>Subtotal: <span id="subtotal">{{ number_format($subtotal, 2) }} TND</span></p>
                            @if (!empty($coupons))
                                <p>Total Coupon Discount: <span id="coupon-discount">-{{ number_format($couponDiscount, 2) }} TND</span></p>
                                <h4>Total: <span id="total-price">{{ number_format($total, 2) }} TND</span></h4>
                            @else
                                <h4>Total: <span id="total-price">{{ number_format($subtotal, 2) }} TND</span></h4>
                            @endif
                            <a href="{{ route('checkout.create') }}" class="btn theme-btn w-100 mt-3">Checkout <i class="la la-arrow-right"></i></a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script>
    $(document).ready(function() {
        $('.remove-from-cart').on('click', function(e) {
            e.preventDefault();
            var courseId = $(this).data('id');
            var row = $('#cart-row-' + courseId);

            $.ajax({
                url: '{{ route("cart.remove", "__ID__") }}'.replace('__ID__', courseId),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        row.remove();

                        $('#subtotal').text(response.subtotal + ' TND');
                        $('#total-price').text(response.totalPrice + ' TND');

                        if (response.cartCount === 0) {
                            $('#cart-items').html('<tr><td colspan="4" class="text-center">Cart is empty.</td></tr>');
                            $('#cart-summary').remove();
                            $('#coupon-list').remove();
                        } else if (response.couponDiscount > 0) {
                            $('#coupon-discount').text('-' + response.couponDiscount + ' TND');
                            if (!document.getElementById('coupon-discount')) {
                                $('#subtotal').after('<p>Total Coupon Discount: <span id="coupon-discount">-' + response.couponDiscount + ' TND</span></p>');
                            }
                        } else {
                            $('#coupon-list').remove();
                            $('#coupon-discount').parent().remove();
                        }

                        alert(response.message);
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('An error occurred while removing the item.');
                }
            });
        });
    });
    </script>
@endsection