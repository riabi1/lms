@extends('admin.layout.Admin_layout')

@section('admin')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        .comment-card {
            border-left: 4px solid;
            transition: all 0.2s;
        }
        .comment-card.approved {
            border-left-color: #28a745;
            background-color: #f4fff4;
        }
        .comment-card.pending {
            border-left-color: #ffc107;
            background-color: #fffdf0;
        }
        .comment-card .form-check-input {
            cursor: pointer;
            transform: scale(1.5); /* Match courses template checkbox size */
        }
        .comment-card .delete-btn {
            color: #dc3545;
        }
        .comment-card .delete-btn:hover {
            color: #b02a37;
        }
        .comment-card.reply {
            margin-left: 20px;
        }
        /* Toastr Customization (matching courses template) */
        #toast-container > .toast-success {
            background-color: #28a745 !important;
            color: white !important;
        }
        #toast-container > .toast-error {
            background-color: #dc3545 !important;
            color: white !important;
        }
    </style>

    <div class="page-content">
        <!-- Breadcrumb -->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Comments & Replies</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-body">
                @if (session('message'))
                    <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show mb-4" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Blog Post Accordion -->
                <div class="accordion" id="commentsAccordion">
                    @forelse ($groupedItems as $group)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-{{ $group['post_id'] }}">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#collapse-{{ $group['post_id'] }}" 
                                        aria-expanded="true" 
                                        aria-controls="collapse-{{ $group['post_id'] }}">
                                    <strong>{{ $group['post_title'] }}</strong> ({{ $group['items']->count() }} comments/replies)
                                </button>
                            </h2>
                            <div id="collapse-{{ $group['post_id'] }}" 
                                 class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                 aria-labelledby="heading-{{ $group['post_id'] }}" 
                                 data-bs-parent="#commentsAccordion">
                                <div class="accordion-body">
                                    @foreach ($group['items'] as $item)
                                        <div class="card comment-card mb-2 {{ $item['approved'] ? 'approved' : 'pending' }} {{ $item['type'] === 'reply' ? 'reply' : '' }}">
                                            <div class="card-body d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <strong>{{ $item['user_name'] }}</strong> 
                                                    <span class="badge {{ $item['type'] === 'comment' ? 'bg-primary' : 'bg-info' }} ms-2">
                                                        {{ ucfirst($item['type']) }}
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($item['created_at'])->format('d M Y H:i') }}
                                                        | Parent ID: 
                                                        @if ($item['parent_id'])
                                                            <a href="#" 
                                                               data-bs-toggle="modal" 
                                                               data-bs-target="#viewModal-{{ $item['parent_id'] }}-comment">
                                                                {{ $item['parent_id'] }}
                                                            </a>
                                                        @else
                                                            —
                                                        @endif
                                                    </small>
                                                    <p class="mb-0 mt-1">{{ Str::limit($item['message'], 100, '...') }}</p>
                                                </div>
                                                <div class="d-flex align-items-center ms-3">
                                                    <form action="{{ route('admin.comments.toggle', ['id' => $item['id'], 'type' => $item['type']]) }}" 
                                                          method="POST" 
                                                          class="toggle-approval-form me-3">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input status-toggle" 
                                                                   type="checkbox" 
                                                                   role="switch" 
                                                                   id="statusToggle{{ $item['id'] }}"
                                                                   data-comment-id="{{ $item['id'] }}"
                                                                   {{ $item['approved'] ? 'checked' : '' }}>
                                                            <label class="form-check-label" 
                                                                   for="statusToggle{{ $item['id'] }}">
                                                                <span class="badge {{ $item['approved'] ? 'bg-success' : 'bg-danger' }}">
                                                                    {{ $item['approved'] ? 'Approved' : 'Pending' }}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </form>
                                                    <a href="#" 
                                                       class="me-2" 
                                                       data-bs-toggle="modal" 
                                                       data-bs-target="#viewModal-{{ $item['id'] }}-{{ $item['type'] }}"
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form action="{{ route('admin.comments.destroy', [$item['id'], $item['type']]) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Are you sure you want to delete this {{ $item['type'] }}?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="delete-btn border-0 bg-transparent" 
                                                                title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal for Viewing Details -->
                                        <div class="modal fade" 
                                             id="viewModal-{{ $item['id'] }}-{{ $item['type'] }}" 
                                             tabindex="-1" 
                                             aria-labelledby="viewModalLabel-{{ $item['id'] }}-{{ $item['type'] }}" 
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="viewModalLabel-{{ $item['id'] }}-{{ $item['type'] }}">
                                                            {{ ucfirst($item['type']) }} Details
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-3 fw-bold">Blog Post:</div>
                                                            <div class="col-md-9">{{ $item['post_title'] }}</div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-md-3 fw-bold">User:</div>
                                                            <div class="col-md-9">{{ $item['user_name'] }}</div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-md-3 fw-bold">Type:</div>
                                                            <div class="col-md-9 text-capitalize">{{ $item['type'] }}</div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-md-3 fw-bold">Parent Comment:</div>
                                                            <div class="col-md-9">
                                                                @if ($item['parent_id'])
                                                                    <a href="#" 
                                                                       data-bs-toggle="modal" 
                                                                       data-bs-target="#viewModal-{{ $item['parent_id'] }}-comment"
                                                                       title="View Parent Comment">
                                                                        Comment ID: {{ $item['parent_id'] }}
                                                                    </a>
                                                                @else
                                                                    None
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-md-3 fw-bold">Message:</div>
                                                            <div class="col-md-9">{!! nl2br(e($item['message'])) !!}</div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-md-3 fw-bold">Created At:</div>
                                                            <div class="col-md-9">{{ \Carbon\Carbon::parse($item['created_at'])->format('d M Y H:i') }}</div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3 fw-bold">Status:</div>
                                                            <div class="col-md-9 text-capitalize">{{ $item['approved'] ? 'Approved' : 'Pending' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info text-center">
                            No comments or replies found.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            // Configure Toastr options (matching courses template)
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 3000,
                preventDuplicates: true
            };

            // Handle toggle switch clicks
            $('.status-toggle').on('change', function() {
                const $toggle = $(this);
                const commentId = $toggle.data('comment-id');
                const isChecked = $toggle.is(':checked');
                const originalState = !isChecked;
                const $form = $toggle.closest('.toggle-approval-form');
                const $badge = $form.find('.badge');

                console.log('Toggle clicked:', { commentId, isChecked }); // Debug log

                $toggle.prop('disabled', true);

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: {
                        comment_id: commentId,
                        is_checked: isChecked ? 1 : 0,
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT'
                    },
                    success: function(response) {
                        console.log('AJAX Success:', response); // Debug log
                        if (response.success) {
                            // Update badge
                            if (response.approved) {
                                $badge.removeClass('bg-danger').addClass('bg-success').text('Approved');
                            } else {
                                $badge.removeClass('bg-success').addClass('bg-danger').text('Pending');
                            }

                            // Update card styling
                            const $card = $form.closest('.comment-card');
                            $card.removeClass('approved pending');
                            $card.addClass(response.approved ? 'approved' : 'pending');

                            // Show Toastr success message
                            toastr.success(response.message || 'Status updated successfully.');
                        } else {
                            toastr.error(response.message || 'Failed to update status.');
                            $toggle.prop('checked', originalState);
                        }
                        $toggle.prop('disabled', false);
                    },
                    error: function(xhr) {
                        console.log('AJAX Error:', xhr.responseJSON); // Debug log
                        toastr.error('Failed to update status: ' + (xhr.responseJSON?.message || 'Unknown error'));
                        $toggle.prop('checked', originalState);
                        $toggle.prop('disabled', false);
                    }
                });
            });

            // Display session-based Toastr notifications
            @if (session('message'))
                toastr.{{ session('alert-type', 'info') }}("{{ session('message') }}");
            @endif
        });
    </script>
@endsection