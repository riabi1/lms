<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questions for {{ $course->course_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .question-card { margin-bottom: 20px; background: #f9f9f9; border-radius: 8px; }
        .answer-form { display: none; }
    </style>
</head>
<body>
    @include('instructor.header') <!-- Assuming you have a header include -->

    <div class="container mt-5">
        <h1 class="mb-4">Questions for {{ $course->course_name }}</h1>
        @if ($questions->isEmpty())
            <p class="text-muted">No questions have been asked yet.</p>
        @else
            @foreach ($questions as $question)
                <div class="question-card p-3">
                    <h5>{{ $question->subject }}</h5>
                    <p>{{ $question->question }}</p>
                    <small class="text-muted">Asked by {{ $question->user->name }} on {{ $question->created_at->format('F j, Y') }}</small>
                    @if ($question->answer)
                        <div class="mt-2 p-2 bg-light rounded">
                            <p>{{ $question->answer }}</p>
                            <small class="text-muted">Answered on {{ $question->updated_at->format('F j, Y') }}</small>
                        </div>
                    @else
                        <button class="btn btn-primary btn-sm mt-2 answer-btn" data-question-id="{{ $question->id }}">Answer</button>
                        <div class="answer-form mt-2" id="answer-form-{{ $question->id }}">
                            <form action="{{ route('instructor.question.answer', $question->id) }}" method="POST" class="answer-question-form">
                                @csrf
                                <textarea class="form-control mb-2" name="answer" rows="3" placeholder="Write your answer..." required></textarea>
                                <button type="submit" class="btn btn-success btn-sm">Submit Answer</button>
                                <button type="button" class="btn btn-secondary btn-sm cancel-answer" data-question-id="{{ $question->id }}">Cancel</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    <script>
        $(document).ready(function() {
            $('.answer-btn').on('click', function() {
                const questionId = $(this).data('question-id');
                $(`#answer-form-${questionId}`).slideDown();
                $(this).hide();
            });

            $('.cancel-answer').on('click', function() {
                const questionId = $(this).data('question-id');
                $(`#answer-form-${questionId}`).slideUp();
                $(`button[data-question-id="${questionId}"]`).show();
            });

            $('.answer-question-form').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            const questionId = form.closest('.question-card').find('.answer-btn').data('question-id');
                            $(`#answer-form-${questionId}`).slideUp().after(`
                                <div class="mt-2 p-2 bg-light rounded">
                                    <p>${response.answer}</p>
                                    <small class="text-muted">Answered on ${response.updated_at}</small>
                                </div>
                            `);
                            form.closest('.question-card').find('.answer-btn').remove();
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'An error occurred.');
                    }
                });
            });
        });
    </script>
</body>
</html>