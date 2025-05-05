<section class="testimonial-area section-padding">
    <div class="container">
        <div class="section-heading text-center">
            <h5 class="ribbon ribbon-lg mb-2">Reviews</h5>
            <h2 class="section__title">Student's Feedback</h2>
            <span class="section-divider"></span>
        </div>
    </div>
    <div class="container-fluid">
        <div class="testimonial-carousel owl-action-styled">
            @php
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
                                <h5><i class="la la-user mr-1"></i>{{ $review->user->name ?? 'Anonymous' }}</h5>
                                <div class="d-flex align-items-center pt-1">
                                    <span class="lh-18 pr-2"><i class="la la-graduation-cap mr-1"></i>Student</span>
                                    <div class="review-stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="la la-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></span>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="card-text">
                            <i class="la la-quote-left mr-1 text-muted"></i>{{ $review->comment }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center">
                    <p>No reviews available yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<style>
.card-item { 
    padding: 15px; 
    background: white; 
    border-radius: 5px; 
    box-shadow: 0 1px 5px rgba(0,0,0,0.1); 
    min-height: 180px; 
}
.media-img img { 
    width: 40px; 
    height: 40px; 
    border-radius: 50%; 
}
.media-body h5 { font-size: 16px; color: #333; }
.lh-18 { font-size: 13px; color: #666; }
.card-text { font-size: 13px; color: #666; }
.review-stars .la-star { font-size: 14px; }
@media (max-width: 768px) { 
    .card-item { min-height: 160px; } 
    .media-img img { width: 35px; height: 35px; }
}
</style>