@extends('frontend.master')

@section('title')
    Blog List | Easy Learning
@endsection

@section('home')
<!-- ================================
    START BREADCRUMB AREA
================================= -->
<section class="breadcrumb-area section-padding img-bg-2">
    <div class="overlay"></div>
    <div class="container">
        <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
            <div class="section-heading">
                <h2 class="section__title text-white">Blog List</h2>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li>Blog</li>
                <li>Blog List</li>
            </ul>
        </div><!-- end breadcrumb-content -->
    </div><!-- end container -->
</section><!-- end breadcrumb-area -->

<!-- ================================
    START BLOG AREA
================================= -->
<section class="blog-area pt-5 pb-5">
    <div class="container">
        <!-- Filtre par catégories -->
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="fs-18 font-weight-bold mb-3">Filter by Category</h4>
                <div class="d-flex flex-wrap" id="category-filter">
                    <a href="#" class="btn btn-sm btn-outline-secondary mr-2 mb-2 filter-btn {{ !request('category') ? 'active' : '' }}" data-category="">All</a>
                    @foreach($categories as $category)
                        <a href="#" class="btn btn-sm btn-outline-secondary mr-2 mb-2 filter-btn {{ request('category') == $category->id ? 'active' : '' }}" data-category="{{ $category->id }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Liste des blogs -->
        <div class="row" id="blog-list">
            @forelse($blogPosts as $post)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card card-item shadow-sm">
                        <div class="card-image">
                            <a href="{{ route('blog.detail', $post->slug) }}" class="d-block">
                                <img class="card-img-top" src="{{ $post->image ? asset('upload/blog-posts/' . $post->image) : asset('upload/no_image.jpg') }}" alt="{{ $post->title }}" style="height: 200px; object-fit: cover;">
                            </a>
                            <div class="course-badge-labels">
                                <div class="course-badge">{{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y') }}</div>
                            </div>
                        </div><!-- end card-image -->
                        <div class="card-body p-3">
                            <h5 class="card-title fs-16">
                                <a href="{{ route('blog.detail', $post->slug) }}">{{ Str::limit($post->title, 40) }}</a>
                            </h5>
                            <ul class="generic-list-item generic-list-item-bullet d-flex align-items-center fs-13 text-muted">
                                <li class="mr-3">By <a href="{{ route('instructor.details', $post->instructor_id) }}" class="text-primary">{{ $post->author_name }}</a></li>
                                <li>{{ $post->category_name }}</li>
                            </ul>
                            <div class="d-flex justify-content-between align-items-center pt-2">
                                <a href="{{ route('blog.detail', $post->slug) }}" class="btn theme-btn btn-sm">
                                    Read More <i class="la la-arrow-right icon ml-1"></i>
                                </a>
                            </div>
                        </div><!-- end card-body -->
                    </div><!-- end card -->
                </div><!-- end col -->
            @empty
                <div class="col-12 text-center">
                    <p class="fs-16 text-muted">No blog posts available.</p>
                </div>
            @endforelse
        </div><!-- end row -->

        <!-- Pagination -->
        <div class="text-center pt-4" id="pagination">
            {{ $blogPosts->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div><!-- end container -->
</section><!-- end blog-area -->
@endsection

@section('scripts')
    @parent
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Gestion du clic sur les boutons de filtre
            $('.filter-btn').on('click', function(e) {
                e.preventDefault();
                
                // Supprimer la classe active de tous les boutons et l'ajouter au bouton cliqué
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');

                // Récupérer la catégorie sélectionnée
                let categoryId = $(this).data('category');

                // Requête AJAX
                $.ajax({
                    url: '{{ route('blog.list') }}',
                    type: 'GET',
                    data: { category: categoryId },
                    success: function(response) {
                        // Mettre à jour la liste des blogs
                        let blogList = $('#blog-list');
                        blogList.empty(); // Vider la liste actuelle

                        if (response.blogs.length > 0) {
                            response.blogs.forEach(function(post) {
                                let blogHtml = `
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="card card-item shadow-sm">
                                            <div class="card-image">
                                                <a href="${post.slug ? '{{ route('blog.detail', '') }}/' + post.slug : '#'}" class="d-block">
                                                    <img class="card-img-top" src="${post.image ? '{{ asset('upload/blog-posts') }}/' + post.image : '{{ asset('upload/no_image.jpg') }}'}" alt="${post.title}" style="height: 200px; object-fit: cover;">
                                                </a>
                                                <div class="course-badge-labels">
                                                    <div class="course-badge">${new Date(post.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</div>
                                                </div>
                                            </div>
                                            <div class="card-body p-3">
                                                <h5 class="card-title fs-16">
                                                    <a href="${post.slug ? '{{ route('blog.detail', '') }}/' + post.slug : '#'}">${post.title.length > 40 ? post.title.substring(0, 40) + '...' : post.title}</a>
                                                </h5>
                                                <ul class="generic-list-item generic-list-item-bullet d-flex align-items-center fs-13 text-muted">
                                                    <li class="mr-3">By <a href="${post.instructor_id ? '{{ route('instructor.details', '') }}/' + post.instructor_id : '#'}" class="text-primary">${post.author_name}</a></li>
                                                    <li>${post.category_name}</li>
                                                </ul>
                                                <div class="d-flex justify-content-between align-items-center pt-2">
                                                    <a href="${post.slug ? '{{ route('blog.detail', '') }}/' + post.slug : '#'}" class="btn theme-btn btn-sm">
                                                        Read More <i class="la la-arrow-right icon ml-1"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                blogList.append(blogHtml);
                            });
                        } else {
                            blogList.append('<div class="col-12 text-center"><p class="fs-16 text-muted">No blog posts available.</p></div>');
                        }

                        // Mettre à jour la pagination
                        $('#pagination').html(response.pagination);
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        alert('An error occurred while filtering blogs.');
                    }
                });
            });

            // Gestion de la pagination via AJAX
            $(document).on('click', '#pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#blog-list').html('');
                        response.blogs.forEach(function(post) {
                            let blogHtml = `
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card card-item shadow-sm">
                                        <div class="card-image">
                                            <a href="${post.slug ? '{{ route('blog.detail', '') }}/' + post.slug : '#'}" class="d-block">
                                                <img class="card-img-top" src="${post.image ? '{{ asset('upload/blog-posts') }}/' + post.image : '{{ asset('upload/no_image.jpg') }}'}" alt="${post.title}" style="height: 200px; object-fit: cover;">
                                            </a>
                                            <div class="course-badge-labels">
                                                <div class="course-badge">${new Date(post.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</div>
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            <h5 class="card-title fs-16">
                                                <a href="${post.slug ? '{{ route('blog.detail', '') }}/' + post.slug : '#'}">${post.title.length > 40 ? post.title.substring(0, 40) + '...' : post.title}</a>
                                            </h5>
                                            <ul class="generic-list-item generic-list-item-bullet d-flex align-items-center fs-13 text-muted">
                                                <li class="mr-3">By <a href="${post.instructor_id ? '{{ route('instructor.details', '') }}/' + post.instructor_id : '#'}" class="text-primary">${post.author_name}</a></li>
                                                <li>${post.category_name}</li>
                                            </ul>
                                            <div class="d-flex justify-content-between align-items-center pt-2">
                                                <a href="${post.slug ? '{{ route('blog.detail', '') }}/' + post.slug : '#'}" class="btn theme-btn btn-sm">
                                                    Read More <i class="la la-arrow-right icon ml-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('#blog-list').append(blogHtml);
                        });
                        $('#pagination').html(response.pagination);
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                    }
                });
            });
        });
    </script>
@endsection