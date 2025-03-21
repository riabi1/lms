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

            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" style="width: 10%;">Image</th>
                        <th style="width: 50%;">Course Details</th>
                        <th class="text-right" style="width: 20%;">Price</th>
                        <th class="text-center" style="width: 20%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cart as $id => $item)
                        <tr>
                            <td class="text-center align-middle">
                                <img src="{{ !empty($item['image']) ? Storage::url('upload/course_images/thumbnail/' . $item['image']) : url('upload/no_image.jpg') }}" 
                                     alt="{{ $item['name'] }}" 
                                     class="rounded lazy" 
                                     style="width: 75px; height: auto;">
                            </td>
                            <td class="align-middle">
                                <strong>{{ $item['name'] }}</strong><br>
                                By <a href="{{ route('instructor.details', $item['instructor_id'] ?? 'unknown') }}">
                                    {{ $item['instructor_name'] ?? 'Unknown Instructor' }}
                                </a>
                            </td>
                            <td class="text-right align-middle">
                                @if (isset($item['selling_price']) && isset($item['discount_price']) && $item['discount_price'] > 0)
                                    <del>${{ number_format($item['selling_price'], 2) }}</del><br>
                                    ${{ number_format($item['price'], 2) }}
                                @else
                                    ${{ number_format($item['selling_price'] ?? $item['price'], 2) }}
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Cart is empty.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-between pt-4">
                <div>
                    <form action="{{ route('coupon.apply') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input class="form-control" type="text" name="coupon_name" placeholder="Enter coupon code">
                            <button type="submit" class="btn theme-btn">Apply Coupon</button>
                        </div>
                    </form>
                    @if (!empty($coupons))
                        <div class="mt-3">
                            <h6>Applied Coupons:</h6>
                            <ul class="list-unstyled">
                                @foreach ($coupons as $coupon)
                                    <li class="d-flex justify-content-between align-items-center mb-2">
                                        <span>{{ $coupon['coupon_name'] }} (-${{ number_format($coupon['discount_amount'], 2) }})</span>
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

            @if ($cart)
                <div class="col-lg-4 ml-auto">
                    <div class="bg-gray p-4 mt-4">
                        <p>Subtotal: ${{ number_format($subtotal, 2) }}</p>
                        @if (!empty($coupons))
                            <p>Total Coupon Discount: -${{ number_format($couponDiscount, 2) }}</p>
                            <h4>Total: ${{ number_format($total, 2) }}</h4>
                        @else
                            <h4>Total: ${{ number_format($subtotal, 2) }}</h4>
                        @endif
                        <a href="{{ route('checkout.create') }}" class="btn theme-btn w-100">Checkout <i class="la la-arrow-right"></i></a>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection