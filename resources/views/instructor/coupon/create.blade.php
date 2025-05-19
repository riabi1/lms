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

                <!-- Coupon Discount -->
                <div class="col-md-6">
                    <label for="coupon_discount" class="form-label">Coupon Discount</label>
                    <input type="number" name="coupon_discount" class="form-control @error('coupon_discount') is-invalid @enderror" 
                           id="coupon_discount" value="{{ old('coupon_discount', '0.00') }}" min="0" step="0.01" required>
                    @error('coupon_discount')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Discount Type -->
                <div class="col-md-6">
                    <label for="discount_type" class="form-label">Discount Type</label>
                    <select name="discount_type" class="form-select @error('discount_type') is-invalid @enderror" id="discount_type" required>
                        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                    </select>
                    @error('discount_type')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Price Preview -->
                <div class="col-md-12">
                    <div id="price-preview" class="alert alert-info" style="display: none;">
                        <p>Original Price: <span id="original-price"></span></p>
                        <p>Discounted Price: <span id="discounted-price"></span></p>
                    </div>
                </div>

                <!-- Max Uses -->
                <div class="col-md-6">
                    <label for="max_uses" class="form-label">Max Uses (Optional)</label>
                    <input type="number" name="max_uses" class="form-control @error('max_uses') is-invalid @enderror" 
                           id="max_uses" value="{{ old('max_uses') }}" min="1" step="1">
                    @error('max_uses')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Courses -->
                <div class="col-md-6">
                    <label for="course_id" class="form-label">Course</label>
                    <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" id="course_id" required>
                        <option value="">Select a course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }} data-price="{{ $course->selling_price }}">
                                {{ $course->course_name }} ({{ number_format($course->selling_price, 2) }} USD)
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
                           value="{{ old('coupon_validity', \Carbon\Carbon::today()->format('Y-m-d')) }}" required>
                    @error('coupon_validity')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Coupon Status -->
                <div class="col-md-6">
                    <label for="status" class="form-label">Coupon Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" id="status" required>
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
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
        function updatePricePreview() {
            let coursePrice = parseFloat($('#course_id option:selected').data('price')) || 0;
            let currency = 'USD'; // Hardcoded since courses table has no currency column
            let discountValue = parseFloat($('#coupon_discount').val()) || 0;
            let discountType = $('#discount_type').val();
            let discountedPrice = coursePrice;

            if (discountType === 'fixed') {
                discountedPrice = coursePrice - discountValue;
            } else if (discountType === 'percentage') {
                discountedPrice = coursePrice * (1 - discountValue / 100);
            }

            // Round to 2 decimal places
            discountedPrice = Math.max(0, Math.round(discountedPrice * 100) / 100);

            if (coursePrice > 0 && !isNaN(discountValue)) {
                $('#price-preview').show();
                $('#original-price').text(`${coursePrice.toFixed(2)} ${currency}`);
                $('#discounted-price').text(`${discountedPrice.toFixed(2)} ${currency}`);
            } else {
                $('#price-preview').hide();
            }
        }

        function validateDiscount() {
            let value = parseFloat($('#coupon_discount').val());
            let discountType = $('#discount_type').val();
            let coursePrice = parseFloat($('#course_id option:selected').data('price')) || 0;
            $('#coupon_discount').removeClass('is-invalid');
            $('#coupon_discount').next('.invalid-feedback').remove();

            if (value < 0) {
                $('#coupon_discount').addClass('is-invalid');
                $('#coupon_discount').after('<span class="invalid-feedback d-block">Discount must be at least 0.</span>');
                return false;
            }
            if (discountType === 'percentage' && value > 100) {
                $('#coupon_discount').addClass('is-invalid');
                $('#coupon_discount').after('<span class="invalid-feedback d-block">Percentage discount cannot exceed 100%.</span>');
                return false;
            }
            if (discountType === 'fixed' && value > coursePrice && coursePrice > 0) {
                $('#coupon_discount').addClass('is-invalid');
                $('#coupon_discount').after('<span class="invalid-feedback d-block">Fixed discount cannot exceed course price of ' + coursePrice + '.</span>');
                return false;
            }
            return true;
        }

        // Update price preview and validate on input/change
        $('#coupon_discount, #discount_type, #course_id').on('input change', function() {
            validateDiscount();
            updatePricePreview();
        });

        // Initial update
        updatePricePreview();

        $('#myForm').on('submit', function(e) {
            if (!validateDiscount()) {
                e.preventDefault();
            }
        });
    });
</script>

@endsection