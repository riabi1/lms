@extends('Instructor.layout.Instructor_layout')

@section('instructor')
<style>
.gradient-primary {
    background: linear-gradient(135deg, #fce7f3, #fbcfe8);
    color: #831843;
}
.gradient-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #064e3b;
}
.gradient-info {
    background: linear-gradient(135deg, #e0f2fe, #bae6fd);
    color: #1e40af;
}
.gradient-warning {
    background: linear-gradient(135deg, #ffedd5, #fed7aa);
    color: #7c2d12;
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
.action-btn {
    font-size: 0.9rem;
    padding: 8px 16px;
    border-radius: 20px;
    background-color: #fbcfe8;
    border-color: #fbcfe8;
    color: #831843;
}
.action-btn:hover {
    background-color: #f9a8d4;
    border-color: #f9a8d4;
}
.tooltip-info {
    cursor: pointer;
    color: #60a5fa;
    font-size: 0.9rem;
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
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}
/* Chat Widget Styles */
.chat-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #fbcfe8;
    color: #831843;
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
    background-color: #f9a8d4;
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
    background: linear-gradient(135deg, #fbcfe8, #f9a8d4);
    color: #831843;
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
    background: #fbcfe8;
    color: #831843;
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
    background: #fbcfe8;
    color: #831843;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
}
.chat-input button:hover {
    background: #f9a8d4;
}
</style>

<div class="container py-5">
    <div class="card shadow p-4 border-0" style="background: #f3f4f6;">
        <h3 class="mb-4 text-primary">🎓 Your Teaching Hub</h3>

        @if(!$instructor->hasVerifiedEmail())
        <div class="alert alert-warning mb-4 d-flex align-items-center" style="background-color: #ffedd5; border-color: #fed7aa; color: #7c2d12;">
            <span class="me-2">⚠️</span>
            Verify your email to unlock full platform features!
            <a href="{{ route('instructor.verification.send') }}" class="alert-link ms-2">Resend Email</a>
        </div>
        @endif

        <div class="mb-5">
            <div class="d-flex align-items-center">
                <img class="rounded-circle me-3 border p-1" style="border-color: #fbcfe8;"
                    src="{{ $instructor->photo ? asset('upload/instructor_images/' . $instructor->photo) : asset('upload/no_image.jpg') }}"
                    alt="{{ $instructor->name }}'s Profile"
                    style="width: 100px; height: 100px; object-fit: cover;">
                <div>
                    <h2 class="mb-1 text-dark fw-bold">{{ $greeting }}</h2>
                    <p class="text-muted mb-2">{{ $trendMessage }}</p>
                    <a href="{{ route('instructor.courses.create') }}" class="btn action-btn">Create New Course</a>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row text-center mb-5">
            <div class="col-md-3 mb-4">
                <div class="card metric-card shadow p-4 gradient-primary">
                    <h5 class="fw-bold">📚 Courses</h5>
                    <div class="metric-value">{{ $courseCount }}</div>
                    <small>
                        @if($courseCount > 0)
                            Amazing! <a href="{{ route('instructor.courses.create') }}" class="text-decoration-underline" style="color: #831843;">Add another?</a>
                        @else
                            Start now! <a href="{{ route('instructor.courses.create') }}" class="text-decoration-underline" style="color: #831843;">Create Course</a>
                        @endif
                    </small>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card metric-card shadow p-4 gradient-success">
                    <h5 class="fw-bold">👥 Students</h5>
                    <div class="metric-value">{{ $studentCount }}</div>
                    <small>
                        @if($studentCount > 0)
                            Inspiring {{ $studentCount }} learners! Promote to grow! 📣
                        @else
                            Attract students with your first course! 🚀
                        @endif
                    </small>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card metric-card shadow p-4 gradient-warning">
                    <h5 class="fw-bold">💸 Revenue</h5>
                    <div class="metric-value">${{ number_format($totalRevenue, 2) }}</div>
                    <small>
                        @if($totalRevenue > 0)
                            Earning big! Optimize pricing! 🤑
                        @else
                            Monetize your expertise! 💡
                        @endif
                    </small>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card metric-card shadow p-4 gradient-info">
                    <h5 class="fw-bold">⭐ Average Rating <span class="tooltip-info" data-bs-toggle="tooltip" title="Average student rating for your courses">ℹ️</span></h5>
                    <div class="metric-value">{{ $averageCourseRating }}/5</div>
                    <small>
                        @if($averageCourseRating > 0)
                            Great feedback! Respond to reviews to boost ratings! 🌟
                        @else
                            Encourage students to leave reviews! 📝
                        @endif
                    </small>
                </div>
            </div>
        </div>

        <!-- Actionable Recommendations -->
        @if(!empty($recommendations))
        <div class="card p-4 mb-5 chart-card">
            <h4 class="mb-4 fw-bold" style="color: #10b981;">🚀 Boost Your Success</h4>
            <ul class="list-group list-group-flush">
                @foreach($recommendations as $recommendation)
                <li class="list-group-item recommendation-item mb-2" style="background: #f3f4f6;">
                    <span style="color: #f472b6;">🔥</span>
                    {{ $recommendation }}
                    @if(str_contains($recommendation, 'quiz'))
                        <a href="{{ route('instructor.quiz.create') }}" class="btn btn-outline-primary action-btn ms-2">Add Quiz</a>
                    @elseif(str_contains($recommendation, 'blog'))
                        <a href="{{ route('instructor.blog.create') }}" class="btn btn-outline-primary action-btn ms-2">Write Blog</a>
                    @elseif(str_contains($recommendation, 'rating'))
                        <a href="{{ route('instructor.all.review') }}" class="btn btn-outline-primary action-btn ms-2">View Feedback</a>
                    @endif
                </li>
                @endforeach
            </ul>
            <p class="mt-3 text-muted">
                @if($courseCount > 5 && $studentCount > 100)
                    You're a platform leader! 🌟 Host live Q&A sessions to deepen student engagement.
                @elseif($courseCount > 0)
                    Awesome progress! 🎉 Add videos or quizzes to keep students coming back.
                @else
                    Your teaching journey starts here! 📚 Create a course to inspire learners.
                @endif
            </p>
        </div>
        @endif

        <!-- Charts Section -->
        <div class="row mb-5">
            <div class="col-md-6 mb-4">
                <div class="card p-4 chart-card">
                    <h4 class="mb-4 fw-bold" style="color: #60a5fa;">📈 Enrollment Trends <span class="tooltip-info" data-bs-toggle="tooltip" title="Enrollments over the last 6 months">ℹ️</span></h4>
                    <div id="enrollmentChart"></div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card p-4 chart-card">
                    <h4 class="mb-4 fw-bold" style="color: #60a5fa;">💰 Revenue Breakdown <span class="tooltip-info" data-bs-toggle="tooltip" title="Revenue by course">ℹ️</span></h4>
                    <div id="revenueChart"></div>
                </div>
            </div>
        </div>

        <!-- Top Performing Courses -->
        <div class="card p-4 mb-5 chart-card">
            <h4 class="mb-4 fw-bold" style="color: #10b981;">🏆 Top Courses</h4>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Enrollments</th>
                        <th>Revenue</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topCourses as $index => $course)
                    <tr>
                        <td><span class="me-2" style="color: #f472b6;">🎓</span>{{ $course['title'] }}</td>
                        <td>{{ $course['enrollments'] }}</td>
                        <td>${{ number_format($course['revenue'], 2) }}</td>
                        <td>{{ $course['average_rating'] }}/5 ⭐</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Course Performance Overview -->
        <div class="card p-4 chart-card">
            <h4 class="mb-4 fw-bold" style="color: #10b981;">📊 All Courses Performance</h4>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Completion</th>
                        <th>Rating</th>
                        <th>Enrollments</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($coursePerformance as $index => $course)
                    <tr>
                        <td><span class="me-2" style="color: #f472b6;">🎓</span>{{ $course['title'] }}</td>
                        <td>{{ $course['completion_rate'] }}%</td>
                        <td>{{ $course['average_rating'] }}/5 ⭐</td>
                        <td>{{ $course['enrollments'] }}</td>
                        <td>${{ number_format($course['revenue'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Chat Widget -->
        <div class="chat-button" id="chatButton">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/>
            </svg>
        </div>
        <div class="chat-window" id="chatWindow">
            <div class="chat-header">
                <span>Teaching Assistant</span>
                <button class="close-chat" id="closeChat">✖</button>
            </div>
            <div class="chat-body" id="chatBody">
                <div class="chat-message bot">Hello! I'm here to help with your teaching. Ask about your courses, students, or revenue! 😊</div>
            </div>
            <div class="chat-input">
                <input type="text" id="chatInput" placeholder="Type your question...">
                <button id="sendMessage">Send</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    $(document).ready(function() {
        // Initialize Bootstrap tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Light color palette
        const chartColors = ['#60a5fa', '#f472b6', '#10b981', '#fb923c', '#a78bfa', '#22d3ee'];

        // Enrollment Chart
        var enrollmentOptions = {
            chart: {
                type: 'line',
                height: 350,
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            series: [{
                name: 'Enrollments',
                data: @json($enrollmentData)
            }],
            xaxis: {
                categories: @json($enrollmentLabels),
                title: {
                    text: 'Month',
                    style: { color: '#1f2937' }
                }
            },
            yaxis: {
                title: {
                    text: 'Enrollments',
                    style: { color: '#1f2937' }
                }
            },
            stroke: {
                curve: 'smooth',
                width: 4
            },
            colors: [chartColors[0]],
            tooltip: {
                theme: 'light',
                y: { formatter: val => `${val} enrollments` }
            },
            grid: {
                borderColor: '#e5e7eb'
            }
        };

        var enrollmentChart = new ApexCharts(document.querySelector("#enrollmentChart"), enrollmentOptions);
        enrollmentChart.render();

        // Revenue Chart
        var revenueOptions = {
            chart: {
                type: 'bar',
                height: 350,
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            series: [{
                name: 'Revenue',
                data: @json($revenueData)
            }],
            xaxis: {
                categories: @json($revenueLabels),
                title: {
                    text: 'Course',
                    style: { color: '#1f2937' }
                },
                labels: {
                    rotate: -45,
                    style: { fontSize: '12px' }
                }
            },
            yaxis: {
                title: {
                    text: 'Revenue ($)',
                    style: { color: '#1f2937' }
                }
            },
            colors: [chartColors[2]],
            tooltip: {
                theme: 'light',
                y: { formatter: val => `$${val.toFixed(2)}` }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    endingShape: 'rounded'
                }
            },
            grid: {
                borderColor: '#e5e7eb'
            }
        };

        var revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
        revenueChart.render();

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

            // Add user message
            const userMessage = document.createElement('div');
            userMessage.className = 'chat-message user';
            userMessage.textContent = message;
            chatBody.appendChild(userMessage);

            // Send to backend
            fetch('{{ route('instructor.chat.handle') }}', {
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
                errorMessage.textContent = 'Sorry, something went wrong. Try again!';
                chatBody.appendChild(errorMessage);
            });

            chatInput.value = '';
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    });
</script>
@endpush
