@extends('User.layout.user_layout')

@section('title')
User Dashboard | Easy Learning
@endsection

@section('userdashboard')
<style>
    .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .alert-warning {
        background-color: #fef3c7;
        border-color: #fde68a;
        color: #92400e;
    }
    .alert-success {
        background-color: #d1fae5;
        border-color: #6ee7b7;
        color: #065f46;
    }
    .alert-danger {
        background-color: #fee2e2;
        border-color: #f87171;
        color: #991b1b;
    }
    .stat-card {
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    }
    .btn-primary {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    .btn-primary:hover {
        background-color: #4338ca;
        border-color: #4338ca;
    }
    .motivational-message {
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        color: #1e1b4b;
        font-weight: 500;
    }
    /* Chat Widget Styles */
    .chat-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #4f46e5;
        color: white;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        z-index: 1000;
    }
    .chat-button:hover {
        background-color: #4338ca;
    }
    .chat-window {
        position: fixed;
        bottom: 100px;
        right: 20px;
        width: 350px;
        height: 400px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: none;
        flex-direction: column;
        z-index: 1000;
    }
    .chat-header {
        background: linear-gradient(135deg, #4f46e5, #818cf8);
        color: white;
        padding: 10px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chat-body {
        flex: 1;
        padding: 10px;
        overflow-y: auto;
        background: #f9fafb;
    }
    .chat-message {
        margin: 5px 0;
        padding: 8px 12px;
        border-radius: 8px;
        max-width: 80%;
    }
    .chat-message.user {
        background: #4f46e5;
        color: white;
        margin-left: auto;
    }
    .chat-message.bot {
        background: #e5e7eb;
        color: #1f2937;
    }
    .chat-input {
        padding: 10px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 10px;
    }
    .chat-input input {
        flex: 1;
        padding: 8px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        outline: none;
    }
    .chat-input button {
        background: #4f46e5;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
    }
    .chat-input button:hover {
        background: #4338ca;
    }
</style>

<div class="container py-4">
    <div class="card p-4">
        <h3 class="mb-4 text-primary">User Dashboard</h3>

        <?php $user = Auth::guard('web')->user(); ?>

        @if (!$user->hasVerifiedEmail())
        <div class="alert alert-warning mb-4">
            Your email is not verified. Please check your inbox or
            <a href="{{ route('verification.send') }}" class="alert-link">resend verification email</a>.
        </div>
        @endif

        <div class="mb-3">
            <div class="d-flex align-items-center">
                <img class="rounded-circle me-3 shadow-sm"
                        src="{{ $user ? ($user->photo ? asset('upload/user_images/' . $user->photo) : asset('upload/no_image.jpg')) : asset('upload/no_image.jpg') }}"
                    alt="{{ $user->name }}'s Profile" style="width: 100px; height: 100px; object-fit: cover;">
                <div>
                    <h2 class="text-dark">Hello, {{ $user->name }}! 👋</h2>
                    <p class="motivational-message">{{ $message }}</p>
                </div>
            </div>
        </div>

        @if (session('status'))
        <div class="alert alert-success mt-3">{{ session('status') }}</div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
        @endif
    </div>

    <!-- Stats Overview -->
    <div class="row mt-5">
        <div class="col-md-4">
            <div class="card p-4 text-center stat-card" style="background: linear-gradient(135deg, #a5b4fc, #818cf8); color: #1e1b4b;">
                <h5>Courses Enrolled 📚</h5>
                <h3>{{ $enrolledCourses->count() }}</h3>
                <p class="mt-2">
                    @if ($enrolledCourses->count() > 0)
                        Great start! Explore your {{ $enrolledCourses->count() }} courses! 🌟
                    @else
                        Enroll in a course to begin! 🚀
                    @endif
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 text-center stat-card" style="background: linear-gradient(135deg, #f9a8d4, #f472b6); color: #4c1d95;">
                <h5>Overall Completion 🎓</h5>
                <h3>{{ $overallCompletion }}%</h3>
                <p class="mt-2">
                    @if ($overallCompletion > 0)
                        You're making progress! Keep it up! 💪
                    @else
                        Start a course to see your progress soar! 📈
                    @endif
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 text-center stat-card" style="background: linear-gradient(135deg, #6ee7b7, #34d399); color: #064e3b;">
                <h5>Quiz Pass Rate 🥳</h5>
                <h3>{{ $passRate }}%</h3>
                <p class="mt-2">
                    @if ($passRate >= 75)
                        You're acing those quizzes! Keep shining! 🌟
                    @elseif ($passRate > 0)
                        Solid effort! Review and boost that score! 📝
                    @else
                        Try a quiz to test your skills! 🎯
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Enrollment Trends Chart -->
    <div class="card p-4 mt-5">
        <h4 class="mb-4 text-indigo-600">Course Enrollments Over Last 6 Months 📅</h4>
        <div id="enrollmentsChart"></div>
    </div>

    <!-- Course Completion Chart -->
    <div class="card p-4 mt-5">
        <h4 class="mb-4 text-pink-600">Course Completion Progress 📊</h4>
        <div id="completionChart" style="height: 400px;"></div>
    </div>

    <!-- Quiz Performance Chart -->
    <div class="card p-4 mt-5">
        <h4 class="mb-4 text-purple-600">Quiz Performance (Pass vs Fail) 🎯</h4>
        <div id="quizPerformanceChart" style="height: 250px;"></div>
    </div>

    <!-- Category Engagement Chart -->
    <div class="card p-4 mt-5">
        <h4 class="mb-4 text-teal-600">Category Engagement 🗂️</h4>
        <div id="categoryEngagementChart" style="height: 300px;"></div>
    </div>

    <!-- Recommended Courses -->
    @if ($recommendedCourses->isNotEmpty())
    <div class="card p-4 mt-5">
        <h4 class="mb-4 text-blue-600">Recommended Courses for You 🌟</h4>
        <p class="motivational-message">
            Based on your interests, here are some courses to fuel your learning journey! 🚀
        </p>
        <div class="row">
            @foreach ($recommendedCourses as $course)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img src="{{ $course->course_image ? asset('upload/course_images/thumbnail/' . $course->course_image) : asset('upload/no_image.jpg') }}"
                         class="card-img-top" alt="{{ $course->course_title }}"
                         style="height: 150px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title text-dark">{{ $course->course_title }}</h5>
                        <p class="card-text text-muted">{!! \Illuminate\Support\Str::limit(strip_tags($course->description), 100) !!}</p>
                        <a href="{{ route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug ?? \Illuminate\Support\Str::slug($course->course_title)]) }}"
                           class="btn btn-primary">View Course 📚</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Motivational Call-to-Action -->
    <div class="card p-4 mt-5">
        <h4 class="mb-4 text-indigo-600">What's Next? 🚀</h4>
        <div class="motivational-message">
            @if ($enrolledCourses->count() > 0 && $overallCompletion < 100)
                Keep learning! Pick up where you left off in your {{ $enrolledCourses->count() }} courses! 📖
            @elseif ($recommendedCourses->isNotEmpty())
                Explore new topics! Check out your recommended courses to expand your skills! 🌍
            @else
                Start your journey! Browse courses and enroll to unlock new knowledge! 🎓
            @endif
            <div class="mt-3">
                <a href="{{ route('user.my.courses') }}" class="btn btn-primary">Browse Courses 🌟</a>
            </div>
        </div>
    </div>

    <!-- Chat Widget -->
    <div class="chat-button" id="chatButton">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
            <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/>
        </svg>
    </div>
    <div class="chat-window" id="chatWindow">
        <div class="chat-header">
            <span>Learning Assistant</span>
            <button class="close-chat" id="closeChat">✖</button>
        </div>
        <div class="chat-body" id="chatBody">
            <div class="chat-message bot">Hello! I'm here to help with your learning. Ask about courses, progress, or study tips! 😊</div>
        </div>
        <div class="chat-input">
            <input type="text" id="chatInput" placeholder="Type your question...">
            <button id="sendMessage">Send</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<!-- DataTables (jQuery already included in layout) -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize DataTable for Enrolled Courses
        $('#enrolledCoursesTable').DataTable({
            "order": [[0, "asc"]],
            "pageLength": 10
        });

        // Vibrant color palette for charts
        const chartColors = ['#4f46e5', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4'];

        // Enrollment Trends Chart
        fetch('{{ route('enrollmenttrends') }}')
            .then(res => res.json())
            .then(data => {
                new ApexCharts(document.querySelector("#enrollmentsChart"), {
                    chart: { type: 'line', height: 350 },
                    series: [{ name: 'Enrollments', data: data.data }],
                    xaxis: { categories: data.labels },
                    stroke: { curve: 'smooth', width: 3 },
                    colors: [chartColors[0]],
                    grid: { borderColor: '#e5e7eb' },
                    tooltip: { theme: 'light' }
                }).render();
            });

        // Course Completion Chart
        fetch('{{ route('completiondata') }}')
            .then(res => res.json())
            .then(data => {
                new ApexCharts(document.querySelector("#completionChart"), {
                    chart: { type: 'bar', height: 400 },
                    series: [{ name: 'Completion %', data: data.data }],
                    xaxis: { categories: data.labels },
                    yaxis: { max: 100, title: { text: 'Percentage (%)' } },
                    colors: [chartColors[1]],
                    plotOptions: { bar: { borderRadius: 4 } },
                    grid: { borderColor: '#e5e7eb' },
                    tooltip: { theme: 'light' }
                }).render();
            });

        // Quiz Performance Chart
        fetch('{{ route('quizperformance') }}')
            .then(res => res.json())
            .then(data => {
                new ApexCharts(document.querySelector("#quizPerformanceChart"), {
                    chart: { type: 'donut', height: 250 },
                    series: data.data,
                    labels: data.labels,
                    colors: [chartColors[2], chartColors[3]],
                    plotOptions: { pie: { donut: { size: '60%' } } },
                    legend: { position: 'bottom' },
                    tooltip: { theme: 'light' }
                }).render();
            });

        // Category Engagement Chart
        fetch('{{ route('categoryengagement') }}')
            .then(res => res.json())
            .then(data => {
                new ApexCharts(document.querySelector("#categoryEngagementChart"), {
                    chart: { type: 'bar', height: 300 },
                    series: [{ name: 'Courses Enrolled', data: data.data }],
                    xaxis: { categories: data.labels },
                    colors: [chartColors[4]],
                    plotOptions: { bar: { borderRadius: 4 } },
                    grid: { borderColor: '#e5e7eb' },
                    tooltip: { theme: 'light' }
                }).render();
            });

        // Chat Widget Functionality
        const chatButton = document.getElementById('chatButton');
        const chatWindow = document.getElementById('chatWindow');
        const closeChat = document.getElementById('closeChat');
        const chatInput = document.getElementById('chatInput');
        const sendMessage = document.getElementById('sendMessage');
        const chatBody = document.getElementById('chatBody');

        chatButton.addEventListener('click', () => {
            chatWindow.style.display = chatWindow.style.display === 'none' ? 'flex' : 'none';
        });

        closeChat.addEventListener('click', () => {
            chatWindow.style.display = 'none';
        });

        sendMessage.addEventListener('click', sendChatMessage);
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendChatMessage();
        });

        function sendChatMessage() {
            const message = chatInput.value.trim();
            if (!message) return;

            // Add user message to chat
            const userMessage = document.createElement('div');
            userMessage.className = 'chat-message user';
            userMessage.textContent = message;
            chatBody.appendChild(userMessage);

            // Send message to backend
            fetch('{{ route('user.chat') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            })
            .then(response => response.json())
            .then(data => {
                // Add bot response
                const botMessage = document.createElement('div');
                botMessage.className = 'chat-message bot';
                botMessage.textContent = data.response;
                chatBody.appendChild(botMessage);
                chatBody.scrollTop = chatBody.scrollHeight;
            })
            .catch(error => {
                const errorMessage = document.createElement('div');
                errorMessage.className = 'chat-message bot';
                errorMessage.textContent = 'Sorry, something went wrong. Please try again.';
                chatBody.appendChild(errorMessage);
            });

            chatInput.value = '';
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    });
</script>
@endpush
