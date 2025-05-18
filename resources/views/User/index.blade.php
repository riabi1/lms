@extends('User.layout.user_layout')

@section('title')
User Dashboard | Easy Learning
@endsection

@section('userdashboard')
<div class="container py-4">
  <div class="card p-4">
    <h3 class="mb-4">User Dashboard</h3>

    <?php $user = Auth::guard('web')->user(); ?>

    @if (!$user->hasVerifiedEmail())
    <div class="alert alert-warning mb-4">
      Your email is not verified. Please check your inbox or
      <a href="{{ route('user.verification.send') }}" class="alert-link">resend verification email</a>.
    </div>
    @endif

    <div class="mb-3">
      <div class="d-flex align-items-center">
        <img class="rounded-circle me-3"
          src="{{ $user->photo ? Storage::url('upload/user_images/' . $user->photo) : asset('upload/no_image.jpg') }}"
          alt="{{ $user->name }}'s Profile" style="width: 100px; height: 100px; object-fit: cover;">
        <h2>Hello, {{ $user->name }}</h2>
      </div>
    </div>

    @if (session('status'))
    <div class="alert alert-success mt-3">{{ session('status') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif
  </div>

  <!-- Enrollment Trends Chart -->
  <div class="card p-4 mt-5">
    <h4 class="mb-4">Course Enrollments Over Last 6 Months</h4>
    <div id="enrollmentsChart"></div>
  </div>

  <!-- Course Completion Chart -->
  <div class="card p-4 mt-5">
    <h4 class="mb-4">Course Completion Progress</h4>
    <div id="completionChart" style="height: 400px;"></div>
  </div>

  <!-- Quiz Performance Chart -->
  <div class="card p-4 mt-5">
    <h4 class="mb-4">Quiz Performance (Pass vs Fail)</h4>
    <div id="quizPerformanceChart" style="height: 250px;"></div>
  </div>

</div>
@endsection

@push('scripts')
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Enrollment Trends Chart
    fetch('{{ route('user.enrollmenttrends') }}')
      .then(res => res.json())
      .then(data => {
        new ApexCharts(document.querySelector("#enrollmentsChart"), {
          chart: {
            type: 'line',
            height: 350
          },
          series: [{
            name: 'Enrollments',
            data: data.data
          }],
          xaxis: {
            categories: data.labels
          },
          stroke: {
            curve: 'smooth'
          },
          colors: ['#4A90E2']
        }).render();
      });

    // Course Completion Chart
    fetch('{{ route('user.completiondata') }}')
      .then(res => res.json())
      .then(data => {
        new ApexCharts(document.querySelector("#completionChart"), {
          chart: {
            type: 'bar',
            height: 400
          },
          series: [{
            name: 'Completion %',
            data: data.data
          }],
          xaxis: {
            categories: data.labels
          },
          yaxis: {
            max: 100,
            title: {
              text: 'Percentage (%)'
            }
          },
          colors: ['#28a745']
        }).render();
      });

    // Quiz Performance Chart
    fetch('{{ route('user.quizperformance') }}')
      .then(res => res.json())
      .then(data => {
        new ApexCharts(document.querySelector("#quizPerformanceChart"), {
          chart: {
            type: 'donut',
            height: 250
          },
          series: data.data,
          labels: data.labels,
          colors: ['#198754', '#dc3545'],
          plotOptions: {
            pie: {
              donut: {
                size: '60%' // petit cercle intérieur
              }
            }
          },
          legend: {
            position: 'bottom'
          }
        }).render();
      });
  });
</script>
@endpush