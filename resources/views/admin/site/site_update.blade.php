@extends('admin.layout.Admin_layout')

@section('title', 'Site Settings | Easy Learning')

@section('admin')
    <div class="page-content">
        <!-- Breadcrumb -->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0 bg-light rounded-3 shadow-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}" class="text-red"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active text-dark" aria-current="page">Site Settings</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-gradient-red text-white py-3">
                <h5 class="mb-0"><i class="bx bx-cog me-2"></i>Site Settings</h5>
            </div>
            <div class="card-body p-4">
                @if (session('message'))
                    <div class="alert alert-{{ session('alert-type', 'success') }} alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form id="siteSettingsForm" action="{{ route('admin.site.update') }}" method="POST" class="row g-3" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $site->id }}">

                    <!-- Contact Info -->
                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-bold">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone', $site->phone) }}" required>
                        @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" id="email" value="{{ old('email', $site->email) }}" required>
                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="address" class="form-label fw-bold">Address</label>
                        <input type="text" name="address" class="form-control" id="address" value="{{ old('address', $site->address) }}">
                        @error('address') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Copyright -->
                    <div class="col-md-6">
                        <label for="copyright" class="form-label fw-bold">Copyright Text</label>
                        <input type="text" name="copyright" class="form-control" id="copyright" value="{{ old('copyright', $site->copyright) }}">
                        @error('copyright') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Logo -->
                    <div class="col-md-6">
                        <label for="logo" class="form-label fw-bold">Site Logo</label>
                        <input class="form-control" name="logo" type="file" id="logo" accept="image/jpeg,image/png,image/jpg">
                        <small class="text-muted">JPEG/PNG, max 2MB, recommended ~140x41px</small>
                        @error('logo') <span class="text-danger small">{{ $message }}</span> @enderror
                        <div class="mt-2">
                            @php
                                $logoUrl = $site->logo ? Storage::disk('public')->url($site->logo) : asset('images/default-logo.png');
                            @endphp
                            <img id="showImage" 
                                 src="{{ $logoUrl }}" 
                                 alt="Site Logo" 
                                 class="rounded p-1 border bg-light" 
                                 style="max-width: 140px; height: auto;"
                                 onerror="this.src='{{ asset('images/default-logo.png') }}';">
                            @if ($site->logo)
                                <p class="text-muted small mt-1">Current logo path: {{ $site->logo }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary px-5">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#logo').on('change', function(e) {
                    const file = e.target.files[0];
                    const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    const maxSize = 2 * 1024 * 1024; // 2MB in bytes

                    if (file) {
                        if (!validImageTypes.includes(file.type)) {
                            alert('Please select a valid JPEG or PNG image.');
                            $('#logo').val('');
                            $('#showImage').attr('src', '{{ $site->logo ? Storage::disk('public')->url($site->logo) : asset('images/default-logo.png') }}');
                            return;
                        }

                        if (file.size > maxSize) {
                            alert('The logo must not exceed 2MB.');
                            $('#logo').val('');
                            $('#showImage').attr('src', '{{ $site->logo ? Storage::disk('public')->url($site->logo) : asset('images/default-logo.png') }}');
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#showImage').attr('src', e.target.result).show();
                        };
                        reader.onerror = function() {
                            alert('Error reading the image file.');
                            $('#logo').val('');
                            $('#showImage').attr('src', '{{ $site->logo ? Storage::disk('public')->url($site->logo) : asset('images/default-logo.png') }}');
                        reader.readAsDataURL(file);
                    }
                });
            });
        </script>
    @endpush
@endsection