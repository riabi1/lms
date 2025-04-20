@php
   // Fetch categories, default to empty collection if null
$categories = $categories ?? App\Models\Category::latest()->limit(6)->get() ?? collect();
@endphp

<section class="category-area pb-90px">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-9">
                <div class="category-content-wrap">
                    <div class="section-heading">
                        <h2 class="section__title">Popular Categories</h2>
                        <span class="section-divider"></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="category-btn-box text-right">
                    <a href="categories.html" class="btn theme-btn">See More <i class="la la-arrow-right icon ml-1"></i></a>
                </div>
            </div>
        </div>
        <div class="category-wrapper mt-30px">
            <div class="row">
                @forelse ($categories as $cat)
                    @php
                        // Fetch course count, default to 0 if null
                        $courseCount = App\Models\Course::whereHas('subcategory', function ($query) use ($cat) {
                            $query->where('category_id', $cat->id);
                        })->count() ?? 0;
                    @endphp
                    <div class="col-lg-4 responsive-column-half">
                        <div class="category-item card-preview" data-tooltip-content="#tooltip_content_{{ $cat->id }}">
                            <img class="cat__img lazy"
                                 src="{{ $cat->image ? asset('storage/upload/category_images/' . $cat->image) : asset('images/no_image.jpg') }}"
                                 data-src="{{ $cat->image ? asset('storage/upload/category_images/' . $cat->image) : asset('images/no_image.jpg') }}"
                                 alt="Category image"
                                 loading="lazy"
                                 onerror="this.src='{{ asset('images/no_image.jpg') }}'">
                            <div class="category-content">
                                <div class="category-inner">
                                    <h3 class="cat__title">
                                        <a href="{{ url('category/'.$cat->id.'/'.$cat->category_slug) }}">{{ $cat->category_name }}</a>
                                    </h3>
                                    <p class="cat__meta">{{ $courseCount }} courses</p>
                                    <a href="{{ url('category/'.$cat->id.'/'.$cat->category_slug) }}"
                                       class="btn theme-btn theme-btn-sm theme-btn-white">
                                        Explore<i class="la la-arrow-right icon ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-muted text-center">No categories available.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

<style>
.category-item {
    position: relative;
    overflow: hidden;
    border-radius: 8px; /* Optional: for rounded corners */
}

.cat__img {
    width: 100%;
    height: 200px; /* Set a fixed height - adjust as needed */
    object-fit: cover; /* Ensures images maintain aspect ratio while filling the space */
    display: block;
    transition: none; /* Removes any hover transition effects */
}

.category-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 15px;
    background: rgba(0, 0, 0, 0.6); /* Optional: semi-transparent background for text readability */
    color: white;
}
</style>