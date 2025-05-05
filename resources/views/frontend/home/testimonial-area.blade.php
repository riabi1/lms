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
.testimonial-area .card-item { 
    padding: 15px; 
    background: white; 
    border-radius: 5px; 
    box-shadow: 0 1px 5px rgba(0,0,0,0.1); 
    width: 300px; /* Fixed width for all cards */
    height: 250px; /* Fixed height for all cards */
    display: flex; 
    flex-direction: column; 
    justify-content: space-between; 
    overflow: hidden; /* Prevent content from overflowing */
}
.testimonial-area .card-body {
    display: flex;
    flex-direction: column;
    height: 100%; /* Ensure card-body takes full height */
}
.testimonial-area .media-img img { 
    width: 40px; 
    height: 40px; 
    border-radius: 50%; 
}
.testimonial-area .media-body h5 { 
    font-size: 16px; 
    color: #333; 
    white-space: nowrap; /* Prevent name from wrapping */
    overflow: hidden; 
    text-overflow: ellipsis; /* Truncate long names */
}
.testimonial-area .lh-18 { 
    font-size: 13px; 
    color: #666; 
}
.testimonial-area .card-text { 
    font-size: 13px; 
    color: #666; 
    flex-grow: 1; /* Allow text to take available space */
    overflow: hidden; /* Hide overflow text */
    text-overflow: ellipsis; /* Add ellipsis for truncated text */
    display: -webkit-box;
    -webkit-line-clamp: 4; /* Limit to 4 lines */
    -webkit-box-orient: vertical; /* Truncate vertically */
}
.testimonial-area .review-stars { 
    display: flex; 
    flex-wrap: nowrap; /* Prevent stars from wrapping to a new line */
    align-items: center; 
}
.testimonial-area .review-stars .la-star { 
    font-size: 14px; 
    margin-right: 2px; /* Small spacing between stars */
}
@media (max-width: 768px) { 
    .testimonial-area .card-item { 
        width: 250px; /* Slightly smaller width for mobile */
        height: 220px; /* Slightly smaller height for mobile */
    }
    .testimonial-area .media-img img { 
        width: 35px; 
        height: 35px; 
    }
    .testimonial-area .card-text {
        -webkit-line-clamp: 3; /* Reduce to 3 lines on mobile */
    }
    .testimonial-area .review-stars .la-star { 
        font-size: 12px; /* Slightly smaller stars on mobile */
        margin-right: 1px; 
    }
}
</style>
