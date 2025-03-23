<header class="header-menu-area bg-white">
  <div class="header-top pr-150px pl-150px border-bottom border-bottom-gray py-1">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <div class="header-widget">
            <ul class="generic-list-item d-flex flex-wrap align-items-center fs-14">
              <li class="d-flex align-items-center pr-3 mr-3 border-right border-right-gray"><i class="la la-phone mr-1"></i><a href="tel:+216 28-587-753"> +216 28-587-753</a></li>
              <li class="d-flex align-items-center"><i class="la la-envelope-o mr-1"></i><a href="mailto:lmspfee@gmail.com"> lmspfee@gmail.com</a></li>
            </ul>
          </div><!-- end header-widget -->
        </div><!-- end col-lg-6 -->
        <div class="col-lg-6">
          <div class="header-widget d-flex flex-wrap align-items-center justify-content-end">
            <div class="theme-picker d-flex align-items-center">
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
            <ul class="generic-list-item d-flex flex-wrap align-items-center fs-14 border-left border-left-gray pl-3 ml-3">
              @guest
              <li class="d-flex align-items-center pr-3 mr-3 border-right border-right-gray">
                <i class="la la-sign-in mr-1"></i>
                <a href="{{ route('login') }}">Login</a>
              </li>
              <li class="d-flex align-items-center">
                <i class="la la-user mr-1"></i>
                <a href="{{ route('register') }}">Register</a>
              </li>
              @else
              <li class="d-flex align-items-center pr-3 mr-3 border-right border-right-gray">
                <i class="la la-tachometer mr-1"></i>
                <a href="{{ route('dashboard') }}">Dashboard</a>
              </li>
              <li class="d-flex align-items-center">
                <i class="la la-sign-out mr-1"></i>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
                </form>
              </li>
              @endguest
            </ul>
          </div><!-- end header-widget -->
        </div><!-- end col-lg-6 -->
      </div><!-- end row -->
    </div><!-- end container-fluid -->
  </div><!-- end header-top -->

  <div class="header-menu-content pr-150px pl-150px bg-white">
    <div class="container-fluid">
      <div class="main-menu-content">
        <a href="#" class="down-button"><i class="la la-angle-down"></i></a>
        <div class="row align-items-center">
          <div class="col-lg-2">
            <div class="logo-box">
              <a href="{{ url('/') }}" class="logo"><img src="#" alt="logo"></a>
              <div class="user-btn-action">
                <div class="search-menu-toggle icon-element icon-element-sm shadow-sm mr-2" data-toggle="tooltip" data-placement="top" title="Search">
                  <i class="la la-search"></i>
                </div>
                <div class="off-canvas-menu-toggle cat-menu-toggle icon-element icon-element-sm shadow-sm mr-2" data-toggle="tooltip" data-placement="top" title="Category menu">
                  <i class="la la-th-large"></i>
                </div>
                <div class="off-canvas-menu-toggle main-menu-toggle icon-element icon-element-sm shadow-sm" data-toggle="tooltip" data-placement="top" title="Main menu">
                  <i class="la la-bars"></i>
                </div>
              </div>
            </div>
          </div><!-- end col-lg-2 -->

          @php
          $categories = App\Models\Category::orderBy('category_name', 'ASC')->get();
          $cart = Session::get('cart', []);
          $cartQty = count($cart);
          $cartSubTotal = array_sum(array_column($cart, 'price'));
          @endphp

          <div class="col-lg-10">
            <div class="menu-wrapper">
              <div class="menu-category">
                <ul>
                  <li>
                    <a href="#">Categories <i class="la la-angle-down fs-12"></i></a>
                    <ul class="cat-dropdown-menu">
                      @foreach ($categories as $cat)
                      @php
                      $subcategories = App\Models\SubCategory::where('category_id', $cat->id)->get();
                      @endphp
                      <li>
                        <a href="{{ url('category/'.$cat->id.'/'.$cat->category_slug) }}">{{ $cat->category_name }}<i class="la la-angle-right"></i></a>
                        <ul class="sub-menu">
                          @foreach ($subcategories as $subcat)
                          <li><a href="{{ url('subcategory/'.$subcat->id.'/'.$subcat->subcategory_slug) }}">{{ $subcat->subcategory_name }}</a></li>
                          @endforeach
                        </ul>
                      </li>
                      @endforeach
                    </ul>
                  </li>
                </ul>
              </div><!-- end menu-category -->
              <nav class="main-menu">
                <ul>
                <li>
                    <a href="#">Courses <i class="la la-angle-down fs-12"></i></a>
                    <ul class="dropdown-menu-item">
                        <li><a href="{{ route('course.list') }}">Course List</a></li>
                    </ul>
                </li>
                  <li>
                    <a href="#">blog</a>
                  </li>
                </ul><!-- end ul -->
              </nav><!-- end main-menu -->

              <div class="shop-cart mr-4">
                <ul>
                  <li>
                    <p class="shop-cart-btn d-flex align-items-center">
                      <i class="la la-shopping-cart fs-20"></i>
                      <span class="product-count ml-1" id="cartQty">{{ $cartQty }}</span>
                    </p>

                   <ul class="cart-dropdown-menu p-3 shadow-sm" style="min-width: 300px;">
                            @if ($cartQty > 0)
                                @foreach ($cart as $id => $item)
                                <li class="media media-card border-bottom pb-2 mb-2" id="cart-item-{{ $id }}">
                                    <a href="{{ url('course/details/'.$item['id'].'/'.Str::slug($item['name'] ?? 'course')) }}" class="media-img mr-3">
                                        <img src="{{ $item['image'] ? asset('storage/upload/course_images/thumbnail/' . $item['image']) : asset('images/no_image.jpg') }}"
                                            alt="{{ $item['name'] ?? 'Unknown Course' }}"
                                            class="lazy rounded"
                                            style="width: 60px; height: auto;"
                                            loading="lazy"
                                            onerror="this.src='{{ asset('images/no_image.jpg') }}'">
                                    </a>
                                    <div class="media-body">
                                        <h5 class="fs-14 font-weight-bold">
                                            <a href="{{ url('course/details/'.$item['id'].'/'.Str::slug($item['name'] ?? 'course')) }}">{{ Str::limit($item['name'] ?? 'N/A', 25) }}</a>
                                        </h5>
                                        <p class="text-muted fs-13 lh-18">{{ $item['instructor_name'] ?? 'Unknown Instructor' }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-black font-weight-semi-bold fs-14">${{ number_format($item['price'] ?? 0, 2) }}</span>
                                            <button type="button" class="btn btn-link text-danger fs-13 p-0" onclick="removeFromCart('{{ $id }}')">Remove</button>
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
                  @section('scripts')
                  

                </ul>
              </div><!-- end shop-cart -->
              <div class="nav-right-button">
                <a href="{{ route('instructor.register') }}" class="btn theme-btn d-none d-lg-inline-block"><i class="la la-user-plus mr-1"></i> Become an instructor</a>
              </div><!-- end nav-right-button -->
            </div><!-- end menu-wrapper -->
          </div><!-- end col-lg-10 -->
        </div><!-- end row -->
      </div>
    </div><!-- end container-fluid -->
  </div><!-- end header-menu-content -->
  <div class="off-canvas-menu custom-scrollbar-styled main-off-canvas-menu">
    <div class="off-canvas-menu-close main-menu-close icon-element icon-element-sm shadow-sm" data-toggle="tooltip" data-placement="left" title="Close menu">
      <i class="la la-times"></i>
    </div><!-- end off-canvas-menu-close -->
  </div><!-- end off-canvas-menu -->
  <div class="off-canvas-menu custom-scrollbar-styled category-off-canvas-menu">
    <div class="off-canvas-menu-close cat-menu-close icon-element icon-element-sm shadow-sm" data-toggle="tooltip" data-placement="left" title="Close menu">
      <i class="la la-times"></i>
    </div><!-- end off-canvas-menu-close -->
  </div><!-- end off-canvas-menu -->
</header><!-- end header-menu-area -->