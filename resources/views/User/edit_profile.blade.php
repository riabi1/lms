@extends('User.layout.user_layout')

@section('title')
    Edit Profile | Easy Learning
@endsection

@section('userdashboard')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <div class="container py-5">
        <div class="card shadow-sm p-4">
            <h3 class="mb-4 fw-bold text-dark"><i class="la la-user mr-2 text-primary"></i> Edit Profile</h3>

            @if(!$profileData->hasVerifiedEmail())
                <div class="alert alert-warning mb-4">
                    Your email is not verified. Please check your inbox or
                    <a href="{{ route('verification.send') }}" class="alert-link">resend verification email</a>.
                </div>
            @endif

            <form id="profileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <!-- Profile Photo -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Profile Photo</label>
                    <div class="d-flex align-items-center mb-2">
                        <img class="rounded-circle me-3 shadow-sm"
                             src="{{ $profileData->photo ? asset('upload/user_images/' . $profileData->photo) : asset('upload/no_image.jpg') }}"
                             alt="{{ $profileData->name }}'s Profile"
                             style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #ddd;">
                        <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/jpg">
                    </div>
                    <small class="text-muted">Max 5MB, .jpg/.png</small>
                    @error('photo')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>


                <div class="row g-3">
                    <!-- Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input class="form-control" type="text" name="name" value="{{ old('name', $profileData->name) }}" required>
                        @error('name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email', $profileData->email) }}" required>
                        @error('email')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Phone</label>
                        <input class="form-control" type="text" name="phone" value="{{ old('phone', $profileData->phone) }}">
                        @error('phone')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Address</label>
                        <input class="form-control" type="text" name="address" value="{{ old('address', $profileData->address) }}">
                        @error('address')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Preferences -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Preferences (Select up to 3)</label>
                        <div class="checkbox-group" data-max="3">
                            @foreach ($categories as $cat)
                                <div class="form-check">
                                    <input class="form-check-input preference-checkbox" 
                                           type="checkbox" 
                                           name="preference[]" 
                                           value="{{ $cat->id }}"
                                           id="category_{{ $cat->id }}"
                                           {{ in_array($cat->id, old('preference', json_decode($profileData->preference ?? '[]', true))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="category_{{ $cat->id }}">
                                        {{ $cat->category_name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted">Select up to 3 categories you are interested in</small>
                        @error('preference')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                        @error('preference.*')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Grade -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Grade</label>
                        <select class="form-control" name="grade_select" id="grade_select">
                            <option value="" {{ old('grade_select', $profileData->grade) == '' ? 'selected' : '' }}>Select a grade</option>
                            <option value="Secondary" {{ old('grade_select', $profileData->grade) == 'Secondary' ? 'selected' : '' }}>Secondary</option>
                            <option value="Baccalaureate Candidate" {{ old('grade_select', $profileData->grade) == 'Baccalaureate Candidate' ? 'selected' : '' }}>Baccalaureate Candidate</option>
                            <option value="Bachelor" {{ old('grade_select', $profileData->grade) == 'Bachelor' ? 'selected' : '' }}>Bachelor</option>
                            <option value="Master" {{ old('grade_select', $profileData->grade) == 'Master' ? 'selected' : '' }}>Master</option>
                            <option value="Engineering" {{ old('grade_select', $profileData->grade) == 'Engineering' ? 'selected' : '' }}>Engineering</option>
                            <option value="Other" {{ old('grade_select', $profileData->grade) && !in_array(old('grade_select', $profileData->grade), ['Secondary', 'Baccalaureate Candidate', 'Bachelor', 'Master', 'Engineering']) ? 'selected' : '' }}>Other</option>
                        </select>
                        <input type="text" class="form-control mt-2 d-none" name="grade_custom" id="grade_custom" value="{{ old('grade_custom', !in_array($profileData->grade, ['Secondary', 'Baccalaureate Candidate', 'Bachelor', 'Master', 'Engineering']) ? $profileData->grade : '') }}" placeholder="Enter custom grade">
                        <small class="text-muted">E.g., Secondary or specify your level</small>
                        @error('grade')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Change Password -->
                    <h4 class="mt-4 mb-3">Change Password</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">New Password (Optional)</label>
                            <input class="form-control" type="password" name="new_password" value="{{ old('new_password') }}">
                            <small class="text-muted">Minimum 8 characters (leave blank to keep current password)</small>
                            @error('new_password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Confirm New Password</label>
                            <input class="form-control" type="password" name="new_password_confirmation">
                            @error('new_password_confirmation')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
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
        </div>

        @push('scripts')
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" integrity="sha512-c42qTSw/wPZ3/5LBzD+Bw5f7bSF2oxou6wEb+I/lqeaKV5FDIfMvvRp772y4jcJLKuGUOpbJMdg/BTl50fJYAw==" crossorigin="anonymous" referrerpolicy="no-referrer">
            <script>
                $(document).ready(function() {
                    // Limit checkbox selections to 3
                    $('.preference-checkbox').on('change', function() {
                        var maxAllowed = 3;
                        var checkedCount = $('.preference-checkbox:checked').length;

                        if (checkedCount > maxAllowed) {
                            $(this).prop('checked', false);
                            alert('You can select up to ' + maxAllowed + ' preferences.');
                        }
                    });

                    // Show/hide custom grade input
                    $('#grade_select').on('change', function() {
                        if ($(this).val() === 'Other') {
                            $('#grade_custom').removeClass('d-none');
                        } else {
                            $('#grade_custom').addClass('d-none').val('');
                        }
                    });

                    // Trigger on page load to handle existing custom grade
                    if ($('#grade_select').val() === 'Other') {
                        $('#grade_custom').removeClass('d-none');
                    }

                    // Animations for cards
                    $('.card').addClass('animate__animated animate__fadeInUp');

                    // Form validation
                    $('#profileForm').validate({
                        rules: {
                            name: { required: true, maxlength: 255 },
                            email: { required: true, email: true, maxlength: 255 },
                            phone: { maxlength: 20 },
                            address: { maxlength: 255 },
                            photo: { accept: "image/jpeg,image/png,image/jpg" },
                            cv: { accept: "application/pdf", maxsize: 2097152 }, // 2MB
                            'preference[]': { maxlength: 3 },
                            grade_select: { required: true },
                            grade_custom: { 
                                required: function(element) {
                                    return $('#grade_select').val() === 'Other';
                                },
                                maxlength: 255
                            },
                            new_password: { minlength: 8 }
                        },
                        messages: {
                            name: { required: 'Please enter your name', maxlength: 'Name cannot exceed 255 characters' },
                            email: { required: 'Please enter your email', email: 'Please enter a valid email', maxlength: 'Email cannot exceed 255 characters' },
                            phone: { maxlength: 'Phone number cannot exceed 20 characters' },
                            address: { maxlength: 'Address cannot exceed 255 characters' },
                            photo: { accept: 'Please upload a valid image (JPEG, PNG, JPG)' },
                            cv: { 
                                accept: 'Please upload a valid PDF file', 
                                maxsize: 'CV file cannot exceed 2MB' 
                            },
                            'preference[]': { maxlength: 'You can select up to 3 preferences' },
                            grade_select: { required: 'Please select a grade' },
                            grade_custom: { 
                                required: 'Please enter a custom grade',
                                maxlength: 'Custom grade cannot exceed 255 characters'
                            },
                            new_password: { minlength: 'Password must be at least 8 characters' }
                        },
                        errorElement: 'div',
                        errorPlacement: function(error, element) {
                            error.addClass('text-danger');
                            element.closest('.col-md-6, .mb-4').append(error);
                        },
                        highlight: function(element) {
                            $(element).addClass('is-invalid');
                            if (element.name === 'preference[]') {
                                $(element).closest('.checkbox-group').addClass('is-invalid');
                            }
                        },
                        unhighlight: function(element) {
                            $(element).removeClass('is-invalid');
                            if (element.name === 'preference[]') {
                                $(element).closest('.checkbox-group').removeClass('is-invalid');
                            }
                        }
                    });
                });
            </script>
        @endpush
    </div>
@endsection