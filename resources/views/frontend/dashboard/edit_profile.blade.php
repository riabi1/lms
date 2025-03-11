@extends('frontend.dashboard.user_dashboard')
@section('userdashboard')
<div class="container py-4">
  <div class="d-flex align-items-center mb-4">
    <div class="mr-3">
      <img class="rounded-circle"
        src="{{ !empty($profileData->photo) ? asset('storage/upload/user_images/' . $profileData->photo) : asset('upload/no_image.jpg') }}"
        alt="Profile"
        style="width: 80px; height: 80px; object-fit: cover;">
    </div>
    <h2 class="mb-0 fs-24">Hello, {{ $profileData->name }}</h2>
  </div>

  <div class="card p-4">
    <h3 class="fs-18 font-weight-semi-bold mb-4">Edit Profile</h3>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
      @csrf
      @method('PATCH')

      <!-- <div class="mb-4 d-flex align-items-center">
        <div class="mr-3">
          <img class="rounded-circle"
            src="{{ !empty($profileData->photo) ? asset('storage/upload/user_images/' . $profileData->photo) : asset('upload/no_image.jpg') }}"
            alt="Avatar"
            style="width: 100px; height: 100px; object-fit: cover;">
        </div> -->
        <div>
          <input type="file"
            name="photo"
            class="form-control-file mb-2"
            accept="image/jpeg,image/png">
          <small class="text-muted">Max 5MB, Min 200x200, .jpg/.png</small>
          @error('photo')
          <div class="text-danger mt-1">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="mb-1">Name</label>
          <input class="form-control"
            type="text"
            name="name"
            value="{{ old('name', $profileData->name) }}"
            required>
          @error('name')
          <div class="text-danger mt-1">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label class="mb-1">Email</label>
          <input class="form-control"
            type="email"
            name="email"
            value="{{ old('email', $profileData->email) }}"
            required>
          @error('email')
          <div class="text-danger mt-1">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label class="mb-1">Phone</label>
          <input class="form-control"
            type="text"
            name="phone"
            value="{{ old('phone', $profileData->phone) }}">
          @error('phone')
          <div class="text-danger mt-1">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label class="mb-1">Address</label>
          <input class="form-control"
            type="text"
            name="address"
            value="{{ old('address', $profileData->address) }}">
          @error('address')
          <div class="text-danger mt-1">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <button class="btn btn-primary" type="submit">Save Changes</button>
    </form>
  </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Success</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Profile updated successfully!
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
@if (session('status'))
<script>
  $(document).ready(function() {
    $('#successModal').modal('show');
  });
</script>
@endif
@endpush
@endsection