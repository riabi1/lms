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
                <h2 class="section__title text-white">Shopping Cart</h2>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>Pages</li>
                <li>Shopping Cart</li>
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
<section class="cart-area section-padding">
    <div class="container">
        <div class="table-responsive">
            <table class="table generic-table">
                <thead>
                    <tr>
                        <th scope="col">Image</th>
                        <th scope="col">Course Details</th>
                        <th scope="col">Price</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="cartPage">
                    @forelse ($cartItems as $item)
                    <tr>
                        <td>
                            @php
                            $course = App\Models\Course::find($item['course_id']);
                            @endphp
                            <img src="{{ asset('storage/upload/course_images/thumbnail/' . $course->course_image) }}" alt="{{ $item['course_name'] }}" class="img-fluid" style="max-width: 100px;" onerror="this.src='{{ asset('images/default-course.jpg') }}'">
                        </td>
                        <td>
                            <h5>{{ $item['course_name'] }}</h5>
                            <p>By <a href="{{ route('instructor.details', $item['instructor_id']) }}">{{ $course->instructor->name ?? 'Unknown Instructor' }}</a></p>
                        </td>
                     <td>
                        @if ($item['price'] < $item['original_price'])
                            <span class="text-success">${{ number_format($item['price'], 2) }}</span>
                            <del class="text-muted">${{ number_format($item['original_price'], 2) }}</del>
                        @else
                            ${{ number_format($item['price'], 2) }}
                        @endif
                    </td>
                        <td>
                            <form action="{{ route('cart.remove', $item['course_id']) }}" method="GET" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm"><i class="la la-trash"></i> Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Your cart is empty.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex flex-wrap align-items-center justify-content-between pt-4">
                @if($coupon)
                    <div>
                        <p>Coupon Applied: {{ $coupon['name'] }}</p>
                        <p>Discount: ${{ number_format($coupon['discount'], 2) }}</p>
                        <a href="{{ route('remove.coupon') }}" class="btn btn-sm btn-warning">Remove Coupon</a>
                    </div>
                @else
                    <form action="{{ route('apply.coupon') }}" method="POST" id="couponForm">
                        @csrf
                        <div class="input-group mb-2" id="couponField">
                            <input class="form-control form--control pl-3" type="text" id="coupon_name" name="coupon_name" placeholder="Coupon code">
                            <div class="input-group-append">
                                <button type="submit" class="btn theme-btn">Apply Code</button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <div class="col-lg-4 ml-auto">
            <div class="bg-gray p-4 rounded-rounded mt-40px" id="couponCalField">
                <h4>Cart Summary</h4>
                <hr>
                <p><strong>Subtotal:</strong> ${{ number_format($subtotal, 2) }}</p>
                @if($coupon)
                    <p><strong>Discount:</strong> -${{ number_format($coupon['discount'], 2) }}</p>
                    <p><strong>Total:</strong> ${{ number_format($totalPrice, 2) }}</p>
                @else
                    <p><strong>Total:</strong> ${{ number_format($totalPrice, 2) }}</p>
                @endif
            </div>
            <a href="{{ route('cart.checkout') }}" class="btn theme-btn w-100 mt-3">Checkout <i class="la la-arrow-right icon ml-1"></i></a>
        </div>
    </div><!-- end container -->
</section>
<!-- ================================
       END CART AREA
================================= -->

@endsection