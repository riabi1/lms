@extends('User.layout.User_layout')
@section('userdashboard')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
  .rating-stars i {
    font-size: 1.2em;
  }
</style>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-10">
      <div class="card">
        <div class="card-header">
          <h4>My Reviews</h4>
        </div>

        <div class="card-body">
          @if (session('message'))
            <script>
              toastr.options = { "closeButton": true, "progressBar": true };
              toastr["{{ session('alert-type') }}"]("{{ session('message') }}");
            </script>
          @endif

          @if ($reviews->isEmpty())
            <p class="text-center">You haven’t submitted any reviews yet.</p>
          @else
            <div class="table-responsive">
              <table class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Course</th>
                    <th>Instructor</th>
                    <th>Comment</th>
                    <th>Rating</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($reviews as $key => $review)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ $review->course->course_name }}</td>
                      <td>{{ $review->instructor->name ?? 'N/A' }}</td>
                      <td>{{ $review->comment }}</td>
                      <td class="rating-stars">
                        @for ($i = 1; $i <= 5; $i++)
                          <i class="bx bxs-star {{ $i <= $review->rating ? 'text-warning' : 'text-secondary' }}"></i>
                        @endfor
                      </td>
                      <td>
                        @if ($review->status)
                          <span class="badge bg-success">Approved</span>
                        @else
                          <span class="badge bg-warning">Pending</span>
                        @endif
                      </td>
                      <td>
                        <a href="{{ route('user.reviews.edit', $review->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('user.reviews.destroy', $review->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this review?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection