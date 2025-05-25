@extends('admin.layout.Admin_layout')

@section('admin')
<div class="page-content py-4">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4 px-4">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0 bg-light rounded-3 shadow-sm">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-red"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active text-dark" aria-current="page">Edit Profile</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container">
        <div class="card shadow-lg border-0 rounded-4 p-4">
            <div class="card-header bg-gradient-red text-white py-3 rounded-top-4">
                <h3 class="mb-0"><i class="bx bx-user-circle me-2"></i>Edit Admin Profile</h3>
            </div>
            <div class="card-body p-4">
                <!-- Email Verification Notice -->
                @if(!$admin->hasVerifiedEmail())
                    <div class="alert alert-warning mb-4 shadow-sm d-flex align-items-center">
                        <i class="bx bx-exclamation-circle me-2 fs-5"></i>
                        <span>Your email is not verified. Please check your inbox or 
                            <a href="{{ route('admin.verification.send') }}" class="alert-link text-decoration-underline">resend verification email</a>.
                        </span>
                    </div>
                @endif

                <!-- Success/Error Messages -->
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
                        <i class="bx bx-check-circle me-2"></i>{{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
                        <i class="bx bx-x-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Profile Form -->
                <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    @method('PATCH')

                    <!-- Profile Information Section -->
                    <h4 class="mt-2 mb-4 text-dark fw-bold border-bottom pb-2">Profile Information</h4>
                    <div class="row g-4">
                        <!-- Profile Photo -->
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Profile Photo</label>
                            <div class="d-flex align-items-center flex-wrap mb-2">
                                <img class="rounded-circle me-3 shadow-sm"
                                     src="{{ $admin->photo ? asset('upload/admin_images/' . $admin->photo) : asset('upload/no_image.jpg') }}"
                                     alt="{{ $admin->name }}'s Profile"
                                     style="width: 100px; height: 100px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/jpg" id="photoInput">
                                    <small class="text-muted d-block mt-1">Max 5MB, .jpg/.png</small>
                                </div>
                            </div>
                            @error('photo')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- CV Upload -->
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Curriculum Vitae (CV)</label>
                            <div class="d-flex align-items-center flex-wrap mb-2">
                                @if($admin->cv)
                                    <a href="{{ asset('upload/admin_cvs/' . $admin->cv) }}" target="_blank" class="me-3 text-primary">
                                        <i class="bx bx-file me-1"></i>Current CV
                                    </a>
                                @else
                                    <span class="me-3 text-muted">No CV uploaded</span>
                                @endif
                                <div class="flex-grow-1">
                                    <input type="file" name="cv" class="form-control" accept="application/pdf" id="cvInput">
                                    <small class="text-muted d-block mt-1">Max 2MB, .pdf only</small>
                                </div>
                            </div>
                            @error('cv')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                            <input class="form-control shadow-sm" type="text" name="name" value="{{ old('name', $admin->name) }}" required>
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <input class="form-control shadow-sm" type="email" name="email" value="{{ old('email', $admin->email) }}" required>
                            @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone</label>
                            <input class="form-control shadow-sm" type="text" name="phone" value="{{ old('phone', $admin->phone) }}">
                            @error('phone')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Address</label>
                            <input class="form-control shadow-sm" type="text" name="address" value="{{ old('address', $admin->address) }}">
                            @error('address')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Password Change Section -->
                    <h4 class="mt-5 mb-4 text-dark fw-bold border-bottom pb-2">Change Password</h4>
                    <div class="row g-4">
                        <!-- New Password -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">New Password</label>
                            <input class="form-control shadow-sm" type="password" name="new_password" value="{{ old('new_password') }}">
                            <small class="text-muted d-block mt-1">Minimum 8 characters (leave blank to keep current password)</small>
                            @error('new_password')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Confirm New Password</label>
                            <input class="form-control shadow-sm" type="password" name="new_password_confirmation">
                            @error('new_password_confirmation')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center mt-5">
                        <button class="btn btn-primary px-5 py-2 shadow-sm" type="submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Bootstrap form validation
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                var forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Image preview
        document.getElementById('photoInput').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.querySelector('.rounded-circle').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // CV file name preview
        document.getElementById('cvInput').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const cvLink = document.querySelector('.text-primary') || document.querySelector('.text-muted');
                cvLink.textContent = file.name;
                cvLink.classList.remove('text-muted');
                cvLink.classList.add('text-primary');
            }
        });
    </script>
@endpush
@endsection