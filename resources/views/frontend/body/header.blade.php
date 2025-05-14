@php
use App\Models\Category;
use App\Models\BlogCategory;
use App\Models\CartItem;
use App\Models\Course;
use Illuminate\Support\Str;

// Fetch categories and blog categories
$categories = Category::orderBy('category_name', 'ASC')->get();
$blogCategories = BlogCategory::orderBy('name', 'ASC')->get();

// Initialize cart variables
$cartItems = collect([]);
$cartQty = 0;
$cartSubTotal = 0;

if (auth()->check()) {
    $cartItems = CartItem::with(['cartable' => function ($query) {
        $query->select('id', 'course_name', 'course_image', 'selling_price', 'discount_price');
    }])
        ->where('user_id', auth()->id())
        ->where('cartable_type', Course::class)
        ->get()
        ->map(function ($item) {
            if (!$item->cartable) {
                return null;
            }
            $quantity = max(1, min($item->quantity ?? 1, 10)); // Ensure quantity is 1-10
            return (object) [
                'cartable_id' => $item->cartable_id,
                'cartable' => $item->cartable,
                'quantity' => $quantity,
                'price' => $item->price,
                'attributes' => [
                    'selling_price' => $item->cartable->selling_price ?? ($item->options['selling_price'] ?? 0),
                    'discount_price' => $item->cartable->discount_price ?? ($item->options['discount_price'] ?? 0),
                ],
            ];
        })->filter()
        ->keyBy('cartable_id') // Prevent duplicates by using cartable_id as key
        ->values();

    $cartQty = $cartItems->count();
    $cartSubTotal = round($cartItems->sum(fn($item) => $item->price * $item->quantity), 2);
} else {
    $tempCart = json_decode(request()->cookie('tempCart', '[]'), true);
    $tempCartItems = collect($tempCart)->filter(fn($item) => isset($item['courseId'], $item['quantity'], $item['price'], $item['selling_price'], $item['discount_price']));
    
    // Debug: Log tempCart for inspection
    // \Log::info('tempCart:', $tempCart);

    $cartItems = $tempCartItems->map(function ($cartItem) {
        $course = Course::where('id', $cartItem['courseId'])
            ->where('status', 1)
            ->select('id', 'course_name', 'course_image', 'selling_price', 'discount_price')
            ->first();
        if (!$course) {
            return null;
        }
        $sellingPrice = $course->selling_price ?? $cartItem['selling_price'] ?? 0;
        $discountPrice = $course->discount_price ?? $cartItem['discount_price'] ?? 0;
        $effectivePrice = max(0, $sellingPrice - $discountPrice);
        $quantity = max(1, min($cartItem['quantity'] ?? 1, 10)); // Ensure quantity is 1-10
        return (object) [
            'cartable_id' => $cartItem['courseId'],
            'cartable' => (object) [
                'id' => $cartItem['courseId'],
                'course_name' => $cartItem['course_name'] ?? $course->course_name,
                'course_image' => $cartItem['image'] ?? $course->course_image,
            ],
            'quantity' => $quantity,
            'price' => $effectivePrice,
            'attributes' => [
                'selling_price' => $sellingPrice,
                'discount_price' => $discountPrice,
            ],
        ];
    })->filter()
    ->keyBy('cartable_id') // Prevent duplicates by using cartable_id as key
    ->values();

    $cartQty = $cartItems->count();
    $cartSubTotal = round($cartItems->sum(fn($item) => $item->price * $item->quantity), 2);
}
@endphp

<header class="header-menu-area bg-white shadow-sm">
    <!-- Header Top -->
    <div class="header-top border-bottom py-2 px-100px">
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

    <div class="header-menu-content py-2 px-100px">
        <div class="container-fluid">
            <div class="main-menu-content">
                <a href="#" class="down-button"><i class="la la-angle-down"></i></a>
                <div class="row align-items-center">
                    <div class="col-lg-2">
                        <div class="logo-box d-flex align-items-center">
                            <a href="{{ url('/') }}" class="logo">
                                <img src="{{ $siteSettings->logo ? Storage::url($siteSettings->logo) : asset('images/default-logo.png') }}"
                                     alt="Logo" class="lazy logo-header" loading="lazy"
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
                                                    <a href="{{ url('category/'.$cat->id.'/'.$cat->category_slug) }}">{{ $cat->category_name }} <i class="la la-angle-right"></i></a>
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
                            <div class="search-bar mr-5 position-relative">
                                <form action="{{ route('course.list') }}" method="GET" class="search-form d-flex align-items-center" id="searchForm">
                                    <div class="input-group">
                                        <input type="text" name="query" class="form-control search-input" id="searchInput" placeholder="Search courses..." autocomplete="off" aria-label="Search courses">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn theme-btn search-btn" aria-label="Search">
                                                <i class="la la-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div id="searchResults" class="search-results-dropdown position-absolute bg-white shadow-sm" style="display: none; z-index: 1000; max-height: 400px; overflow-y: auto;">
                                    <ul class="list-unstyled mb-0"></ul>
                                </div>
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
                                                <li><a href="{{ route('blog.list') }}?category={{ $blogCat->id }}">{{ $blogCat->name }}</a></li>
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
                                                    <li class="media media-card border-bottom pb-2 mb-2" id="cart-item-{{ $item->cartable_id }}">
                                                        <a href="{{ url('course/details/'.$item->cartable_id.'/'.Str::slug($item->cartable->course_name)) }}" class="media-img mr-3">
                                                            <img src="{{ $item->cartable->course_image ? asset('upload/course_images/thumbnail/' . $item->cartable->course_image) : asset('upload/no_image.jpg') }}"
                                                                 alt="{{ e($item->cartable->course_name) }}"
                                                                 class="lazy rounded"
                                                                 style="width: 60px; height: auto;"
                                                                 loading="lazy"
                                                                 onerror="this.src='{{ asset('upload/no_image.jpg') }}'">
                                                        </a>
                                                        <div class="media-body">
                                                            <h5 class="fs-14 font-weight-bold">
                                                                <a href="{{ url('course/details/'.$item->cartable_id.'/'.Str::slug($item->cartable->course_name)) }}">{{ Str::limit(e($item->cartable->course_name), 25) }}</a>
                                                            </h5>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span class="text-black font-weight-semi-bold fs-14">
                                                                    TND {{ number_format($item->price, 2) }}
                                                                </span>
                                                                <button class="btn btn-danger btn-sm {{ auth()->check() ? 'remove-from-cart' : 'remove-from-cart-temp' }}" data-id="{{ $item->cartable_id }}">Remove</button>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                                <li class="media media-card border-top pt-2 mt-2">
                                                    <div class="media-body fs-15">
                                                        <p class="text-black font-weight-bold lh-18">Total: <span class="cart-total" id="cartSubTotal">TND {{ number_format($cartSubTotal, 2) }}</span></p>
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
                                <a href="{{ route('instructor.register') }}" class="btn theme-btn d-none d-lg-inline-block">
                                    <i class="la la-user-plus mr-2"></i> Become an Instructor
                                </a>
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
    </div>
</header>

<style>
    .header-menu-area {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        background-color: #ffffff;
        backdrop-filter: none;
        transition: transform 0.3s ease;
    }
    .header-menu-area.sticky-hidden {
        transform: translateY(-100%);
    }
    .header-top {
        background-color: #ffffff;
        border-bottom: 1px solid #e5e5e5;
    }
    .header-menu-content {
        background-color: #ffffff;
    }
    body {
        padding-top: 0;
    }
    .px-100px {
        padding-left: 100px;
        padding-right: 100px;
    }
    .header-top-list li i {
        font-size: 16px;
    }
    .theme-btn {
        background: #dc3545;
        color: #fff;
        transition: background-color 0.3s ease;
    }
    .theme-btn:hover {
        background: #c82333;
    }
    .menu-wrapper {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }
    .menu-category, .main-menu, .search-bar, .shop-cart {
        background-color: transparent;
    }
    .search-bar {
        flex-grow: 1;
        max-width: 450px;
        min-width: 200px;
    }
    .search-bar .search-form {
        width: 100%;
    }
    .search-bar .input-group {
        border-radius: 25px;
        overflow: hidden;
        border: 1px solid #e5e5e5;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .search-bar .input-group:hover,
    .search-bar .input-group:focus-within {
        border-color: #dc3545;
        box-shadow: 0 0 8px rgba(220, 53, 69, 0.2);
    }
    .search-bar .form-control.search-input {
        border: none;
        padding: 10px 15px;
        font-size: 14px;
        line-height: 1.5;
        background: #fff;
        color: #333;
    }
    .search-bar .form-control.search-input:focus {
        outline: none;
        box-shadow: none;
    }
    .search-bar .form-control.search-input.is-invalid {
        border-color: #dc3545;
        background: #fff;
    }
    .search-bar .theme-btn.search-btn {
        background: #dc3545;
        color: #fff;
        padding: 10px 15px;
        border: none;
        transition: background-color 0.3s ease;
    }
    .search-bar .theme-btn.search-btn:hover {
        background: #c82333;
    }
    .search-bar .theme-btn.search-btn i {
        font-size: 16px;
    }
    .search-results-dropdown {
        width: 100%;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        top: calc(100% + 8px);
        left: 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        background: #fff;
        z-index: 1001;
    }
    .search-results-dropdown ul li {
        padding: 12px 15px;
        border-bottom: 1px solid #f1f1f1;
        transition: background-color 0.2s ease;
    }
    .search-results-dropdown ul li:last-child {
        border-bottom: none;
    }
    .search-results-dropdown ul li a {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #333;
    }
    .search-results-dropdown ul li a:hover {
        background-color: #f8f9fa;
    }
    .search-results-dropdown .course-image {
        width: 70px;
        height: 40px;
        margin-right: 12px;
        border-radius: 4px;
        object-fit: cover;
    }
    .search-results-dropdown .course-title {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 4px;
        line-height: 1.4;
    }
    .search-results-dropdown .course-price {
        font-size: 13px;
        color: #dc3545;
        font-weight: 500;
    }
    @media (max-width: 991px) {
        .search-bar {
            max-width: 300px;
        }
        .px-100px {
            padding-left: 50px;
            padding-right: 50px;
        }
        .menu-wrapper {
            justify-content: space-between;
        }
    }
    @media (max-width: 767px) {
        .search-bar {
            max-width: 100%;
            margin-right: 0;
            margin-bottom: 10px;
        }
        .search-results-dropdown {
            width: 100%;
        }
        .px-100px {
            padding-left: 15px;
            padding-right: 15px;
        }
        .menu-wrapper {
            flex-direction: column;
            align-items: flex-start;
        }
        .menu-category, .main-menu, .shop-cart {
            margin-right: 0;
            margin-bottom: 10px;
        }
    }
</style>

<script>
$(document).ready(function() {
    function showNotification(message, type) {
        const $message = $('<div class="cart-message"></div>').html(`<div class="alert alert-${type}">${message}</div>`)
            .css({position: 'fixed', top: '10px', right: '10px', 'z-index': 1000});
        $('body').append($message);
        setTimeout(() => $message.fadeOut(300, () => $message.remove()), 3000);
    }

    function updateHeaderPadding() {
        const header = $('.header-menu-area');
        const headerHeight = header.outerHeight();
        $('body').css('padding-top', headerHeight + 'px');
    }

    updateHeaderPadding();
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    $(window).on('resize', debounce(updateHeaderPadding, 100));

    let lastScrollTop = 0;
    $(window).on('scroll', function() {
        const scrollTop = $(this).scrollTop();
        const header = $('.header-menu-area');
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            header.addClass('sticky-hidden');
        } else {
            header.removeClass('sticky-hidden');
        }
        lastScrollTop = scrollTop;
    });

    function debounceSearch(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    const performSearch = debounceSearch(function(query) {
        if (query.length < 2) {
            $('#searchResults').hide().find('ul').empty();
            return;
        }
        $.ajax({
            url: '{{ route("search") }}',
            method: 'GET',
            data: { query: query },
            dataType: 'json',
            beforeSend: function() {
                $('#searchResults ul').html('<li class="p-3 text-center"><i class="la la-spinner la-spin mr-2"></i>Loading...</li>');
                $('#searchResults').show();
            },
            success: function(response) {
                const $resultsList = $('#searchResults ul');
                $resultsList.empty();
                if (response.length === 0) {
                    $resultsList.append('<li class="p-3 text-muted text-center">No courses found</li>');
                } else {
                    response.forEach(function(course) {
                        $resultsList.append(`
                            <li>
                                <a href="${course.url}" class="d-flex align-items-center">
                                    <img src="${course.image}" 
                                         alt="${course.title}" 
                                         class="course-image" 
                                         loading="lazy"
                                         onerror="this.src='{{ asset('upload/no_image.jpg') }}'">
                                    <div>
                                        <div class="course-title">${course.title}</div>
                                        <div class="course-price">TND ${course.price}</div>
                                    </div>
                                </a>
                            </li>
                        `);
                    });
                }
                $('#searchResults').show();
            },
            error: function() {
                $('#searchResults ul').empty().append('<li class="p-3 text-danger text-center">An error occurred. Please try again.</li>');
                $('#searchResults').show();
            }
        });
    }, 300);

    $('#searchInput').on('input', function() {
        const query = $(this).val().trim();
        performSearch(query);
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-bar').length) {
            $('#searchResults').hide().find('ul').empty();
        }
    });

    $('#searchForm').on('submit', function(e) {
        if (!$('#searchInput').val().trim()) {
            e.preventDefault();
            $('#searchInput').addClass('is-invalid');
            setTimeout(() => $('#searchInput').removeClass('is-invalid'), 2000);
        }
    });

    $('#searchInput').on('focus', function() {
        $(this).removeClass('is-invalid');
    });

    $('#cartDropdown').on('click', '.remove-from-cart-temp', function(e) {
        e.preventDefault();
        const courseId = $(this).data('id');
        let tempCart = JSON.parse(getCookie('tempCart') || '[]');
        tempCart = tempCart.filter(item => item.courseId !== courseId);
        setCookie('tempCart', JSON.stringify(tempCart), 43200);
        const $cartItem = $('#cart-item-' + courseId);
        $cartItem.remove();
        const newCartQty = tempCart.length;
        $('#cartQty').text(newCartQty);
        let newSubTotal = tempCart.reduce((sum, item) => sum + (item.price * item.quantity), 0).toFixed(2);
        $('#cartSubTotal').text('TND ' + newSubTotal);
        if (newCartQty === 0) {
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
        $('.add-to-cart[data-course-id="' + courseId + '"]').each(function() {
            $(this).data('in-cart', false).removeAttr('data-in-cart')
                .prop('disabled', false)
                .html('<i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart');
        });
        showNotification('Removed from cart.', 'success');
    });

    $('#cartDropdown').on('click', '.remove-from-cart', function(e) {
        e.preventDefault();
        const courseId = $(this).data('id');
        const $cartItem = $('#cart-item-' + courseId);
        $.ajax({
            url: '{{ route("cart.remove", ":id") }}'.replace(':id', courseId),
            method: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $cartItem.remove();
                    $('#cartQty').text(response.cartCount);
                    $('#cartSubTotal').text('TND ' + response.subtotal);
                    if (response.cartCount === 0) {
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
                    $('.add-to-cart[data-course-id="' + courseId + '"]').each(function() {
                        $(this).data('in-cart', false).removeAttr('data-in-cart')
                            .prop('disabled', false)
                            .html('<i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart');
                    });
                    showNotification(response.message, 'success');
                } else {
                    showNotification(response.message || 'Action completed.', 'info');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                showNotification(response.message || 'An error occurred.', 'danger');
            }
        });
    });
});
</script>