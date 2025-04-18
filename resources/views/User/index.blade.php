@extends('User.layout.user_layout')

@section('title')
    User Dashboard | Easy Learning
@endsection

@section('userdashboard')
    <div class="container py-4">
        <div class="card p-4">
            <h3 class="mb-4">User Dashboard</h3>

            <?php
                $user = Auth::guard('web')->user();
            ?>

            @if(!$user->hasVerifiedEmail())
                <div class="alert alert-warning mb-4">
                    Your email is not verified. Please check your inbox or 
                    <a href="{{ route('user.verification.send') }}" class="alert-link">resend verification email</a>.
                </div>
            @endif

            <div class="mb-3">
                <div class="d-flex align-items-center">
                    <img class="rounded-circle me-3"
                         src="{{ $user->photo ? Storage::url('upload/user_images/' . $user->photo) : asset('upload/no_image.jpg') }}"
                         alt="{{ $user->name }}'s Profile"
                         style="width: 100px; height: 100px; object-fit: cover;">
                    <h2>Hello, {{ $user->name }}</h2>
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
@endsection