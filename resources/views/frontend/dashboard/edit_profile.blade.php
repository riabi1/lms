@extends('frontend.dashboard.user_dashboard')
@section('userdashboard')
<div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between mb-5">
    <div class="media media-card align-items-center">
        <div class="media-img media--img media-img-md rounded-full">
            <img class="rounded-full img-fluid" src="{{ !empty($profileData->photo) ? asset('storage/upload/user_images/' . $profileData->photo) : asset('upload/no_image.jpg') }}" alt="Student thumbnail image" style="width: 50px; height: 50px; object-fit: cover;">
        </div>
        <div class="media-body">
            <h2 class="section__title fs-30">Hello, {{ $profileData->name }}</h2>
        </div><!-- end media-body -->
    </div><!-- end media -->
</div><!-- end breadcrumb-content -->

<div class="tab-pane fade show active" id="edit-profile" role="tabpanel" aria-labelledby="edit-profile-tab">
    <div class="setting-body">
        <h3 class="fs-17 font-weight-semi-bold pb-4">Edit Profile</h3>
        
        @if (session('status'))
            <div class="alert alert-success mb-3">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="row pt-40px">
            @csrf
            @method('PATCH')

            <div class="media media-card align-items-center">
                <div class="media-img media-img-lg mr-4 bg-gray">
                    <img class="mr-3 img-fluid" src="{{ !empty($profileData->photo) ? asset('storage/upload/user_images/' . $profileData->photo) : asset('upload/no_image.jpg') }}" alt="avatar image" style="width: 100px; height: 100px; object-fit: cover;">
                </div>
                <div class="media-body">
                    <div class="file-upload-wrap file-upload-wrap-2">
                        <input type="file" name="photo" class="multi file-upload-input with-preview" accept="image/jpeg,image/png">
                        <span class="file-upload-text"><i class="la la-photo mr-2"></i>Upload a Photo</span>
                    </div><!-- file-upload-wrap -->
                    <p class="fs-14">Max file size is 5MB, Minimum dimension: 200x200. Suitable files are .jpg & .png</p>
                    @error('photo')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div><!-- end media -->

            <div class="input-box col-lg-6">
                <label class="label-text">Name</label>
                <div class="form-group">
                    <input class="form-control form--control" type="text" name="name" value="{{ old('name', $profileData->name) }}" required>
                    <span class="la la-user input-icon"></span>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div><!-- end input-box -->
           
            <div class="input-box col-lg-6">
                <label class="label-text">Email</label>
                <div class="form-group">
                    <input class="form-control form--control" type="email" name="email" value="{{ old('email', $profileData->email) }}" required>
                    <span class="la la-envelope input-icon"></span>
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div><!-- end input-box -->
            <div class="input-box col-lg-6">
                <label class="label-text">Phone</label>
                <div class="form-group">
                    <input class="form-control form--control" type="text" name="phone" value="{{ old('phone', $profileData->phone) }}">
                    <span class="la la-phone input-icon"></span>
                    @error('phone')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div><!-- end input-box -->
            <div class="input-box col-lg-6">
                <label class="label-text">Address</label>
                <div class="form-group">
                    <input class="form-control form--control" type="text" name="address" value="{{ old('address', $profileData->address) }}">
                    <span class="la la-map-marker input-icon"></span>
                    @error('address')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div><!-- end input-box -->

            <div class="input-box col-lg-12 py-2">
                <button class="btn theme-btn" type="submit">Save Changes</button>
            </div><!-- end input-box -->
        </form>
    </div><!-- end setting-body -->
</div><!-- end tab-pane -->
@endsection