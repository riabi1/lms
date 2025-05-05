@extends('User.layout.user_layout')

@section('title')
    Submit a Report | Easy Learning
@endsection

@section('userdashboard')
    <div class="container py-4">
        <div class="card p-4">
            <h3 class="mb-4">Submit a Report</h3>

            @if(session('success'))
                <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger mt-3">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('report.submit') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Report Title</label>
                    <input class="form-control" type="text" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Report Type</label>
                    <select class="form-control" name="type" required>
                        <option value="" {{ old('type') == '' ? 'selected' : '' }}>Select a Type</option>
                        <option value="course_issue" {{ old('type') == 'course_issue' ? 'selected' : '' }}>Course Issue</option>
                        <option value="technical" {{ old('type') == 'technical' ? 'selected' : '' }}>Technical Problem</option>
                        <option value="technical" {{ old('type') == 'technical' ? 'selected' : '' }}>Technical Problem</option>
                        <option value="content_error" {{ old('type') == 'content_error' ? 'selected' : '' }}>Content Error</option>
                        <option value="billing" {{ old('type') == 'billing' ? 'selected' : '' }}>Billing Issue</option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('type')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Related Course (Optional)</label>
                    <select class="form-control" name="course_id">
                        <option value="">Select a Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->course_title }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="5" required>{{ old('description') }}</textarea>
                    <small class="text-muted">Please provide details about the issue or problem.</small>
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary" type="submit">Submit Report</button>
            </form>
        </div>
    </div>
@endsection