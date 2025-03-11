@extends('frontend.dashboard.user_dashboard')
@section('userdashboard')
<?php
    // Get the authenticated user - this assumes $profileData is passed from controller
    // If not passed, you might need to use Auth::user() directly
    $user = $profileData ?? Auth::guard('web')->user();
?>

<div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between mb-5">
    <div class="media media-card align-items-center">
        <div class="media-img media--img media-img-md rounded-full">
            <img class="rounded-full"
                 src="{{ $user->photo ? Storage::url('upload/user_images/' . $user->photo) : asset('upload/no_image.jpg') }}"
                 alt="{{ $user->name }}'s profile image"
                 style="width: 80px; height: 80px; object-fit: cover;">
        </div>
        <div class="media-body">
            <h2 class="section__title fs-30">Hello, {{ $user->name }}</h2>
        </div><!-- end media-body -->
    </div><!-- end media -->
</div><!-- end breadcrumb-content -->

<div class="section-block mb-5"></div>
<div class="dashboard-heading mb-5">
    <h3 class="fs-22 font-weight-semi-bold">Dashboard</h3>
</div>

@endsection