@extends('Instructor.layout.Instructor_layout')
@section('instructor')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Add Coupon</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">Add Coupon</h5>
            <form id="myForm" action="{{ route('instructor.coupon.store') }}" method="POST" class="row g-3">
                @csrf

                <!-- Coupon Name -->
                <div class="col-md-6">
                    <label for="coupon_name" class="form-label">Coupon Name</label>
                    <input type="text" name="coupon_name" class="form-control @error('coupon_name') is-invalid @enderror" 
                           id="coupon_name" value="{{ old('coupon_name') }}" required>
                    @error('coupon_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Coupon Discount -->
                <div class="col-md-6">
                    <label for="coupon_discount" class="form-label">Coupon Discount (%)</label>
                    <input type="number" name="coupon_discount" class="form-control @error('coupon_discount') is-invalid @enderror" 
                           id="coupon_discount" value="{{ old('coupon_discount') }}" min="0" max="100" step="1" required>
                    @error('coupon_discount')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Courses -->
                <div class="col-md-6">
                    <label for="course_id" class="form-label">Course</label>
                    <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" id="course_id" required>
                        <option value="">Select a course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Coupon Validity -->
                <div class="col-md-6">
                    <label for="coupon_validity" class="form-label">Coupon Validity Date</label>
                    <input type="date" name="coupon_validity" class="form-control @error('coupon_validity') is-invalid @enderror" 
                           id="coupon_validity" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" 
                           value="{{ old('coupon_validity') }}" required>
                    @error('coupon_validity')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit and Cancel Buttons -->
                <div class="col-md-12">
                    <div class="d-md-flex d-grid align-items-center gap-3">
                        <button type="submit" class="btn btn-primary px-4">Add Coupon</button>
                        <a href="{{ route('instructor.coupon.index') }}" class="btn btn-secondary px-4">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Validation en temps réel pour coupon_discount
        $('#coupon_discount').on('input', function() {
            let value = parseInt($(this).val());
            if (value < 0 || value > 100) {
                $(this).addClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
                $(this).after('<span class="invalid-feedback d-block">Discount must be between 0 and 100.</span>');
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });
    });
</script>

@endsection