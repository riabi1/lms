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
        <label class="form-label">Report Category</label>
        <select class="form-control" name="report_category_id" required>
          <option value="" {{ old('report_category_id') == '' ? 'selected' : '' }}>Select a Category</option>
          @foreach($reportCategories as $category)
          <option value="{{ $category->id }}" {{ old('report_category_id') == $category->id ? 'selected' : '' }}>
            {{ $category->name }}
          </option>
          @endforeach
        </select>
        @error('report_category_id')
        <div class="text-danger">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
          <label for="course_id" class="form-label">Related Course (Optional)</label>
          <select name="course_id" id="course_id" class="form-control">
            <option value="">No Course</option>
            @foreach ($courses as $course)
            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
              {{ $course->course_title }}
            </option>
            @endforeach
          </select>
          @error('course_id')
          <small class="text-danger">{{ $message }}</small>
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