@extends('frontend.dashboard.user_dashboard')
@section('userdashboard')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h4>Edit Review for {{ $review->course->course_name }}</h4>
        </div>
        <div class="card-body">
          <form action="{{ route('user.review.update', $review->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group mb-3">
              <label for="comment">Comment</label>
              <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" rows="3" required>{{ old('comment', $review->comment) }}</textarea>
              @error('comment')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group mb-3">
              <label for="rate">Rating</label>
              <select class="form-control @error('rate') is-invalid @enderror" id="rate" name="rate" required>
                @for ($i = 1; $i <= 5; $i++)
                  <option value="{{ $i }}" {{ old('rate', $review->rating) == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                  @endfor
              </select>
              @error('rate')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Review</button>
            <a href="{{ route('user.reviews') }}" class="btn btn-secondary">Cancel</a>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection