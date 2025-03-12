@extends('instructor.instructor_dashboard')
@section('instructor')

<div class="container py-4">
    <div class="card p-4">
        <h3 class="mb-4">Edit Instructor Profile</h3>

        @if(!$instructor->hasVerifiedEmail())
            <div class="alert alert-warning mb-4">
                Your email is not verified. Please check your inbox or 
                <a href="{{ route('instructor.verification.send') }}" class="alert-link">resend verification email</a>.
            </div>
        @endif

        <form method="POST" action="{{ route('instructor.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label class="form-label">Profile Photo</label>
                <div class="d-flex align-items-center mb-2">
                    <img class="rounded-circle me-3"
                         src="{{ $instructor->photo ? Storage::url('upload/instructor_images/' . $instructor->photo) : asset('upload/no_image.jpg') }}"
                         alt="{{ $instructor->name }}'s Profile"
                         style="width: 100px; height: 100px; object-fit: cover;">
                    <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png">
                </div>
                <small class="text-muted">Max 5MB, .jpg/.png</small>
                @error('photo')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name</label>
                    <input class="form-control" type="text" name="name" value="{{ old('name', $instructor->name) }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" value="{{ old('email', $instructor->email) }}" required>
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input class="form-control" type="text" name="phone" value="{{ old('phone', $instructor->phone) }}">
                    @error('phone')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Address</label>
                    <input class="form-control" type="text" name="address" value="{{ old('address', $instructor->address) }}">
                    @error('address')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button class="btn btn-primary" type="submit">Save Changes</button>
        </form>

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