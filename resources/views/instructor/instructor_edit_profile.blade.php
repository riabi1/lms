@extends('Instructor.layout.Instructor_layout')
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
                    <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/jpg">
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

                <div class="col-md-12 mb-3">
                    <label class="form-label">Biography</label>
                    <textarea class="form-control" name="bio" rows="4">{{ old('bio', $instructor->bio) }}</textarea>
                    <small class="text-muted">Tell us about yourself</small>
                    @error('bio')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Experience</label>
                    <textarea class="form-control" name="experience" rows="4">{{ old('experience', $instructor->experience) }}</textarea>
                    <small class="text-muted">Describe your professional experience</small>
                    @error('experience')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Skills</label>
                    <div id="skills-container">
                        @php
                            $skills = $instructor->skills ? array_map(function($skill) {
                                [$name, $level] = explode(':', $skill . ':0'); // Ajoute :0 si pas de niveau
                                return ['name' => trim($name), 'level' => trim($level)];
                            }, explode(',', $instructor->skills)) : [];
                        @endphp
                        @forelse($skills as $index => $skill)
                            <div class="skill-entry mb-2 d-flex align-items-center">
                                <input type="text" name="skills[{{$index}}][name]" value="{{ $skill['name'] }}" class="form-control me-2" placeholder="Skill name" required>
                                <input type="number" name="skills[{{$index}}][level]" value="{{ $skill['level'] }}" class="form-control me-2" min="0" max="100" placeholder="Level (0-100)" style="width: 100px;" required>
                                <button type="button" class="btn btn-danger btn-sm remove-skill">Remove</button>
                            </div>
                        @empty
                            <div class="skill-entry mb-2 d-flex align-items-center">
                                <input type="text" name="skills[0][name]" value="" class="form-control me-2" placeholder="Skill name" required>
                                <input type="number" name="skills[0][level]" value="" class="form-control me-2" min="0" max="100" placeholder="Level (0-100)" style="width: 100px;" required>
                                <button type="button" class="btn btn-danger btn-sm remove-skill">Remove</button>
                            </div>
                        @endforelse
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm mt-2" id="add-skill">Add Skill</button>
                    <small class="text-muted">Add your skills and their proficiency level (0-100%)</small>
                    @error('skills')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Education</label>
                    <textarea class="form-control" name="education" rows="4">{{ old('education', $instructor->education) }}</textarea>
                    <small class="text-muted">List your educational background</small>
                    @error('education')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Website</label>
                    <input class="form-control" type="url" name="website" value="{{ old('website', $instructor->website) }}">
                    <small class="text-muted">Your personal or professional website (e.g., https://example.com)</small>
                    @error('website')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Location</label>
                    <input class="form-control" type="text" name="location" value="{{ old('location', $instructor->location) }}">
                    <small class="text-muted">Where are you based? (e.g., New York, USA)</small>
                    @error('location')
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
    <script>
        $(document).ready(function() {
            let skillIndex = {{ count($skills) > 0 ? count($skills) : 1 }};

            $('#add-skill').click(function() {
                const skillEntry = `
                    <div class="skill-entry mb-2 d-flex align-items-center">
                        <input type="text" name="skills[${skillIndex}][name]" class="form-control me-2" placeholder="Skill name" required>
                        <input type="number" name="skills[${skillIndex}][level]" class="form-control me-2" min="0" max="100" placeholder="Level (0-100)" style="width: 100px;" required>
                        <button type="button" class="btn btn-danger btn-sm remove-skill">Remove</button>
                    </div>`;
                $('#skills-container').append(skillEntry);
                skillIndex++;
            });

            $(document).on('click', '.remove-skill', function() {
                if ($('.skill-entry').length > 1) {
                    $(this).closest('.skill-entry').remove();
                }
            });
        });
    </script>
@endpush
@endsection