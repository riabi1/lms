@extends('Instructor.layout.Instructor_layout')

@section('instructor')
<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Course Questions</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <!-- No additional actions needed -->
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Questions by Course</h5>
            @if ($courses->isEmpty())
                <div class="alert alert-info" role="alert">
                    No courses or questions found.
                </div>
            @else
                <div class="accordion" id="courseQuestionsAccordion" style="overflow-x: auto;">
                    @foreach ($courses as $course)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $course->id }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $course->id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $course->id }}">
                                    {{ $course->course_name }} ({{ $course->questions->count() }} Questions)
                                </button>
                            </h2>
                            <div id="collapse{{ $course->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ $course->id }}" data-bs-parent="#courseQuestionsAccordion">
                                <div class="accordion-body">
                                    @if ($course->questions->isEmpty())
                                        <p class="text-muted">No questions for this course.</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Student</th>
                                                        <th>Question</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                        <th>Answer</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($course->questions as $question)
                                                        <tr data-question-id="{{ $question->id }}">
                                                            <td>{{ $question->id }}</td>
                                                            <td>{{ $question->user->name ?? 'N/A' }}</td>
                                                            <td>{{ \Illuminate\Support\Str::limit($question->question_text, 50) }}</td>
                                                            <td>
                                                                <span class="badge {{ $question->status === 'answered' ? 'bg-success' : ($question->status === 'pending' ? 'bg-warning' : 'bg-secondary') }}">
                                                                    {{ ucfirst($question->status) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $question->created_at->format('d M Y') }}</td>
                                                            <td>
                                                                <button class="btn btn-primary btn-sm answer-question" data-question-id="{{ $question->id }}" title="Answer Question">
                                                                    <i class="bi bi-reply"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <!-- Inline Answer Section -->
                                                        <tr class="answer-section" id="answer-section-{{ $question->id }}" style="display: none;">
                                                            <td colspan="6">
                                                                <div class="p-3">
                                                                    <p><strong>Student:</strong> {{ $question->user->name ?? 'N/A' }}</p>
                                                                    <p><strong>Course:</strong> {{ $course->course_name ?? 'N/A' }}</p>
                                                                    <p><strong>Question:</strong> {{ $question->question_text ?? 'N/A' }}</p>
                                                                    <p><strong>Status:</strong> <span class="badge {{ $question->status === 'answered' ? 'bg-success' : ($question->status === 'pending' ? 'bg-warning' : 'bg-secondary') }}">{{ ucfirst($question->status) }}</span></p>
                                                                    <p><strong>Date:</strong> {{ $question->created_at->format('d M Y') }}</p>
                                                                    <div class="answers-list" id="answers-list-{{ $question->id }}">
                                                                        @if ($question->answers && $question->answers->isNotEmpty())
                                                                            @foreach ($question->answers as $answer)
                                                                                <div class="card mb-2" id="answer-{{ $answer->id }}">
                                                                                    <div class="card-body">
                                                                                        <p><strong>{{ $answer->instructor->name ?? 'N/A' }}</strong> <small class="text-muted">{{ $answer->created_at->format('d M Y, H:i') }}</small></p>
                                                                                        <p>{{ $answer->answer_text }}</p>
                                                                                        @if ($answer->instructor_id === Auth::guard('instructor')->id())
                                                                                            <div class="mt-2">
                                                                                                <button class="btn btn-sm btn-warning edit-answer" data-answer-id="{{ $answer->id }}" data-question-id="{{ $question->id }}" title="Edit Answer">
                                                                                                    <i class="bi bi-pencil"></i>
                                                                                                </button>
                                                                                                <button class="btn btn-sm btn-danger delete-answer" data-answer-id="{{ $answer->id }}" data-question-id="{{ $question->id }}" title="Delete Answer">
                                                                                                    <i class="bi bi-trash"></i>
                                                                                                </button>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        @else
                                                                            <p class="text-muted">No answers yet.</p>
                                                                        @endif
                                                                    </div>
                                                                    <!-- Edit Answer Form (Hidden by Default) -->
                                                                    <div class="edit-answer-form mt-4" id="edit-answer-form-{{ $question->id }}" style="display: none; max-width: 600px;">
                                                                        <h6>Edit Your Answer</h6>
                                                                        <form class="edit-answer-form-inner" data-question-id="{{ $question->id }}" action="{{ route('instructor.question.answer.update') }}" method="POST">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <input type="hidden" name="answer_id" class="edit-answer-id">
                                                                            <div class="mb-3">
                                                                                <textarea class="form-control no-tinymce" name="answer_text" rows="4" placeholder="Type your answer here..." required></textarea>
                                                                                <div class="invalid-feedback"></div>
                                                                            </div>
                                                                            <button type="submit" class="btn theme-btn">Update Answer</button>
                                                                            <button type="button" class="btn btn-secondary ms-2 cancel-edit" data-question-id="{{ $question->id }}">Cancel</button>
                                                                        </form>
                                                                    </div>
                                                                    @if ($question->status === 'pending')
                                                                        <div class="mt-4 answer-form-section" id="answer-form-section-{{ $question->id }}">
                                                                            <h6>Submit Your Answer</h6>
                                                                            <form class="answer-form" data-question-id="{{ $question->id }}" action="{{ route('instructor.question.answer.store') }}" method="POST">
                                                                                @csrf
                                                                                <input type="hidden" name="question_id" value="{{ $question->id }}">
                                                                                <div class="mb-3">
                                                                                    <textarea class="form-control no-tinymce" name="answer_text" rows="4" placeholder="Type your answer here..." required></textarea>
                                                                                    <div class="invalid-feedback"></div>
                                                                                </div>
                                                                                <button type="submit" class="btn theme-btn">Submit Answer</button>
                                                                                <button type="button" class="btn btn-secondary ms-2 cancel-answer" data-question-id="{{ $question->id }}">Cancel</button>
                                                                            </form>
                                                                        </div>
                                                                    @endif
                                                                </div>
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
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        console.log('Document ready, initializing answer handlers');
        const instructorId = {{ Auth::guard('instructor')->id() }};
        console.log('Authenticated instructor ID:', instructorId);

        // Toggle Inline Answer Section
        $('.answer-question').on('click', function(e) {
            e.preventDefault();
            const questionId = parseInt($(this).data('question-id'));
            console.log('Answer button clicked, question ID:', questionId);

            const $section = $(`#answer-section-${questionId}`);
            if ($section.is(':visible')) {
                $section.hide();
                console.log('Answer section hidden for question ID:', questionId);
            } else {
                $('.answer-section').hide(); // Hide other open sections
                $section.show();
                if ($(`#answer-form-section-${questionId}`).length) {
                    $section.find('.answer-form textarea').focus();
                }
                console.log('Answer section shown for question ID:', questionId);
            }
        });

        // Cancel Answer
        $('.cancel-answer').on('click', function(e) {
            e.preventDefault();
            const questionId = parseInt($(this).data('question-id'));
            $(`#answer-section-${questionId}`).hide();
            console.log('Answer section cancelled for question ID:', questionId);
        });

        // Submit New Answer
        $('.answer-form').on('submit', function(e) {
            e.preventDefault();
            console.log('Answer form submitted');

            const $form = $(this);
            const questionId = parseInt($form.data('question-id'));
            console.log('Submitting answer for question ID:', questionId);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Answer submission successful:', response);
                    toastr.success(response.message);

                    // Update answers list
                    const answerHtml = `
                        <div class="card mb-2 animate__animated animate__fadeIn" id="answer-${response.answer.id}">
                            <div class="card-body">
                                <p><strong>${response.answer.instructor_name}</strong> <small class="text-muted">${response.answer.created_at}</small></p>
                                <p>${response.answer.answer_text}</p>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-warning edit-answer" data-answer-id="${response.answer.id}" data-question-id="${questionId}" title="Edit Answer">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-answer" data-answer-id="${response.answer.id}" data-question-id="${questionId}" title="Delete Answer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>`;
                    $(`#answers-list-${questionId}`).html(answerHtml);

                    // Update table status
                    const $row = $(`tr[data-question-id="${questionId}"]`);
                    if ($row.length) {
                        $row.find('td:nth-child(4)').html('<span class="badge bg-success">Answered</span>');
                        $row.find('.answer-section').find('.badge').removeClass('bg-warning').addClass('bg-success').text('Answered');
                        console.log('Table status updated for question ID:', questionId);
                    } else {
                        console.warn('Table row not found for question ID:', questionId);
                    }

                    // Hide answer form
                    $form.closest('.answer-form-section').remove();
                    console.log('Answer form removed for question ID:', questionId);
                },
                error: function(xhr) {
                    console.error('Answer submission error:', xhr.responseJSON);
                    const errors = xhr.responseJSON?.errors || {};
                    $form.find('.form-control').removeClass('is-invalid');
                    $form.find('.invalid-feedback').empty();
                    Object.keys(errors).forEach(field => {
                        $form.find(`[name="${field}"]`).addClass('is-invalid');
                        $form.find(`[name="${field}"]`).next('.invalid-feedback').text(errors[field][0]);
                    });
                    toastr.error('Please correct the errors in the form.');
                }
            });
        });

        // Edit Answer
        $(document).on('click', '.edit-answer', function(e) {
            e.preventDefault();
            const answerId = parseInt($(this).data('answer-id'));
            const questionId = parseInt($(this).data('question-id'));
            console.log('Edit answer clicked, answer ID:', answerId, 'question ID:', questionId);

            const $answerCard = $(`#answer-${answerId}`);
            const answerText = $answerCard.find('p').eq(1).text(); // Get answer text
            const $editForm = $(`#edit-answer-form-${questionId}`);

            // Populate and show edit form
            $editForm.find('.edit-answer-id').val(answerId);
            $editForm.find('textarea').val(answerText);
            $editForm.show();
            $editForm.find('textarea').focus();
            $answerCard.hide(); // Hide answer card while editing
            console.log('Edit form shown for answer ID:', answerId);
        });

        // Cancel Edit
        $(document).on('click', '.cancel-edit', function(e) {
            e.preventDefault();
            const questionId = parseInt($(this).data('question-id'));
            const $editForm = $(`#edit-answer-form-${questionId}`);
            const $answerCard = $(`#answers-list-${questionId} .card:hidden`);

            $editForm.hide();
            $editForm.find('.form-control').removeClass('is-invalid');
            $editForm.find('.invalid-feedback').empty();
            $answerCard.show();
            console.log('Edit cancelled for question ID:', questionId);
        });

        // Submit Edited Answer
        $(document).on('submit', '.edit-answer-form-inner', function(e) {
            e.preventDefault();
            console.log('Edit answer form submitted');

            const $form = $(this);
            const questionId = parseInt($form.data('question-id'));
            const answerId = parseInt($form.find('.edit-answer-id').val());
            console.log('Submitting edited answer for answer ID:', answerId, 'question ID:', questionId);

            $.ajax({
                url: $form.attr('action'),
                method: 'PUT',
                data: $form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Answer update successful:', response);
                    toastr.success(response.message);

                    // Update answer card
                    const answerHtml = `
                        <div class="card mb-2 animate__animated animate__fadeIn" id="answer-${response.answer.id}">
                            <div class="card-body">
                                <p><strong>${response.answer.instructor_name}</strong> <small class="text-muted">${response.answer.created_at}</small></p>
                                <p>${response.answer.answer_text}</p>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-warning edit-answer" data-answer-id="${response.answer.id}" data-question-id="${questionId}" title="Edit Answer">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-answer" data-answer-id="${response.answer.id}" data-question-id="${questionId}" title="Delete Answer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>`;
                    $(`#answers-list-${questionId}`).html(answerHtml);

                    // Hide edit form
                    $form.closest('.edit-answer-form').hide();
                    console.log('Edit form hidden for answer ID:', answerId);
                },
                error: function(xhr) {
                    console.error('Answer update error:', xhr.responseJSON);
                    const errors = xhr.responseJSON?.errors || {};
                    $form.find('.form-control').removeClass('is-invalid');
                    $form.find('.invalid-feedback').empty();
                    Object.keys(errors).forEach(field => {
                        $form.find(`[name="${field}"]`).addClass('is-invalid');
                        $form.find(`[name="${field}"]`).next('.invalid-feedback').text(errors[field][0]);
                    });
                    toastr.error('Please correct the errors in the form.');
                }
            });
        });

        // Delete Answer
        $(document).on('click', '.delete-answer', function(e) {
            e.preventDefault();
            const answerId = parseInt($(this).data('answer-id'));
            const questionId = parseInt($(this).data('question-id'));
            console.log('Delete answer clicked, answer ID:', answerId, 'question ID:', questionId);

            Swal.fire({
                title: 'Are you sure?',
                text: 'This answer will be deleted permanently.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("instructor.question.answer.destroy") }}',
                        method: 'DELETE',
                        data: { answer_id: answerId },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('Answer deletion successful:', response);
                            toastr.success(response.message);

                            // Remove answer card
                            $(`#answer-${answerId}`).remove();

                            // Update status to pending if no answers remain
                            const $answersList = $(`#answers-list-${questionId}`);
                            if ($answersList.find('.card').length === 0) {
                                $answersList.html('<p class="text-muted">No answers yet.</p>');
                                const $row = $(`tr[data-question-id="${questionId}"]`);
                                $row.find('td:nth-child(4)').html('<span class="badge bg-warning">Pending</span>');
                                $row.find('.answer-section').find('.badge').removeClass('bg-success').addClass('bg-warning').text('Pending');
                                // Re-add answer form
                                const formHtml = `
                                    <div class="mt-4 answer-form-section" id="answer-form-section-${questionId}">
                                        <h6>Submit Your Answer</h6>
                                        <form class="answer-form" data-question-id="${questionId}" action="{{ route('instructor.question.answer.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="question_id" value="${questionId}">
                                            <div class="mb-3">
                                                <textarea class="form-control no-tinymce" name="answer_text" rows="4" placeholder="Type your answer here..." required></textarea>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <button type="submit" class="btn theme-btn">Submit Answer</button>
                                            <button type="button" class="btn btn-secondary ms-2 cancel-answer" data-question-id="${questionId}">Cancel</button>
                                        </form>
                                    </div>`;
                                $answersList.after(formHtml);
                                console.log('Answer form re-added for question ID:', questionId);
                            }
                            console.log('Answer deleted for answer ID:', answerId);
                        },
                        error: function(xhr) {
                            console.error('Answer deletion error:', xhr.responseJSON);
                            toastr.error('Failed to delete the answer.');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection