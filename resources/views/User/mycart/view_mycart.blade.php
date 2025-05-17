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
                <div class="alert {{ session('success') ? 'alert-success' : (session('info') ? 'alert-info' : 'alert-danger') }} alert-dismissible fade show" role="alert">
                    {{ session('success') ?: session('info') ?: session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div id="cart-content">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" scope="col">Image</th>
                                <th scope="col">Course Details</th>
                                <th class="text-end" scope="col">Price</th>
                                <th class="text-center" scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody id="cart-items">
                            @forelse ($cartItems as $item)
                                <tr id="cart-row-{{ $item->id }}">
                                    <td class="text-center">
                                        <img src="{{ isset($item->attributes['image']) ? asset('upload/course_images/thumbnail/' . $item->attributes['image']) : asset('images/default-course.jpg') }}"
                                             srcset="{{ isset($item->attributes['image']) ? asset('upload/course_images/thumbnail/' . $item->attributes['image']) . ' 75w,' . asset('upload/course_images/thumbnail/' . $item->attributes['image']) . ' 50w' : asset('images/default-course.jpg') }}"
                                             sizes="(max-width: 576px) 50px, 75px"
                                             alt="{{ e($item->name) }}"
                                             class="rounded lazy"
                                             width="75"
                                             height="50"
                                             loading="lazy">
                                    </td>
                                    <td>
                                        <strong>{{ e($item->name) }}</strong><br>
                                        By <a href="{{ isset($item->attributes['instructor_id']) ? route('instructor.details', $item->attributes['instructor_id']) : '#' }}">
                                            {{ e($item->attributes['instructor_name'] ?? 'Unknown Instructor') }}
                                        </a>
                                    </td>
                                    <td class="text-end">
                                        @if (isset($item->attributes['selling_price']) && isset($item->attributes['discount_price']) && $item->attributes['discount_price'] > 0)
                                            <del>{{ number_format($item->attributes['selling_price'], 2) }} USD</del><br>
                                            <span class="effective-price">{{ number_format($item->price, 2) }} USD</span>
                                        @else
                                            <span class="effective-price">{{ number_format($item->price, 2) }} USD</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm remove-from-cart"
                                                data-id="{{ $item->id }}"
                                                data-is-guest="{{ Auth::check() ? '0' : '1' }}">Remove</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        Your cart is empty. <a href="{{ route('course.list') }}" class="btn btn-link">Continue Shopping</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($cartItems->count() > 0)
                    <div class="row g-4 pt-4">
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
                                                <span>{{ e($coupon['code']) }} (-{{ number_format($coupon['discount_amount'], 2) }} USD)</span>
                                                <a href="{{ route('coupon.remove', $coupon['code']) }}"
                                                   class="btn btn-warning btn-sm remove-coupon"
                                                   data-code="{{ $coupon['code'] }}">Remove</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-4">
                            <div class="card p-4 mt-4">
                                <h5 class="card-title">Cart Summary</h5>
                                <div class="card-body">
                                    <p class="d-flex justify-content-between"><strong>Subtotal:</strong> <span id="subtotal">{{ number_format($subtotal, 2) }} USD</span></p>
                                    @if ($couponDiscount > 0)
                                        <p class="d-flex justify-content-between" id="coupon-discount-container"><strong>Coupon Discount:</strong> <span id="coupon-discount">-{{ number_format($couponDiscount, 2) }} USD</span></p>
                                    @endif
                                    <h4 class="d-flex justify-content-between"><strong>Total:</strong> <span id="total-price">{{ number_format($total, 2) }} USD</span></h4>
                                    <a href="{{ route('checkout.create') }}" class="btn theme-btn w-100 mt-3">Checkout <i class="la la-arrow-right"></i></a>
                                </div>
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
        .cart-area {
            padding: 2rem 0;
        }
        .cart-area .table img {
            width: 75px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        .cart-area .table-responsive {
            margin-bottom: 1.5rem;
            overflow-x: auto;
        }
        .cart-area .card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }
        .cart-area .effective-price {
            font-weight: 600;
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
            top: 1rem;
            right: 1rem;
            z-index: 1050;
            padding: 0.75rem;
            border-radius: 4px;
            max-width: 300px;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .alert {
            margin-bottom: 1rem;
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
                font-size: 0.875rem;
                padding: 0.5rem;
            }
            .cart-area .table img {
                width: 50px;
                height: 33px;
            }
            .cart-area .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }
    </style>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function showNotification(message, type) {
                const $message = $(`<div class="cart-message"><div class="alert alert-${type} alert-dismissible fade show" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>`);
                $('body').append($message);
                setTimeout(() => $message.fadeOut(300, () => $message.remove()), 3000);
            }

            function updateCartSummary(data) {
                $('#subtotal').text(data.subtotal + ' USD');
                $('#total-price').text(data.totalPrice + ' USD');
                if (data.couponDiscount && parseFloat(data.couponDiscount) > 0) {
                    if (!$('#coupon-discount-container').length) {
                        $('#subtotal').parent().after(`<p class="d-flex justify-content-between" id="coupon-discount-container"><strong>Coupon Discount:</strong> <span id="coupon-discount">-${data.couponDiscount} USD</span></p>`);
                    } else {
                        $('#coupon-discount').text(`-${data.couponDiscount} USD`);
                    }
                } else {
                    $('#coupon-discount-container').remove();
                }
            }

            $('.remove-from-cart').on('click', function(e) {
                e.preventDefault();
                const $button = $(this);
                const itemId = $button.data('id');
                const isGuest = $button.data('is-guest') === 1;
                const $row = $(`#cart-row-${itemId}`);

                if (isGuest) {
                    showNotification('Please log in to manage your cart.', 'danger');
                    window.location.href = '{{ route('login') }}?redirect={{ urlencode(route('cart')) }}';
                    return;
                }

                $.ajax({
                    url: `{{ url('cart/remove') }}/${itemId}`,
                    method: 'POST',
                    dataType: 'json',
                    beforeSend: function() {
                        $button.prop('disabled', true).text('Removing...');
                    },
                    success: function(response) {
                        if (response.success) {
                            $row.fadeOut(300, function() {
                                $(this).remove();
                                if (response.cartCount === 0) {
                                    $('#cart-items').html(`
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                Your cart is empty. <a href="{{ route('course.list') }}" class="btn btn-link">Continue Shopping</a>
                                            </td>
                                        </tr>
                                    `);
                                    $('#cart-summary').remove();
                                    $('#coupon-list').remove();
                                }
                            });
                            updateCartSummary(response);
                            $(document).trigger('cartUpdated', {
                                cartCount: response.cartCount,
                                cartSubTotal: response.subtotal
                            });
                            showNotification(response.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        showNotification(xhr.responseJSON?.message || 'Failed to remove item.', 'danger');
                        $button.prop('disabled', false).text('Remove');
                    },
                    complete: function() {
                        $button.prop('disabled', false).text('Remove');
                    }
                });
            });

            $('#coupon-form').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                const $button = $form.find('button[type="submit"]');
                const couponCode = $form.find('input[name="code"]').val().trim().toUpperCase();
                const csrfToken = $form.find('input[name="_token"]').val();

                if (!couponCode) {
                    showNotification('Please enter a coupon code.', 'danger');
                    return;
                }

                if (!csrfToken) {
                    showNotification('CSRF token is missing.', 'danger');
                    return;
                }

                const formData = {
                    _token: csrfToken,
                    code: couponCode
                };

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        $button.prop('disabled', true).text('Applying...');
                    },
                    success: function(response) {
                        if (response.success) {
                            updateCartSummary(response);
                            if (response.coupons && response.coupons.length > 0) {
                                let couponHtml = '<h6>Applied Coupons:</h6><ul class="list-unstyled">';
                                $.each(response.coupons, function(index, coupon) {
                                    couponHtml += `
                                        <li class="d-flex justify-content-between align-items-center mb-2">
                                            <span>${coupon.code} (-${parseFloat(coupon.discount_amount).toFixed(2)} USD)</span>
                                            <a href="{{ url('coupon/remove') }}/${coupon.code}"
                                               class="btn btn-warning btn-sm remove-coupon"
                                               data-code="${coupon.code}">Remove</a>
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
                            bindRemoveCouponEvents();
                            showNotification(response.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX Error:', xhr.responseJSON, 'Form Data:', formData);
                        let errorMessage = 'Failed to apply coupon.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.code) {
                            errorMessage = xhr.responseJSON.errors.code[0];
                        }
                        showNotification(errorMessage, 'danger');
                        $button.prop('disabled', false).text('Apply Coupon');
                    },
                    complete: function() {
                        $button.prop('disabled', false).text('Apply Coupon');
                    }
                });
            });

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
                            if (response.success) {
                                $li.fadeOut(300, function() {
                                    $(this).remove();
                                    if (!response.coupons || response.coupons.length === 0) {
                                        $('#coupon-list').remove();
                                    }
                                });
                                updateCartSummary(response);
                                showNotification(response.message, 'success');
                            }
                        },
                        error: function(xhr) {
                            showNotification(xhr.responseJSON?.message || 'Failed to remove coupon.', 'danger');
                            $link.prop('disabled', false).text('Remove');
                        },
                        complete: function() {
                            $link.prop('disabled', false).text('Remove');
                        }
                    });
                });
            }

            bindRemoveCouponEvents();

            $(document).on('cartUpdated', function(e, data) {
                if (data.cartCount !== undefined) {
                    $('#cartQty').text(data.cartCount);
                    $('#cartSubTotal').text('USD ' + data.cartSubTotal);
                }
            });
        });
    </script>
@endsection