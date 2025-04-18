<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <a href="{{ route('home') }}" class="logo">
                <img src="{{ asset('frontend/images/logo2.png') }}" alt="Logo" class="img-fluid" style="max-height: 70px; filter: brightness(110%);">
            </a>
        </div>
        <div>
            <h4 class="logo-text">Student</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i></div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="{{ route('dashboard') }}">
                <div class="parent-icon"><i class='bx bx-grid-alt'></i></div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>

       <li class="menu-label">Management Tools</li>

        <li>
            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <div class="parent-icon"><i class='bx bx-user'></i></div>
                <div class="menu-title">My Profile</div>
            </a>
        </li>
    
        <li>
            <a href="{{ route('user.my.courses') }}">
                <div class="parent-icon"><i class='bx bx-book'></i></div>
                <div class="menu-title">My Courses</div>
            </a>
        </li>

        <li>
            <a href="{{ route('quizzes.index') }}">
                <div class="parent-icon"><i class='bx bx-question-mark'></i></div>
                <div class="menu-title">Quiz Attempts</div>
            </a>
        </li>

        <li>
            <a href="{{ route('wishlist.index') }}">
                <div class="parent-icon"><i class='bx bx-heart'></i></div>
                <div class="menu-title">Wishlist</div>
                <span class="badge badge-info p-1 ml-2" id="wishQty">
                    {{ auth()->check() ? \App\Models\Wishlist::where('trackable_type', 'App\Models\User')->where('trackable_id', auth()->id())->count() : 0 }}
                </span>
            </a>
        </li>

        <li>
            <a href="{{ route('chat') }}">
                <div class="parent-icon"><i class='bx bx-chat'></i></div>
                <div class="menu-title">Live Chat</div>
                <span class="badge badge-info p-1 ml-2">2</span>
            </a>
        </li>

        <li>
            <a href="{{ route('user.reviews.index') }}" class="{{ request()->routeIs('user.reviews.index') ? 'active' : '' }}">
                <div class="parent-icon"><i class='bx bx-star'></i></div>
                <div class="menu-title">Reviews</div>
            </a>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-bar-chart-alt'></i></div>
                <div class="menu-title">Reports</div>
            </a>
            <ul class="mm-collapse">
                <li>
                    <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('user.reports.index') ? 'mm-active' : '' }}">
                        <i class='bx bx-radiogeons-circle'></i> My Reports
                    </a>
                </li>
                <li>
                    <a href="{{ route('report') }}" class="{{ request()->routeIs('user.report') ? 'mm-active' : '' }}">
                        <i class='bx bx-radio-circle'></i> Submit Report
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                    <div class="parent-icon"><i class='bx bx-log-out'></i></div>
                    <div class="menu-title">Logout</div>
                </a>
            </form>
        </li>
    </ul>
    <!--end navigation-->
</div>