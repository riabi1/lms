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
                                    <img src="{{ isset($item->attributes['image']) ? asset('upload/course_images/thumbnail/' . $item->attributes['image']) : asset('upload/no_image.jpg') }}" 
                                         alt="{{ e($item->name) }}" 
                                         class="rounded lazy" 
                                         style="width: 75px; height: auto;"
                                         loading="lazy"
                                         onerror="this.src='{{ asset('upload/no_image.jpg') }}'">
                                </td>
                                <td class="align-middle">
                                    <strong>{{ e($item->name) }}</strong><br>
                                    By <a href="{{ isset($item->attributes['instructor_id']) ? route('instructor.details', $item->attributes['instructor_id']) : '#' }}">
                                        {{ e($item->attributes['instructor_name'] ?? 'Unknown Instructor') }}
                                    </a>
                                </td>
                                <td class="text-right align-middle">
                                    @if (isset($item->attributes['selling_price']) && isset($item->attributes['discount_price']) && $item->attributes['discount_price'] > 0)
                                        <del>{{ number_format($item->attributes['selling_price'] * $item->quantity, 2) }} TND</del><br>
                                        <span class="effective-price">{{ number_format($item->price * $item->quantity, 2) }} TND</span>
                                    @else
                                        <span class="effective-price">{{ number_format($item->price * $item->quantity, 2) }} TND</span>
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
                                    <div class="input-group mb-3">
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
                                                <span>{{ e($coupon['code']) }} (-{{ number_format($coupon['discount_amount'], 2) }} TND)</span>
                                                <a href="{{ route('coupon.remove', $coupon['code']) }}" class="btn btn-warning btn-sm remove-coupon" data-code="{{ $coupon['code'] }}">Remove</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-4">
                            <div class="bg-gray p-4 mt-4" id="cart-summary">
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

@section('scripts')
    @parent
    <script>
    $(document).ready(function() {
        // Show notification
        function showNotification(message, type) {
            const $message = $('<div class="cart-message"></div>').html(`<div class="alert alert-${type}">${message}</div>`)
                .css({ position: 'fixed', top: '10px', right: '10px', 'z-index': 1000 });
            $('body').append($message);
            setTimeout(() => $message.fadeOut(300, () => $message.remove()), 3000);
        }

        // Remove from cart
        $('.remove-from-cart').on('click', function(e) {
            e.preventDefault();
            const courseId = $(this).data('id');
            const $row = $('#cart-row-' + courseId);
            const $cartItem = $('#cart-item-' + courseId);

            $.ajax({
                url: '{{ route("cart.remove", ":id") }}'.replace(':id', courseId),
                method: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                dataType: 'json',
                beforeSend: function() {
                    $row.find('.remove-from-cart').prop('disabled', true).text('Removing...');
                },
                success: function(response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                        return;
                    }
                    if (response.success) {
                        $row.remove();
                        if ($cartItem.length) {
                            $cartItem.remove();
                        }

                        // Update subtotal, coupon discount, and total
                        $('#subtotal').text(response.subtotal + ' TND');
                        $('#total-price').text(response.totalPrice + ' TND');

                        if (response.couponDiscount > 0) {
                            if (!$('#coupon-discount-container').length) {
                                $('#subtotal').after('<p id="coupon-discount-container"><strong>Coupon Discount:</strong> <span id="coupon-discount">-' + response.couponDiscount + ' TND</span></p>');
                            } else {
                                $('#coupon-discount').text('-' + response.couponDiscount + ' TND');
                            }
                        } else {
                            $('#coupon-discount-container').remove();
                        }

                        // Handle empty cart
                        if (response.cartCount === 0) {
                            $('#cart-items').html('<tr><td colspan="4" class="text-center">Cart is empty.</td></tr>');
                            $('#cart-summary').remove();
                            $('#coupon-list').remove();
                            if ($('#cartDropdown').length) {
                                $('#cartDropdown').html(`
                                    <li class="media media-card">
                                        <div class="media-body fs-15 text-center">
                                            <p class="text-muted lh-18">Your cart is empty</p>
                                        </div>
                                    </li>
                                    <li class="mt-3">
                                        <a href="{{ route('cart') }}" class="btn theme-btn w-100 py-2">Go to Cart <i class="la la-arrow-right icon ml-1"></i></a>
                                    </li>
                                `);
                            }
                        }

                        // Update header cart
                        if ($('#cartQty').length) {
                            $('#cartQty').text(response.cartCount);
                        }
                        if ($('#cartSubTotal').length) {
                            $('#cartSubTotal').text('TND ' + response.subtotal);
                        }

                        $('.add-to-cart[data-course-id="' + courseId + '"]').each(function() {
                            $(this).data('in-cart', false)
                                .removeAttr('data-in-cart')
                                .prop('disabled', false)
                                .html('<i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart');
                        });

                        if ($('#cartDropdown').length) {
                            $.ajax({
                                url: '{{ route("cart") }}',
                                method: 'GET',
                                success: function(html) {
                                    const $newCart = $(html).find('#cartDropdown').html();
                                    $('#cartDropdown').html($newCart);
                                },
                                error: function(xhr) {
                                    console.error('Cart dropdown refresh error:', xhr);
                                }
                            });
                        }

                        showNotification(response.message, 'success');
                    } else {
                        showNotification(response.message || 'Action completed.', 'info');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    showNotification(response.message || 'An error occurred while removing the item.', 'danger');
                    $row.find('.remove-from-cart').prop('disabled', false).text('Remove');
                }
            });
        });

        // Apply coupon
        $('#coupon-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const $button = $form.find('button[type="submit"]');

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $button.prop('disabled', true).text('Applying...');
                },
                success: function(response) {
                    if (response.success) {
                        // Update subtotal, coupon discount, and total
                        $('#subtotal').text(response.subtotal + ' TND');
                        $('#total-price').text(response.totalPrice + ' TND');

                        // Update coupon list
                        if (response.coupons && response.coupons.length > 0) {
                            let couponHtml = '<h6>Applied Coupons:</h6><ul class="list-unstyled">';
                            response.coupons.forEach(function(coupon) {
                                couponHtml += `
                                    <li class="d-flex justify-content-between align-items-center mb-2">
                                        <span>${coupon.code} (-${parseFloat(coupon.discount_amount).toFixed(2)} TND)</span>
                                        <a href="{{ route('coupon.remove', '') }}/${coupon.code}" class="btn btn-warning btn-sm remove-coupon" data-code="${coupon.code}">Remove</a>
                                    </li>`;
                            });
                            couponHtml += '</ul>';
                            if ($('#coupon-list').length) {
                                $('#coupon-list').html(couponHtml);
                            } else {
                                $form.after('<div class="mt-3" id="coupon-list">' + couponHtml + '</div>');
                            }
                        } else {
                            $('#coupon-list').remove();
                        }

                        // Update coupon discount display
                        if (response.couponDiscount > 0) {
                            if (!$('#coupon-discount-container').length) {
                                $('#subtotal').after('<p id="coupon-discount-container"><strong>Coupon Discount:</strong> <span id="coupon-discount">-' + response.couponDiscount + ' TND</span></p>');
                            } else {
                                $('#coupon-discount').text('-' + response.couponDiscount + ' TND');
                            }
                        } else {
                            $('#coupon-discount-container').remove();
                        }

                        // Rebind remove coupon events
                        bindRemoveCouponEvents();

                        showNotification(response.message, 'success');
                    } else {
                        showNotification(response.message || 'Invalid coupon code.', 'danger');
                    }
                    $button.prop('disabled', false).text('Apply Coupon');
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    showNotification(response.message || 'An error occurred while applying the coupon.', 'danger');
                    $button.prop('disabled', false).text('Apply Coupon');
                }
            });
        });

        // Remove coupon
        function bindRemoveCouponEvents() {
            $('.remove-coupon').on('click', function(e) {
                e.preventDefault();
                const couponCode = $(this).data('code');
                const $li = $(this).closest('li');

                $.ajax({
                    url: '{{ route("coupon.remove", ":code") }}'.replace(':code', couponCode),
                    method: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $li.find('.remove-coupon').prop('disabled', true).text('Removing...');
                    },
                    success: function(response) {
                        if (response.success) {
                            $li.remove();
                            $('#subtotal').text(response.subtotal + ' TND');
                            $('#total-price').text(response.totalPrice + ' TND');

                            if (response.couponDiscount > 0) {
                                if (!$('#coupon-discount-container').length) {
                                    $('#subtotal').after('<p id="coupon-discount-container"><strong>Coupon Discount:</strong> <span id="coupon-discount">-' + response.couponDiscount + ' TND</span></p>');
                                } else {
                                    $('#coupon-discount').text('-' + response.couponDiscount + ' TND');
                                }
                            } else {
                                $('#coupon-discount-container').remove();
                            }

                            if (response.coupons.length === 0) {
                                $('#coupon-list').remove();
                            }

                            showNotification(response.message, 'success');
                        } else {
                            showNotification(response.message || 'Failed to remove coupon.', 'danger');
                        }
                        $li.find('.remove-coupon').prop('disabled', false).text('Remove');
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        showNotification(response.message || 'An error occurred while removing the coupon.', 'danger');
                        $li.find('.remove-coupon').prop('disabled', false).text('Remove');
                    }
                });
            });
        }

        // Initial binding for remove coupon events
        bindRemoveCouponEvents();
    });
    </script>
@endsection