@extends('Instructor.layout.Instructor_layout')

@section('instructor')
<div class="container py-5">
    <div class="row g-4">
        <!-- Profil de l'Instructeur -->
        <div class="col-lg-12">
            <div class="card shadow-sm p-4">
                <h3 class="mb-4 fw-bold text-dark"><i class="la la-user mr-2 text-primary"></i> Edit Instructor Profile</h3>

                @if(!$instructor->hasVerifiedEmail())
                    <div class="alert alert-warning mb-4">
                        Your email is not verified. Please check your inbox or 
                        <a href="{{ route('instructor.verification.send') }}" class="alert-link">resend verification email</a>.
                    </div>
                @endif

                <form method="POST" action="{{ route('instructor.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <!-- Photo de Profil -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Profile Photo</label>
                        <div class="d-flex align-items-center mb-2">
                            <img class="rounded-circle me-3 shadow-sm"
                                 src="{{ $instructor->photo ? asset('upload/instructor_images/' . $instructor->photo) : asset('upload/no_image.jpg') }}"
                                 alt="{{ $instructor->name }}'s Profile"
                                 style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #ddd;">
                            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/jpg">
                        </div>
                        <small class="text-muted">Max 5MB, .jpg/.png</small>
                        @error('photo')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- CV Field -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Curriculum Vitae (CV)</label>
                        <div class="d-flex align-items-center mb-2">
                            @if($instructor->cv)
                                <a href="{{ asset('upload/instructor_cvs/' . $instructor->cv) }}" target="_blank" class="me-3 text-primary">
                                    <i class="la la-file-pdf-o"></i> Current CV
                                </a>
                            @else
                                <span class="me-3 text-muted">No CV uploaded</span>
                            @endif
                            <input type="file" name="cv" class="form-control" accept=".pdf">
                        </div>
                        <small class="text-muted">Max 2MB, .pdf only</small>
                        @error('cv')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <!-- Nom -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Name</label>
                            <input class="form-control" type="text" name="name" value="{{ old('name', $instructor->name) }}" required>
                            @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input class="form-control" type="email" name="email" value="{{ old('email', $instructor->email) }}" required>
                            @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Téléphone -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input class="form-control" type="text" name="phone" value="{{ old('phone', $instructor->phone) }}">
                            @error('phone')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Adresse -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Address</label>
                            <input class="form-control" type="text" name="address" value="{{ old('address', $instructor->address) }}">
                            @error('address')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Biographie -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Biography</label>
                            <textarea class="form-control" name="bio" rows="4">{{ old('bio', $instructor->bio) }}</textarea>
                            <small class="text-muted">Tell us about yourself</small>
                            @error('bio')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Expérience -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Experience</label>
                            <textarea class="form-control" name="experience" rows="4">{{ old('experience', $instructor->experience) }}</textarea>
                            <small class="text-muted">Describe your professional experience</small>
                            @error('experience')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Spécialité -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Specialty</label>
                            <input class="form-control" type="text" name="specialty" value="{{ old('specialty', $instructor->specialty) }}">
                            <small class="text-muted">Your area of expertise (e.g., Web Development, Data Science)</small>
                            @error('specialty')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Éducation -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Education</label>
                            <textarea class="form-control" name="education" rows="4">{{ old('education', $instructor->education) }}</textarea>
                            <small class="text-muted">List your educational background</small>
                            @error('education')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Site Web -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Website</label>
                            <input class="form-control" type="url" name="website" value="{{ old('website', $instructor->website) }}">
                            <small class="text-muted">Your personal or professional website (e.g., https://example.com)</small>
                            @error('website')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Localisation -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Location</label>
                            <input class="form-control" type="text" name="location" value="{{ old('location', $instructor->location) }}">
                            <small class="text-muted">Where are you based? (e.g., New York, USA)</small>
                            @error('location')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nouveau Mot de Passe (Optionnel) -->
                        <h4 class="mt-4 mb-3">Change Password</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">New Password (Optional)</label>
                                <input class="form-control" type="password" name="new_password">
                                <small class="text-muted">Leave blank if you don’t want to change it</small>
                                @error('new_password')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirmation du Nouveau Mot de Passe -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirm New Password</label>
                                <input class="form-control" type="password" name="new_password_confirmation">
                                @error('new_password_confirmation')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-primary px-4" type="submit">Save Changes</button>
                    </div>
                </form>

                @if(session('status'))
                    <div class="alert alert-success mt-3">{{ session('status') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                @endif
            </div>
        </div><!-- end col-lg-12 -->
    </div><!-- end row -->
</div><!-- end container -->

<!-- Scripts -->
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" integrity="sha512-c42qTSw/wPZ3/5LBzD+Bw5f7bSF2oxou6wEb+I/lqeaKV5FDIfMvvRp772y4jcJLKuGUOpbJMdg/BTl50fJYAw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script>
        $(document).ready(function() {
            // Animations pour les cartes
            $('.card').addClass('animate__animated animate__fadeInUp');
        });
    </script>
@endpush
@endsection