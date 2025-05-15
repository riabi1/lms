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
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" scope="col">Image</th>
                                <th scope="col">Course Details</th>
                                <th class="text-right" scope="col">Price</th>
                                <th class="text-center" scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody id="cart-items">
                            @forelse ($cartItems as $item)
                                <tr id="cart-row-{{ $item->id ?? $item->cartable_id }}">
                                    <td class="text-center align-middle">
                                        <img src="{{ isset($item->options['image']) ? asset('upload/course_images/thumbnail/' . $item->options['image']) : asset('images/default-course.jpg') }}"
                                             alt="{{ e($item->options['name'] ?? 'Course') }}"
                                             class="rounded lazy"
                                             loading="lazy">
                                    </td>
                                    <td class="align-middle">
                                        <strong>{{ e($item->options['name'] ?? 'Unknown Course') }}</strong><br>
                                        By <a href="{{ isset($item->options['instructor_id']) ? route('instructor.details', $item->options['instructor_id']) : '#' }}">
                                            {{ e($item->options['instructor_name'] ?? 'Unknown Instructor') }}
                                        </a>
                                    </td>
                                    <td class="text-right align-middle">
                                        @if (isset($item->options['selling_price']) && isset($item->options['discount_price']) && $item->options['discount_price'] > 0)
                                            <del>{{ number_format($item->options['selling_price'] * $item->quantity, 2) }} TND</del><br>
                                            <span class="effective-price">{{ number_format($item->price * $item->quantity, 2) }} TND</span>
                                        @else
                                            <span class="effective-price">{{ number_format($item->price * $item->quantity, 2) }} TND</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <button class="btn btn-danger btn-sm remove-from-cart"
                                                data-id="{{ $item->id ?? $item->cartable_id }}"
                                                data-is-guest="{{ Auth::check() ? '0' : '1' }}">Remove</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        Your cart is empty. <a href="{{ route('course.list') }}" class="btn btn-link">Continue Shopping</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($cartItems->count() > 0)
                    @php
                        $courseIds = $cartItems->pluck('cartable_id')->toArray();
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
                                    <div class="input-group mb-3">
                                        <input class="form-control" type="text" name="coupon_name" placeholder="Enter coupon code" required>
                                        <button type="submit" class="btn theme-btn">Apply Coupon</button>
                                    </div>
                                    @error('coupon_name')
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
                                                <span>{{ e($coupon['coupon_name']) }} (-{{ number_format($coupon['discount_amount'], 2) }} TND)</span>
                                                <a href="{{ route('coupon.remove', $coupon['coupon_name']) }}"
                                                   class="btn btn-warning btn-sm remove-coupon"
                                                   data-code="{{ $coupon['coupon_name'] }}">Remove</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-4">
                            <div class="bg-gray p-4 mt-4 rounded" id="cart-summary">
                                <p><strong>Subtotal:</strong> <span id="subtotal">{{ number_format($subtotal, 2) }} TND</span></p>
                                @if ($couponDiscount > 0)
                                    <p id="coupon-discount-container"><strong>Coupon Discount:</strong> <span id="coupon-discount">-{{ number_format($couponDiscount, 2) }} TND</span></p>
                                @endif
                                <h4><strong>Total:</strong> <span id="total-price">{{ number_format($total, 2) }} TND</span></h4>
                                <a href="{{ route('checkout.create') }}" class="btn theme-btn w-100 mt-3">Checkout <i class="la la-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('styles')
    <style>
        .cart-area .table img {
            width: 75px;
            height: auto;
        }
        .cart-area .table-responsive {
            margin-bottom: 20px;
        }
        .cart-area .bg-gray {
            border: 1px solid #e9ecef;
        }
        .cart-area .effective-price {
            font-weight: bold;
            color: #28a745;
        }
        .cart-area .btn-danger {
            transition: background-color 0.3s ease;
        }
        .cart-area .btn-danger:hover {
            background-color: #c82333;
        }
        .cart-message {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
            padding: 10px;
            border-radius: 4px;
            max-width: 300px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        @media (max-width: 576px) {
            .cart-area .table th, .cart-area .table td {
                font-size: 14px;
                padding: 10px;
            }
            .cart-area .table img {
                width: 50px;
            }
        }
    </style>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            // Setup AJAX with CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Show notification
            function showNotification(message, type) {
                const $message = $('<div class="cart-message"></div>')
                    .html(`<div class="alert alert-${type}">${message}</div>`);
                $('body').append($message);
                setTimeout(() => $message.fadeOut(300, () => $message.remove()), 3000);
            }

            // Update cart summary
            function updateCartSummary(data) {
                $('#subtotal').text(data.cartSubTotal + ' TND');
                $('#total-price').text(data.cartSubTotal + ' TND'); // Total may differ if coupons applied

                if (data.couponDiscount && data.couponDiscount > 0) {
                    if (!$('#coupon-discount-container').length) {
                        $('#subtotal').after(`<p id="coupon-discount-container"><strong>Coupon Discount:</strong> <span id="coupon-discount">-${data.couponDiscount} TND</span></p>`);
                    } else {
                        $('#coupon-discount').text(`-${data.couponDiscount} TND`);
                    }
                    $('#total-price').text((parseFloat(data.cartSubTotal) - parseFloat(data.couponDiscount)).toFixed(2) + ' TND');
                } else {
                    $('#coupon-discount-container').remove();
                }
            }

            // Remove from cart
            $('.remove-from-cart').on('click', function(e) {
                e.preventDefault();
                const $button = $(this);
                const itemId = $button.data('id');
                const isGuest = $button.data('is-guest') === 1;
                const $row = $(`#cart-row-${itemId}`);

                $.ajax({
                    url: `{{ url('cart/remove') }}/${itemId}`,
                    method: 'POST',
                    dataType: 'json',
                    beforeSend: function() {
                        $button.prop('disabled', true).text('Removing...');
                    },
                    success: function(response) {
                        $row.remove();
                        updateCartSummary(response);

                        // Handle empty cart
                       如果是(response.cartCount === 0) {
                            $('#cart-items').html(`
                                <tr>
                                    <td colspan="4" class="text-center">
                                        Your cart is empty. <a href="{{ route('course.list') }}" class="btn btn-link">Continue Shopping</a>
                                    </td>
                                </tr>
                            `);
                            $('#cart-summary').remove();
                            $('#coupon-list').remove();
                        }

                        // Update header cart
                        $(document).trigger('cartUpdated', {
                            cartCount: response.cartCount,
                            cartSubTotal: response.cartSubTotal
                        });

                        showNotification(response.message, 'success');
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        showNotification(response.message || 'Failed to remove item.', 'danger');
                        $button.prop('disabled', false).text('Remove');
                    }
                });
            });

            // Apply coupon
            $('#coupon-form').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                const $button = $form.find('button[type="submit"]');
                const couponCode = $form.find('input[name="coupon_name"]').val().trim();

                if (!couponCode) {
                    showNotification('Please enter a coupon code.', 'danger');
                    return;
                }

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    beforeSend: function() {
                        $button.prop('disabled', true).text('Applying...');
                    },
                    success: function(response) {
                        // Update cart summary
                        updateCartSummary({
                            cartSubTotal: response.cartSubTotal || '{{ number_format($subtotal, 2) }}',
                            couponDiscount: response.couponDiscount || '{{ number_format($couponDiscount, 2) }}'
                        });

                        // Update coupon list
                        if (response.coupons && Object.keys(response.coupons).length > 0) {
                            let couponHtml = '<h6>Applied Coupons:</h6><ul class="list-unstyled">';
                            $.each(response.coupons, function(key, coupon) {
                                couponHtml += `
                                    <li class="d-flex justify-content-between align-items-center mb-2">
                                        <span>${coupon.coupon_name} (-${parseFloat(coupon.discount_amount).toFixed(2)} TND)</span>
                                        <a href="{{ url('coupon/remove') }}/${coupon.coupon_name}"
                                           class="btn btn-warning btn-sm remove-coupon"
                                           data-code="${coupon.coupon_name}">Remove</a>
                                    </li>`;
                            });
                            couponHtml += '</ul>';
                            if ($('#coupon-list').length) {
                                $('#coupon-list').html(couponHtml);
                            } else {
                                $form.after(`<div class="mt-3" id="coupon-list">${couponHtml}</div>`);
                            }
                        } else {
                            $('#coupon-list').remove();
                        }

                        // Rebind remove coupon events
                        bindRemoveCouponEvents();

                        showNotification(response.message, 'success');
                        $button.prop('disabled', false).text('Apply Coupon');
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        showNotification(response.message || 'Failed to apply coupon.', 'danger');
                        $button.prop('disabled', false).text('Apply Coupon');
                    }
                });
            });

            // Remove coupon
            function bindRemoveCouponEvents() {
                $('.remove-coupon').off('click').on('click', function(e) {
                    e.preventDefault();
                    const $link = $(this);
                    const couponCode = $link.data('code');
                    const $li = $link.closest('li');

                    $.ajax({
                        url: $link.attr('href'),
                        method: 'GET',
                        dataType: 'json',
                        beforeSend: function() {
                            $link.prop('disabled', true).text('Removing...');
                        },
                        success: function(response) {
                            $li.remove();
                            updateCartSummary({
                                cartSubTotal: response.cartSubTotal || '{{ number_format($subtotal, 2) }}',
                                couponDiscount: response.couponDiscount || '{{ number_format($couponDiscount, 2) }}'
                            });

                            if (!response.coupons || Object.keys(response.coupons).length === 0) {
                                $('#coupon-list').remove();
                            }

                            showNotification(response.message, 'success');
                            $link.prop('disabled', false).text('Remove');
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON || {};
                            showNotification(response.message || 'Failed to remove coupon.', 'danger');
                            $link.prop('disabled', false).text('Remove');
                        }
                    });
                });
            }

            // Initial binding for remove coupon events
            bindRemoveCouponEvents();

            // Update cart on custom event
            $(document).on('cartUpdated', function(e, data) {
                if (data.cartCount !== undefined) {
                    $('#cartQty').text(data.cartCount);
                    $('#cartSubTotal').text('TND ' + data.cartSubTotal);
                }
            });
        });
    </script>
@endsection