@extends('instructor.layout.instructor_layout')

@section('instructor')
<div class="page-content">
  <!-- Breadcrumb -->
  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item">
            <a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('instructor.reports.index') }}">My Reports</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">Submit New Report</li>
        </ol>
      </nav>
    </div>
  </div>

  <!-- Report Form -->
  <div class="card">
    <div class="card-header">
      <h4>Submit a New Report</h4>
    </div>
    <div class="card-body">
      @if (session('error'))
      <div class="alert alert-danger mb-3">{{ session('error') }}</div>
      @endif

      <form action="{{ route('instructor.reports.store') }}" method="POST">
        @csrf

        <div class="mb-3">
          <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
          <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
          @error('title')
          <small class="text-danger">{{ $message }}</small>
          @enderror
        </div>

        <div class="mb-3">
          <label for="report_category_id" class="form-label">Category <span class="text-danger">*</span></label>
          <select name="report_category_id" id="report_category_id" class="form-control" required>
            <option value="" disabled selected>Select a category</option>
            @foreach ($reportCategories as $category)
            <option value="{{ $category->id }}" {{ old('report_category_id') == $category->id ? 'selected' : '' }}>
              {{ $category->name }}
            </option>
            @endforeach
          </select>
          @error('report_category_id')
          <small class="text-danger">{{ $message }}</small>
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
          <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
          <textarea name="description" id="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
          @error('description')
          <small class="text-danger">{{ $message }}</small>
          @enderror
        </div>

        <div class="mb-3">
          <button type="submit" class="btn btn-primary">Submit Report</button>
          <a href="{{ route('instructor.reports.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection