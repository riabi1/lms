@extends('Instructor.layout.Instructor_layout')

@section('instructor')
<div class="container py-4">
  <div class="card p-4">
    <h3 class="mb-4">Instructor Dashboard</h3>

    @php
    $instructor = Auth::guard('instructor')->user();
    @endphp

    @if(!$instructor->hasVerifiedEmail())
    <div class="alert alert-warning mb-4">
      Your email is not verified. Please check your inbox or
      <a href="{{ route('instructor.verification.send') }}" class="alert-link">resend verification email</a>.
    </div>
    @endif

    <div class="mb-3">
      <div class="d-flex align-items-center">
        <img class="rounded-circle me-3"
          src="{{ $instructor && $instructor->photo ? asset('upload/instructor_images/' . $instructor->photo) : asset('upload/no_image.jpg') }}"
          alt="{{ $instructor->name }}'s Profile"
          style="width: 100px; height: 100px; object-fit: cover;">
        <div>
          <h2>Hello, {{ $instructor->name }}</h2>
        </div>
      </div>
    </div>

    <div class="row text-center my-4">
      <div class="col-md-4">
        <div class="card shadow p-3">
          <h5>Total Courses</h5>
          <h3>{{ $courseCount }}</h3>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow p-3">
          <h5>Total Students</h5>
          <h3>{{ $studentCount }}</h3>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow p-3">
          <h5>Total Revenue</h5>
          <h3>${{ number_format($totalRevenue, 2) }}</h3>
        </div>
      </div>
    </div>

    <div class="card p-4 mt-5">
      <h4 class="mb-4">Enrollments Over Last 6 Months</h4>
      <div id="enrollmentChart"></div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var options = {
      chart: {
        type: 'line',
        height: 350
      },
      series: [{
        name: 'Enrollments',
        data: @json($enrollmentData)
      }],
      xaxis: {
        categories: @json($enrollmentLabels)
      },
      stroke: {
        curve: 'smooth'
      },
      colors: ['#00BFFF']
    };

    var chart = new ApexCharts(document.querySelector("#enrollmentChart"), options);
    chart.render();
  });
</script>
@endpush