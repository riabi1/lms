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

            <table class="table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Course Details</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cart as $id => $item)
                        <tr>
                            <td>
                                <img src="{{ !empty($item['image']) ? Storage::url('upload/course_images/thumbnail/' . $item['image']) : url('upload/no_image.jpg') }}" 
                                     alt="{{ $item['name'] }}" 
                                     class="rounded lazy" 
                                     style="width: 50px; height: auto;">
                            </td>
                            <td>
                                {{ $item['name'] }} By {{ $item['instructor_name'] ?? 'Unknown Instructor' }}
                            </td>
                            <td>
                                @if (isset($item['selling_price']) && isset($item['discount_price']) && $item['discount_price'] > 0)
                                    <del>${{ number_format($item['selling_price'], 2) }}</del> 
                                    ${{ number_format($item['price'], 2) }}
                                @else
                                    ${{ number_format($item['selling_price'] ?? $item['price'], 2) }}
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Cart is empty.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-between pt-4">
                @if (!Session::has('coupon'))
                    <form action="{{ route('coupon.apply') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input class="form-control" type="text" name="coupon_name" placeholder="Coupon code">
                            <button type="submit" class="btn theme-btn">Apply Code</button>
                        </div>
                    </form>
                @endif
            </div>

            @if ($cart)
                <div class="col-lg-4 ml-auto">
                    <div class="bg-gray p-4 mt-4">
                        <p>Subtotal: ${{ number_format($subtotal, 2) }}</p>
                        @if (Session::has('coupon'))
                            <p>Coupon Discount: -${{ number_format($couponDiscount, 2) }}</p>
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