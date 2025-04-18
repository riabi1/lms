<header class="header-menu-area bg-white">
        <div class="header-top pr-100px pl-100px border-bottom border-bottom-gray py-2">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="header-widget">
                            <ul class="generic-list-item d-flex flex-wrap align-items-center fs-14 header-top-list">
                                <li class="d-flex align-items-center pr-4 mr-4 border-right border-right-gray">
                                    <i class="la la-phone mr-2"></i>
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteSettings->phone) }}">{{ $siteSettings->phone }}</a>
                                </li>
                                <li class="d-flex align-items-center">
                                    <i class="la la-envelope-o mr-2"></i>
                                    <a href="mailto:{{ $siteSettings->email }}">{{ $siteSettings->email }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="header-widget d-flex flex-wrap align-items-center justify-content-end">
                            <div class="theme-picker d-flex align-items-center mr-4">
                                <button class="theme-picker-btn dark-mode-btn" title="Dark mode">
                                    <svg id="moon" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                    </svg>
                                </button>
                                <button class="theme-picker-btn light-mode-btn" title="Light mode">
                                    <svg id="sun" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="5"></circle>
                                        <line x1="12" y1="1" x2="12" y2="3"></line>
                                        <line x1="12" y1="21" x2="12" y2="23"></line>
                                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                        <line x1="1" y1="12" x2="3" y2="12"></line>
                                        <line x1="21" y1="12" x2="23" y2="12"></line>
                                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                                    </svg>
                                </button>
                            </div>
                            <ul class="generic-list-item d-flex flex-wrap align-items-center fs-14 header-top-list border-left border-left-gray pl-4 ml-4">
                                @guest
                                    <li class="d-flex align-items-center pr-4 mr-4 border-right border-right-gray">
                                        <i class="la la-sign-in mr-2"></i>
                                        <a href="{{ route('login') }}">Login</a>
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <i class="la la-user mr-2"></i>
                                        <a href="{{ route('register') }}">Register</a>
                                    </li>
                                @else
                                    <li class="d-flex align-items-center pr-4 mr-4 border-right border-right-gray">
                                        <i class="la la-tachometer mr-2"></i>
                                        <a href="{{ route('dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <i class="la la-sign-out mr-2"></i>
                                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                @endguest
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="header-menu-content pr-100px pl-100px bg-white py-2">
            <div class="container-fluid">
                <div class="main-menu-content">
                    <a href="#" class="down-button"><i class="la la-angle-down"></i></a>
                    <div class="row align-items-center">
                        <div class="col-lg-2">
                            <div class="logo-box d-flex align-items-center">
                                <a href="{{ url('/') }}" class="logo">
                                    <img src="{{ $siteSettings->logo ? Storage::url($siteSettings->logo) : asset('images/default-logo.png') }}" 
                                         alt="logo" class="lazy logo-header" loading="lazy" 
                                         onerror="this.src='{{ asset('images/no_image.jpg') }}'">
                                </a>
                                <div class="user-btn-action d-flex ml-4">
                                    <div class="search-menu-toggle icon-element icon-element-sm shadow-sm mr-3" data-toggle="tooltip" data-placement="top" title="Search">
                                        <i class="la la-search"></i>
                                    </div>
                                    <div class="off-canvas-menu-toggle cat-menu-toggle icon-element icon-element-sm shadow-sm mr-3" data-toggle="tooltip" data-placement="top" title="Category menu">
                                        <i class="la la-th-large"></i>
                                    </div>
                                    <div class="off-canvas-menu-toggle main-menu-toggle icon-element icon-element-sm shadow-sm" data-toggle="tooltip" data-placement="top" title="Main menu">
                                        <i class="la la-bars"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            $categories = App\Models\Category::orderBy('category_name', 'ASC')->get();
                            $blogCategories = App\Models\BlogCategory::orderBy('name', 'ASC')->get();
                            $cartItems = \Darryldecode\Cart\Facades\CartFacade::getContent();
                            $cartQty = $cartItems->count();
                            $cartSubTotal = \Darryldecode\Cart\Facades\CartFacade::getSubTotal();
                        @endphp

                        <div class="col-lg-10">
                            <div class="menu-wrapper d-flex align-items-center">
                                <div class="menu-category mr-5">
                                    <ul>
                                        <li>
                                            <a href="#">Categories <i class="la la-angle-down fs-12"></i></a>
                                            <ul class="cat-dropdown-menu">
                                                @forelse ($categories as $cat)
                                                    @php
                                                        $subcategories = App\Models\SubCategory::where('category_id', $cat->id)->get();
                                                    @endphp
                                                    <li>
                                                        <a href="{{ url('category/'.$cat->id.'/'.$cat->category_slug) }}">{{ $cat->category_name }}<i class="la la-angle-right"></i></a>
                                                        <ul class="sub-menu">
                                                            @forelse ($subcategories as $subcat)
                                                                <li><a href="{{ url('subcategory/'.$subcat->id.'/'.$subcat->subcategory_slug) }}">{{ $subcat->subcategory_name }}</a></li>
                                                            @empty
                                                                <li>No subcategories available</li>
                                                            @endforelse
                                                        </ul>
                                                    </li>
                                                @empty
                                                    <li>No categories available</li>
                                                @endforelse
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                                <nav class="main-menu mr-5">
                                    <ul>
                                        <li>
                                            <a href="#">Courses <i class="la la-angle-down fs-12"></i></a>
                                            <ul class="dropdown-menu-item">
                                                <li><a href="{{ route('course.list') }}">Course List</a></li>
                                            </ul>
                                        </li>
                                        <li>
                                            <a href="#">Blog <i class="la la-angle-down fs-12"></i></a>
                                            <ul class="dropdown-menu-item">
                                                <li><a href="{{ route('blog.list') }}">All Blogs</a></li>
                                                @forelse ($blogCategories as $blogCat)
                                                    <li>
                                                        <a href="{{ url('blog/category/'.$blogCat->id.'/'.$blogCat->slug) }}">{{ $blogCat->name }}</a>
                                                    </li>
                                                @empty
                                                    <li>No blog categories available</li>
                                                @endforelse
                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                                <div class="shop-cart mr-5">
                                    <ul>
                                        <li>
                                            <p class="shop-cart-btn d-flex align-items-center">
                                                <i class="la la-shopping-cart fs-20"></i>
                                                <span class="product-count ml-2" id="cartQty">{{ $cartQty }}</span>
                                            </p>
                                            <ul class="cart-dropdown-menu p-3 shadow-sm" style="min-width: 300px;" id="cartDropdown">
                                                @if ($cartQty > 0)
                                                    @foreach ($cartItems as $item)
                                                        <li class="media media-card border-bottom pb-2 mb-2" id="cart-item-{{ $item->id }}">
                                                            <a href="{{ url('course/details/'.$item->id.'/'.Str::slug($item->name)) }}" class="media-img mr-3">
                                                                <img src="{{ $item->attributes->image ? asset('storage/upload/course_images/thumbnail/' . $item->attributes->image) : asset('images/no_image.jpg') }}"
                                                                     alt="{{ e($item->name) }}"
                                                                     class="lazy rounded"
                                                                     style="width: 60px; height: auto;"
                                                                     loading="lazy"
                                                                     onerror="this.src='{{ asset('images/no_image.jpg') }}'">
                                                            </a>
                                                            <div class="media-body">
                                                                <h5 class="fs-14 font-weight-bold">
                                                                    <a href="{{ url('course/details/'.$item->id.'/'.Str::slug($item->name)) }}">{{ Str::limit(e($item->name), 25) }}</a>
                                                                </h5>
                                                                <p class="text-muted fs-13 lh-18">{{ e($item->attributes->instructor_name ?? 'Unknown Instructor') }}</p>
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <span class="text-black font-weight-semi-bold fs-14">${{ number_format($item->price, 2) }}</span>
                                                                    <button class="btn btn-danger btn-sm remove-from-cart" data-id="{{ $item->id }}">Remove</button>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                    <li class="media media-card border-top pt-2 mt-2">
                                                        <div class="media-body fs-15">
                                                            <p class="text-black font-weight-bold lh-18">Total: <span class="cart-total" id="cartSubTotal">${{ number_format($cartSubTotal, 2) }}</span></p>
                                                        </div>
                                                    </li>
                                                @else
                                                    <li class="media media-card">
                                                        <div class="media-body fs-15 text-center">
                                                            <p class="text-muted lh-18">Your cart is empty</p>
                                                        </div>
                                                    </li>
                                                @endif
                                                <li class="mt-3">
                                                    <a href="{{ route('cart') }}" class="btn theme-btn w-100 py-2">Go to Cart <i class="la la-arrow-right icon ml-1"></i></a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                                <div class="nav-right-button">
                                    <a href="{{ route('instructor.register') }}" class="btn theme-btn d-none d-lg-inline-block"><i class="la la-user-plus mr-2"></i> Become an instructor</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="off-canvas-menu custom-scrollbar-styled main-off-canvas-menu">
            <div class="off-canvas-menu-close main-menu-close icon-element icon-element-sm shadow-sm" data-toggle="tooltip" data-placement="left" title="Close menu">
                <i class="la la-times"></i>
            </div>
        </div>
        <div class="off-canvas-menu custom-scrollbar-styled category-off-canvas-menu">
            <div class="off-canvas-menu-close cat-menu-close icon-element icon-element-sm shadow-sm" data-toggle="tooltip" data-placement="left" title="Close menu">
                <i class="la la-times"></i>
            </div>
        </div>
    </header>

    <main>
        @yield('home')
    </main>

  

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @yield('scripts')

    <!-- Unified cart removal script -->
    <script>
    $(document).ready(function() {
        $('.remove-from-cart').on('click', function(e) {
            e.preventDefault();
            var courseId = $(this).data('id');
            var cartRow = $('#cart-row-' + courseId); // Cart page row
            var cartItem = $('#cart-item-' + courseId); // Header dropdown item

            $.ajax({
                url: '{{ route("cart.remove", ":id") }}'.replace(':id', courseId),
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Remove item from cart page or header
                        if (cartRow.length) {
                            cartRow.remove();
                        }
                        if (cartItem.length) {
                            cartItem.remove();
                        }

                        // Update cart count
                        if ($('#cartQty').length) {
                            $('#cartQty').text(response.cartCount);
                        }

                        // Update cart page elements if present
                        if ($('#subtotal').length && $('#total-price').length) {
                            $('#subtotal').text(response.subtotal + ' TND');
                            $('#total-price').text(response.totalPrice + ' TND');

                            if (response.cartCount === 0) {
                                $('#cart-items').html('<tr><td colspan="4" class="text-center">Cart is empty.</td></tr>');
                                $('#cart-summary').remove();
                                $('#coupon-list').remove();
                            } else if (response.couponDiscount > 0) {
                                if (!document.getElementById('coupon-discount')) {
                                    $('#subtotal').after('<p id="coupon-discount-container">Total Coupon Discount: <span id="coupon-discount">-' + response.couponDiscount + ' TND</span></p>');
                                } else {
                                    $('#coupon-discount').text('-' + response.couponDiscount + ' TND');
                                }
                            } else {
                                $('#coupon-list').remove();
                                $('#coupon-discount-container').remove();
                            }
                        }

                        // Update header dropdown
                        if ($('#cartDropdown').length) {
                            if (response.cartCount === 0) {
                                $('#cartDropdown').html(
                                    '<li class="media media-card">' +
                                    '<div class="media-body fs-15 text-center">' +
                                    '<p class="text-muted lh-18">Your cart is empty</p>' +
                                    '</div></li>' +
                                    '<li class="mt-3">' +
                                    '<a href="{{ route('cart') }}" class="btn theme-btn w-100 py-2">Go to Cart <i class="la la-arrow-right icon ml-1"></i></a>' +
                                    '</li>'
                                );
                            } else {
                                $('#cartSubTotal').text('$' + response.subtotal);
                            }
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
