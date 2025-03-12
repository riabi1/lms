@extends('instructor.instructor_dashboard')
@section('instructor')

<div class="container py-4">
    <div class="card p-4">
        <h3 class="mb-4">Instructor Dashboard</h3>

        <?php
            $instructor = Auth::guard('instructor')->user();
        ?>

        @if(!$instructor->hasVerifiedEmail())
            <div class="alert alert-warning mb-4">
                Your email is not verified. Please check your inbox or 
                <a href="{{ route('instructor.verification.send') }}" class="alert-link">resend verification email</a>.
            </div>
        @endif

        <div class="mb-3">
            <div class="d-flex align-items-center">
                <img class="rounded-circle me-3"
                     src="{{ $instructor->photo ? Storage::url('upload/instructor_images/' . $instructor->photo) : asset('upload/no_image.jpg') }}"
                     alt="{{ $instructor->name }}'s Profile"
                     style="width: 100px; height: 100px; object-fit: cover;">
                <h2>Hello, {{ $instructor->name }}</h2>
            </div>
        </div>

        @if(session('status'))
            <div class="alert alert-success mt-3">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mt-3">{{ session('error') }}</div>
        @endif
    </div>
</div>

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush
@endsection