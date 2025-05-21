@extends('admin.layout.Admin_layout')

@section('admin')
<style>
    .gradient-primary {
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        color: #1e40af;
    }
    .gradient-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #064e3b;
    }
    .gradient-warning {
        background: linear-gradient(135deg, #ffedd5, #fed7aa);
        color: #7c2d12;
    }
    .gradient-info {
        background: linear-gradient(135deg, #fbcfe8, #f9a8d4);
        color: #831843;
    }
    .metric-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-radius: 12px;
        overflow: hidden;
    }
    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    .recommendation-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 8px;
        transition: background 0.2s;
    }
    .recommendation-item:hover {
        background: #f3f4f6;
    }
    .chart-card {
        border-radius: 15px;
        background: #ffffff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
    .table thead {
        background: #fbcfe8;
        color: #831843;
    }
    .table tbody tr:nth-child(even) {
        background: #f3f4f6;
    }
    .table tbody tr:hover {
        background: #e0f2fe;
    }
    .metric-value {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    .chart-container {
        position: relative;
        padding: 15px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    .chart-container canvas {
        max-height: 350px !important;
        width: 100% !important;
        display: block !important;
        visibility: visible !important;
    }
</style>

<div class="container py-4">
    <div class="card p-4 shadow border-0" style="background: #f3f4f6;">
        <h3 class="mb-4 text-primary">Admin Dashboard</h3>

        <!-- Debug: Check if $admin is defined -->
        @if(!$admin)
            <div class="alert alert-danger mb-4">
                Error: Admin not authenticated. Please <a href="{{ route('login') }}" class="alert-link">log in</a>.
            </div>
        @endif

        <!-- Email Verification Alert -->
        @if($admin && !$admin->hasVerifiedEmail())
            <div class="alert alert-warning mb-4 d-flex align-items-center" style="background-color: #ffedd5; border-color: #fed7aa; color: #7c2d12;">
                <span class="me-2">⚠️</span>
                Your email is not verified. Please check your inbox or
                <a href="{{ route('verification.send') }}" class="alert-link">resend verification email</a>.
            </div>
        @endif

        <!-- Admin Profile Section -->
        <div class="mb-5">
            <div class="d-flex align-items-center">
                <img class="rounded-circle me-3 border p-1" style="border-color: #fbcfe8;"
                    src="{{ $admin && $admin->photo ? asset('upload/admin_images/' . $admin->photo) : asset('upload/no_image.jpg') }}"
                    alt="{{ $admin ? $admin->name : 'Admin' }}'s Profile"
                    style="width: 100px; height: 100px; object-fit: cover;">
                <div>
                    <h2 class="mb-1 text-dark fw-bold">Hello, {{ $admin ? $admin->name : 'Admin' }}</h2>
                    <p class="text-muted">Here's a snapshot of your platform's performance.</p>
                </div>
            </div>
        </div>

        @if(session('status'))
            <div class="alert alert-success mt-3">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mt-3">{{ session('error') }}</div>
        @endif

        <!-- Key Metrics -->
        <div class="row text-center mb-5">
            <div class="col-md-3 mb-4">
                <div class="card metric-card shadow p-4 gradient-primary">
                    <h5 class="fw-bold">👥 Total Users</h5>
                    <div class="metric-value">{{ $totalUsers ?? 0 }}</div>
                    <small>Active: {{ $activeUsers ?? 0 }} ({{ $userGrowth ?? 0 }} new this month)</small>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card metric-card shadow p-4 gradient-success">
                    <h5 class="fw-bold">📚 Total Courses</h5>
                    <div class="metric-value">{{ $totalCourses ?? 0 }}</div>
                    <small>{{ $totalEnrollments ?? 0 }} enrollments</small>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card metric-card shadow p-4 gradient-warning">
                    <h5 class="fw-bold">💸 Total Revenue</h5>
                    <div class="metric-value">${{ number_format($totalRevenue ?? 0, 2) }}</div>
                    <small>${{ number_format($revenueLast30Days ?? 0, 2) }} last 30 days</small>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card metric-card shadow p-4 gradient-info">
                    <h5 class="fw-bold">⭐ Avg. Course Rating</h5>
                    <div class="metric-value">{{ number_format($averageCourseRating ?? 0, 1) }}/5</div>
                    <small>{{ number_format($averageCompletionRate ?? 0, 1) }}% completion rate</small>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card metric-card shadow p-4 gradient-primary">
                    <h5 class="fw-bold">🎟️ Coupon Usage</h5>
                    <div class="metric-value">{{ number_format($couponUsageRate ?? 0, 1) }}%</div>
                    <small>{{ $usedCoupons ?? 0 }} of {{ $totalCoupons ?? 0 }} used</small>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-5">
            <div class="col-md-6 mb-4">
                <div class="card p-4 chart-card chart-container">
                    <h4 class="mb-4 fw-bold" style="color: #60a5fa;">📈 Enrollment Trends</h4>
                    <canvas id="enrollmentChart"></canvas>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card p-4 chart-card chart-container">
                    <h4 class="mb-4 fw-bold" style="color: #60a5fa;">💰 Revenue Trends</h4>
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card p-4 chart-card chart-container">
                    <h4 class="mb-4 fw-bold" style="color: #60a5fa;">📊 Course Completion Rates</h4>
                    <canvas id="completionChart"></canvas>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card p-4 chart-card chart-container">
                    <h4 class="mb-4 fw-bold" style="color: #60a5fa;">💬 Blog Engagement</h4>
                    <canvas id="blogChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Actionable Recommendations -->
        @if(!empty($recommendations))
            <div class="card p-4 mb-5 chart-card">
                <h4 class="mb-4 fw-bold" style="color: #10b981;">🚀 Platform Improvement Suggestions</h4>
                <ul class="list-group list-group-flush">
                    @foreach($recommendations as $recommendation)
                        <li class="list-group-item recommendation-item mb-2" style="background: #f3f4f6;">
                            <span style="color: #f472b6;">🔥</span>
                            {{ $recommendation }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Platform Health -->
        <div class="card p-4 mb-5 chart-card">
            <h4 class="mb-4 fw-bold" style="color: #10b981;">🛠️ Platform Health</h4>
            <p><strong>Pending Reports:</strong> {{ $pendingReports ?? 0 }}</p>
            <p><strong>Top Report Categories:</strong></p>
            <ul>
                @foreach($commonReportCategories ?? [] as $category)
                    <li>{{ $category->reportCategory ? $category->reportCategory->name : 'Uncategorized' }}: {{ $category->report_count }} reports</li>
                @endforeach
            </ul>
            <p><strong>Blog Engagement:</strong> {{ $blogEngagement ?? 0 }} comments</p>
            <p><strong>Quiz Participation:</strong> {{ $quizParticipation ?? 0 }} attempts</p>
        </div>

        <!-- Top Performing Courses -->
        <div class="card p-4 mb-5 chart-card">
            <h4 class="mb-4 fw-bold" style="color: #10b981;">🏆 Top Performing Courses</h4>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Enrollments</th>
                        <th>Revenue</th>
                        <th>Avg. Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topCourses ?? [] as $course)
                        <tr>
                            <td><span class="me-2" style="color: #f472b6;">🎓</span>{{ $course->course_title }}</td>
                            <td>{{ $course->enrollments }}</td>
                            <td>${{ number_format($course->revenue, 2) }}</td>
                            <td>{{ $course->average_rating ? number_format($course->average_rating, 1) : 'N/A' }}/5 ⭐</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Course Completion Rates Table -->
        <div class="card p-4 mb-5 chart-card">
            <h4 class="mb-4 fw-bold" style="color: #10b981;">📊 Course Completion Rates</h4>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Completion Rate (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courseCompletionRates ?? [] as $course)
                        <tr>
                            <td><span class="me-2" style="color: #f472b6;">🎓</span>{{ $course->course_title }}</td>
                            <td>{{ round($course->completion_rate, 2) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Blog Engagement Table -->
        <div class="card p-4 chart-card">
            <h4 class="mb-4 fw-bold" style="color: #10b981;">💬 Blog Engagement by Post</h4>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Post</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($blogEngagementByPost ?? [] as $post)
                        <tr>
                            <td><span class="me-2" style="color: #f472b6;">📝</span>{{ $post->title }}</td>
                            <td>{{ $post->comment_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Debug data
        console.log('Enrollment Labels:', @json($enrollmentLabels ?? []));
        console.log('Enrollment Data:', @json($enrollmentData ?? []));
        console.log('Revenue Labels:', @json($revenueLabels ?? []));
        console.log('Revenue Data:', @json($revenueData ?? []));
        console.log('Completion Labels:', @json($courseCompletionRates->pluck('course_title') ?? []));
        console.log('Completion Data:', @json($courseCompletionRates->pluck('completion_rate') ?? []));
        console.log('Blog Labels:', @json($blogEngagementByPost->pluck('title') ?? []));
        console.log('Blog Data:', @json($blogEngagementByPost->pluck('comment_count') ?? []));

        // Enrollment Chart
        const enrollmentCtx = document.getElementById('enrollmentChart');
        if (!enrollmentCtx) {
            console.error('Enrollment chart canvas not found');
        } else if (!@json($enrollmentLabels ?? []) || !@json($enrollmentData ?? [])) {
            console.error('Enrollment chart data is missing');
        } else {
            new Chart(enrollmentCtx, {
                type: 'line',
                data: {
                    labels: @json($enrollmentLabels ?? []),
                    datasets: [{
                        label: 'Enrollments',
                        data: @json($enrollmentData ?? []),
                        borderColor: '#60a5fa',
                        backgroundColor: 'rgba(96, 165, 250, 0.2)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#60a5fa',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        x: {
                            title: { display: true, text: 'Month' },
                            ticks: { color: '#333333' },
                            grid: { display: false }
                        },
                        y: {
                            title: { display: true, text: 'Enrollments' },
                            ticks: { color: '#333333', beginAtZero: true },
                            grid: { color: '#e5e7eb' }
                        }
                    }
                }
            });
        }

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart');
        if (!revenueCtx) {
            console.error('Revenue chart canvas not found');
        } else if (!@json($revenueLabels ?? []) || !@json($revenueData ?? [])) {
            console.error('Revenue chart data is missing');
        } else {
            new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: @json($revenueLabels ?? []),
                    datasets: [{
                        label: 'Revenue ($)',
                        data: @json($revenueData ?? []),
                        backgroundColor: '#10b981',
                        borderColor: '#10b981',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        x: {
                            title: { display: true, text: 'Month' },
                            ticks: { color: '#333333' },
                            grid: { display: false }
                        },
                        y: {
                            title: { display: true, text: 'Revenue ($)' },
                            ticks: { color: '#333333', beginAtZero: true },
                            grid: { color: '#e5e7eb' }
                        }
                    }
                }
            });
        }

        // Course Completion Chart
        const completionCtx = document.getElementById('completionChart');
        if (!completionCtx) {
            console.error('Completion chart canvas not found');
        } else if (!@json($courseCompletionRates->pluck('course_title') ?? []) || !@json($courseCompletionRates->pluck('completion_rate') ?? [])) {
            console.error('Completion chart data is missing');
        } else {
            new Chart(completionCtx, {
                type: 'bar',
                data: {
                    labels: @json($courseCompletionRates->pluck('course_title') ?? []),
                    datasets: [{
                        label: 'Completion Rate (%)',
                        data: @json($courseCompletionRates->pluck('completion_rate') ?? []),
                        backgroundColor: @json($courseCompletionRates->map(function($course) { return $course->completion_rate < 50 ? '#F44336' : '#4CAF50'; }) ?? []),
                        borderColor: @json($courseCompletionRates->map(function($course) { return $course->completion_rate < 50 ? '#D32F2F' : '#388E3C'; }) ?? []),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        x: {
                            title: { display: true, text: 'Course' },
                            ticks: { color: '#333333' },
                            grid: { display: false }
                        },
                        y: {
                            title: { display: true, text: 'Completion Rate (%)' },
                            ticks: { color: '#333333', beginAtZero: true, max: 100 },
                            grid: { color: '#e5e7eb' }
                        }
                    }
                }
            });
        }

        // Blog Engagement Chart
        const blogCtx = document.getElementById('blogChart');
        if (!blogCtx) {
            console.error('Blog chart canvas not found');
        } else if (!@json($blogEngagementByPost->pluck('title') ?? []) || !@json($blogEngagementByPost->pluck('comment_count') ?? [])) {
            console.error('Blog chart data is missing');
        } else {
            new Chart(blogCtx, {
                type: 'bar',
                data: {
                    labels: @json($blogEngagementByPost->pluck('title') ?? []),
                    datasets: [{
                        label: 'Comments',
                        data: @json($blogEngagementByPost->pluck('comment_count') ?? []),
                        backgroundColor: @json($blogEngagementByPost->map(function($post) { return $post->comment_count == 0 ? '#F44336' : '#4CAF50'; }) ?? []),
                        borderColor: @json($blogEngagementByPost->map(function($post) { return $post->comment_count == 0 ? '#D32F2F' : '#388E3C'; }) ?? []),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        x: {
                            title: { display: true, text: 'Blog Post' },
                            ticks: { color: '#333333' },
                            grid: { display: false }
                        },
                        y: {
                            title: { display: true, text: 'Comments' },
                            ticks: { color: '#333333', beginAtZero: true },
                            grid: { color: '#e5e7eb' }
                        }
                    }
                }
            });
        }
    </script>
@endpush
@endsection