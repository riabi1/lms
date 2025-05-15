@php
use App\Models\Category;
use App\Models\BlogCategory;

// Fetch categories and blog categories
$categories = Category::orderBy('category_name', 'ASC')->get();
$blogCategories = BlogCategory::orderBy('name', 'ASC')->get();
@endphp

<header class="header-menu-area bg-white shadow-sm">
  <!-- Header Top -->
  <div class="header-top border-bottom py-2 px-100px">
    <div class="container-fluid">
      <div class="row align-items-center">
        <!-- Contact Info -->
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
        <!-- Theme Picker & Auth Links -->
        <div class="col-lg-6">
          <div class="header-widget d-flex flex-wrap align-items-center justify-content-end">
            <div class="theme-picker d-flex align-items-center mrpline-block">
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

  <!-- Main Header Content -->
  <div class="header-menu-content py-2 px-100px">
    <div class="container-fluid">
      <div class="main-menu-content">
        <a href="#" class="down-button"><i class="la la-angle-down"></i></a>
        <div class="row align-items-center">
          <!-- Logo & Menu Toggles -->
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
          <!-- Menu, Search, Cart & Instructor Button -->
          <div class="col-lg-10">
            <div class="menu-wrapper d-flex align-items-center">
              <!-- Category Menu -->
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
              <!-- Search Bar -->
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
                <div id="searchResults" class="search-results-dropdown position-absolute bg-white shadow-sm" style="display: none; max-height: 400px; overflow-y: auto;">
                  <ul class="list-unstyled mb-0"></ul>
                </div>
              </div>
              <!-- Main Navigation -->
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
              <!-- Shopping Cart -->
              <div class="shop-cart mr-5">
                <ul>
                  <li>
                    <p class="shop-cart-btn d-flex align-items-center">
                      <i class="la la-shopping-cart fs-20"></i>
                      <span class="product-count ml-2" id="cartQty">0</span>
                    </p>
                    <ul class="cart-dropdown-menu p-3 shadow-sm" style="min-width: 300px;" id="cartDropdown">
                      <li class="media media-card">
                        <div class="media-body fs-15 text-center">
                          <p class="text-muted lh-18">Your cart is empty</p>
                        </div>
                      </li>
                      <li class="mt-3">
                        <a href="{{ route('cart') }}" class="btn theme-btn w-100 py-2">Go to Cart <i class="la la-arrow-right icon ml-1"></i></a>
                      </li>
                    </ul>
                  </li>
                </ul>
              </div>
              <!-- Become an Instructor -->
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
  </div>

  <!-- Off-Canvas Menus -->
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

<style>
  /* General Header Styles */
  .header-menu-area {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    background-color: #ffffff;
    min-height: 120px;
    max-height: 120px;
    transition: transform 0.3s ease;
  }

  .header-menu-area.sticky-hidden {
    transform: translateY(-100%);
  }

  .header-top {
    background-color: #ffffff;
    border-bottom: 1px solid #e5e5e5;
    height: 40px;
    line-height: 40px;
  }

  .header-menu-content {
    background-color: #ffffff;
    height: 80px;
    display: flex;
    align-items: center;
  }

  body {
    padding-top: 120px;
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

  .logo-box img.logo-header {
    max-height: 60px;
    width: auto;
    object-fit: contain;
  }

  /* Menu and Search Bar */
  .menu-wrapper {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    position: relative;
  }

  .menu-category,
  .main-menu,
  .search-bar,
  .shop-cart {
    background-color: transparent;
    position: relative;
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

  /* Search Results Dropdown */
  .search-results-dropdown {
    width: 100%;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    top: calc(100% + 8px);
    left: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    background: #fff;
    z-index: 10000;
    max-height: 400px;
    overflow-y: auto;
    position: absolute;
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
    width: 50px;
    height: auto;
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

  /* Dropdown Menus */
  .cat-dropdown-menu,
  .dropdown-menu-item,
  .cart-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 10000;
    background: #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-radius: 4px;
    min-width: 200px;
    padding: 10px 0;
    visibility: hidden;
    opacity: 0;
    transition: visibility 0.2s, opacity 0.2s ease;
  }

  .menu-category li:hover > .cat-dropdown-menu,
  .main-menu li:hover > .dropdown-menu-item,
  .shop-cart li:hover > .cart-dropdown-menu {
    visibility: visible;
    opacity: 1;
  }

  .cat-dropdown-menu li,
  .dropdown-menu-item li,
  .cart-dropdown-menu li {
    position: relative;
  }

  .cat-dropdown-menu .sub-menu {
    position: absolute;
    top: 0;
    left: 100%;
    z-index: 10000;
    background: #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-radius: 4px;
    min-width: 200px;
    padding: 10px 0;
    visibility: hidden;
    opacity: 0;
    transition: visibility 0.2s, opacity 0.2s ease;
  }

  .cat-dropdown-menu li:hover > .sub-menu {
    visibility: visible;
    opacity: 1;
  }

  .cat-dropdown-menu li a,
  .dropdown-menu-item li a {
    display: block;
    padding: 8px 20px;
    color: #333;
    text-decoration: none;
    font-size: 14px;
    transition: background-color 0.2s ease;
  }

  .cat-dropdown-menu li a:hover,
  .dropdown-menu-item li a:hover {
    background-color: #f8f9fa;
  }

  /* Cart Dropdown Specific Styles */
  .cart-dropdown-menu {
    min-width: 300px;
    padding: 15px;
  }

  .cart-dropdown-menu .media-card {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #f1f1f1;
  }

  .cart-dropdown-menu .media-card:last-child {
    border-bottom: none;
  }

  .cart-dropdown-menu .media-body {
    flex: 1;
  }

  .cart-dropdown-menu .cart-item-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    margin-right: 10px;
    border-radius: 4px;
  }

  .cart-dropdown-menu .cart-item-title {
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 5px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .cart-dropdown-menu .cart-item-price {
    font-size: 13px;
    color: #dc3545;
    font-weight: 500;
  }

  .cart-dropdown-menu .cart-item-remove {
    background: transparent;
    border: none;
    color: #dc3545;
    font-size: 18px;
    cursor: pointer;
    padding: 5px;
    transition: color 0.2s ease;
  }

  .cart-dropdown-menu .cart-item-remove:hover {
    color: #c82333;
  }

  .cart-dropdown-menu .cart-subtotal {
    font-weight: bold;
    margin: 10px 0;
    text-align: right;
  }

  .cart-dropdown-menu .theme-btn {
    display: block;
    text-align: center;
  }

  .shop-cart-btn {
    cursor: pointer;
    color: #333;
    font-size: 14px;
  }

  .shop-cart-btn .product-count {
    background: #dc3545;
    color: #fff;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    font-size: 12px;
  }

  /* Ensure no overflow clipping */
  .header-menu-area,
  .header-menu-content,
  .menu-wrapper {
    overflow: visible !important;
  }

  /* Responsive Adjustments */
  @media (max-width: 991px) {
    .header-menu-area {
      min-height: 120px;
      max-height: 120px;
      z-index: 1000;
    }

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

    .cat-dropdown-menu,
    .dropdown-menu-item,
    .cart-dropdown-menu {
      width: 100%;
      left: 0;
      z-index: 10000;
    }

    .cat-dropdown-menu .sub-menu {
      left: 0;
      top: 100%;
    }
  }

  @media (max-width: 767px) {
    .header-menu-area {
      min-height: 120px;
      max-height: 120px;
      z-index: 1000;
    }

    .search-bar {
      max-width: 100%;
      margin-right: 0;
      margin-bottom: 10px;
    }

    .search-results-dropdown {
      width: 100%;
      z-index: 10000;
    }

    .px-100px {
      padding-left: 15px;
      padding-right: 15px;
    }

    .menu-wrapper {
      flex-direction: column;
      align-items: flex-start;
    }

    .menu-category,
    .main-menu,
    .shop-cart {
      margin-right: 0;
      margin-bottom: 10px;
    }

    .cat-dropdown-menu,
    .dropdown-menu-item,
    .cart-dropdown-menu {
      position: static;
      width: 100%;
      z-index: 10000;
      box-shadow: none;
      border: 1px solid #e5e5e5;
    }

    .cat-dropdown-menu .sub-menu {
      position: static;
      width: 100%;
      box-shadow: none;
      border: none;
      padding-left: 20px;
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

  function updateCartCount(count) {
    $('#cartQty').text(count);
  }

  function loadCartItems() {
    $.ajax({
        url: '{{ route("cart.items") }}',
        method: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#cartDropdown').html(`
                <li class="media media-card">
                    <div class="media-body fs-15 text-center">
                        <p class="text-muted lh-18"><i class="la la-spinner la-spin mr-2"></i>Loading cart...</p>
                    </div>
                </li>
            `);
        },
        success: function(response) {
            const $cartDropdown = $('#cartDropdown');
            $cartDropdown.empty();
            updateCartCount(response.cartCount);

            if (response.cartItems.length === 0) {
                $cartDropdown.html(`
                    <li class="media media-card">
                        <div class="media-body fs-15 text-center">
                            <p class="text-muted lh-18">Your cart is empty</p>
                        </div>
                    </li>
                    <li class="mt-3">
                        <a href="{{ route('cart') }}" class="btn theme-btn w-100 py-2">Go to Cart <i class="la la-arrow-right icon ml-1"></i></a>
                    </li>
                `);
            } else {
                let cartItemsHtml = '';
                response.cartItems.forEach(function(item) {
                    cartItemsHtml += `
                        <li class="media media-card">
                            <img src="${item.image}" alt="${item.name}" class="cart-item-image" onerror="this.src='{{ asset('images/default-course.jpg') }}'">
                            <div class="media-body">
                                <h5 class="cart-item-title">${item.name}</h5>
                                <p class="cart-item-price">TND ${item.price}</p>
                            </div>
                            <button class="cart-item-remove" data-id="${item.id}" title="Remove item">
                                <i class="la la-trash"></i>
                            </button>
                        </li>
                    `;
                });
                $cartDropdown.html(`
                    ${cartItemsHtml}
                    <li class="cart-subtotal">
                        Subtotal: <span>TND ${response.cartSubTotal}</span>
                    </li>
                    <li class="mt-3">
                        <a href="{{ route('cart') }}" class="btn theme-btn w-100 py-2">Go to Cart <i class="la la-arrow-right icon ml-1"></i></a>
                    </li>
                    <li class="mt-2">
                        <a href="{{ route('checkout.create') }}" class="btn theme-btn w-100 py-2">Checkout <i class="la la-check icon ml-1"></i></a>
                    </li>
                `);
            }
        },
        error: function() {
            $('#cartDropdown').html(`
                <li class="media media-card">
                    <div class="media-body fs-15 text-center">
                        <p class="text-danger lh-18">Error loading cart</p>
                    </div>
                </li>
                <li class="mt-3">
                    <a href="{{ route('cart') }}" class="btn theme-btn w-100 py-2">Go to Cart <i class="la la-arrow-right icon ml-1"></i></a>
                </li>
            `);
        }
    });
}

  // Remove item from cart
  $(document).on('click', '.cart-item-remove', function() {
    const itemId = $(this).data('id');
    const $cartItem = $(this).closest('.media-card');

    $.ajax({
      url: '{{ route("cart.remove", ":id") }}'.replace(':id', itemId),
      method: 'DELETE',
      data: {
        _token: '{{ csrf_token() }}'
      },
      beforeSend: function() {
        $cartItem.find('.cart-item-remove').prop('disabled', true).html('<i class="la la-spinner la-spin"></i>');
      },
      success: function(response) {
        showNotification(response.message, 'success');
        updateCartCount(response.cartCount);
        loadCartItems(); // Reload cart to update subtotal and items
      },
      error: function(xhr) {
        const message = xhr.responseJSON?.message || 'Error removing item from cart';
        showNotification(message, 'danger');
        $cartItem.find('.cart-item-remove').prop('disabled', false).html('<i class="la la-trash"></i>');
      }
    });
  });

  // Load cart items on page load
  loadCartItems();

  // Update cart on add-to-cart success
  $(document).on('cartUpdated', function(e, data) {
    updateCartCount(data.cartCount);
    loadCartItems();
  });

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

  // Hide header on scroll down, show on scroll up
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
});
</script>