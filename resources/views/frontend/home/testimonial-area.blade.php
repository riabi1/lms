<section class="testimonial-area section-padding">
  <div class="container">
    <div class="section-heading text-center">
      <h5 class="ribbon ribbon-lg mb-2">Reviews</h5>
      <h2 class="section__title">Student's Feedback</h2>
      <span class="section-divider"></span>
    </div><!-- end section-heading -->
  </div><!-- end container -->
  <div class="container-fluid">
    <div class="testimonial-carousel owl-action-styled">
      @php
        // Récupérer les avis publiés avec la relation user
        $reviews = App\Models\Review::where('status', 1)->with('user')->get();
      @endphp
      @forelse ($reviews as $review)
        <div class="card card-item">
          <div class="card-body">
            <div class="media media-card align-items-center pb-3">
              <div class="media-img avatar-md">
                <img src="{{ $review->user && $review->user->photo ? asset('storage/upload/user_images/' . $review->user->photo) : asset('images/small-avatar-1.jpg') }}" 
                     alt="Testimonial avatar" 
                     class="rounded-full">
              </div>
              <div class="media-body">
                <h5>{{ $review->user->name ?? 'Anonymous' }}</h5>
                <div class="d-flex align-items-center pt-1">
                  <span class="lh-18 pr-2">Student</span>
                  <div class="review-stars">
                    @for ($i = 1; $i <= 5; $i++)
                      <span class="la la-star {{ $i <= $review->rating ? '' : 'text-muted' }}"></span>
                    @endfor
                  </div>
                </div>
              </div>
            </div><!-- end media -->
            <p class="card-text">
              {{ $review->comment }}
            </p>
          </div><!-- end card-body -->
        </div><!-- end card -->
      @empty
        <div class="text-center">
          <p>No reviews available yet.</p>
        </div>
      @endforelse
    </div><!-- end testimonial-carousel -->
  </div><!-- end container-fluid -->
</section><!-- end testimonial-area -->