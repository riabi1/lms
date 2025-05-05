@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<!-- Scripts pour validation -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.quiz.index') }}">All Quizzes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Quiz: {{ $quiz->title }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('instructor.quiz.show', $quiz->id) }}" class="btn btn-secondary px-5">Back to Quiz</a>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-3">Edit Quiz: {{ $quiz->title }}</h4>

            <!-- Messages Flash -->
            @if (session('message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Erreurs de validation -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form id="quizForm" action="{{ route('instructor.quiz.update', $quiz->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                        <select name="course_id" id="course_id" class="form-control @error('course_id') is-invalid @enderror" required>
                            <option value="">Select a course</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id', $quiz->course_id) == $course->id ? 'selected' : '' }}>
                                    {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title', $quiz->title) }}" placeholder="Enter quiz title" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3" placeholder="Describe the quiz">{{ old('description', $quiz->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                    <input type="number" name="time_limit" id="time_limit" class="form-control @error('time_limit') is-invalid @enderror" 
                           value="{{ old('time_limit', $quiz->time_limit) }}" min="1" max="1440" placeholder="e.g., 30">
                    @error('time_limit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div id="questions">
                    <h5>Questions</h5>
                    @foreach ($quiz->questions as $index => $question)
                        <div class="question-block mb-3 p-3 border rounded" data-index="{{ $index }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6>Question {{ $index + 1 }}</h6>
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion(this)" {{ $loop->first && $quiz->questions->count() == 1 ? 'disabled' : '' }}>Remove</button>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Question Text <span class="text-danger">*</span></label>
                                <input type="text" name="questions[{{ $index }}][question_text]" class="form-control @error("questions.$index.question_text") is-invalid @enderror" 
                                       value="{{ old("questions.$index.question_text", $question->question_text) }}" placeholder="Enter the question" required>
                                @error("questions.$index.question_text")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Options (comma-separated) <span class="text-danger">*</span></label>
                                <input type="text" name="questions[{{ $index }}][options]" class="form-control @error("questions.$index.options") is-invalid @enderror" 
                                       value="{{ old("questions.$index.options", implode(',', json_decode($question->options, true))) }}" placeholder="e.g., Option 1, Option 2, Option 3" required>
                                @error("questions.$index.options")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Correct Answer <span class="text-danger">*</span></label>
                                <input type="text" name="questions[{{ $index }}][correct_answer]" class="form-control @error("questions.$index.correct_answer") is-invalid @enderror" 
                                       value="{{ old("questions.$index.correct_answer", $question->correct_answer) }}" placeholder="e.g., Option 1" required>
                                @error("questions.$index.correct_answer")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex gap-3 mb-3">
                    <button type="button" class="btn btn-secondary" onclick="addQuestion()">Add Question</button>
                    <button type="submit" class="btn btn-primary">Update Quiz</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let questionCount = {{ $quiz->questions->count() }};

    function addQuestion() {
        const questionsDiv = document.getElementById('questions');
        const newQuestion = `
            <div class="question-block mb-3 p-3 border rounded" data-index="${questionCount}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6>Question ${questionCount + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion(this)">Remove</button>
                </div>
                <div class="mb-3">
                    <label class="form-label">Question Text <span class="text-danger">*</span></label>
                    <input type="text" name="questions[${questionCount}][question_text]" class="form-control" 
                           placeholder="Enter the question" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Options (comma-separated) <span class="text-danger">*</span></label>
                    <input type="text" name="questions[${questionCount}][options]" class="form-control" 
                           placeholder="e.g., Option 1, Option 2, Option 3" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Correct Answer <span class="text-danger">*</span></label>
                    <input type="text" name="questions[${questionCount}][correct_answer]" class="form-control" 
                           placeholder="e.g., Option 1" required>
                </div>
            </div>`;
        questionsDiv.insertAdjacentHTML('beforeend', newQuestion);
        questionCount++;
        updateRemoveButtons();
    }

    function removeQuestion(button) {
        button.closest('.question-block').remove();
        questionCount--;
        updateQuestionNumbers();
        updateRemoveButtons();
    }

    function updateQuestionNumbers() {
        const questionBlocks = document.querySelectorAll('.question-block');
        questionBlocks.forEach((block, index) => {
            block.dataset.index = index;
            block.querySelector('h6').textContent = `Question ${index + 1}`;
            const inputs = block.querySelectorAll('input');
            inputs[0].name = `questions[${index}][question_text]`;
            inputs[1].name = `questions[${index}][options]`;
            inputs[2].name = `questions[${index}][correct_answer]`;
        });
    }

    function updateRemoveButtons() {
        const removeButtons = document.querySelectorAll('.question-block .btn-danger');
        removeButtons.forEach((button, index) => {
            button.disabled = (index === 0 && removeButtons.length === 1);
        });
    }

    $(document).ready(function() {
        $('#quizForm').validate({
            rules: {
                course_id: { required: true },
                title: { required: true, maxlength: 255 },
                description: { maxlength: 1000 },
                time_limit: { min: 1, max: 1440 },
                'questions[*][question_text]': { required: true, maxlength: 500 },
                'questions[*][options]': { required: true },
                'questions[*][correct_answer]': { required: true }
            },
            messages: {
                course_id: "Please select a course",
                title: { required: "Please enter a quiz title", maxlength: "Title cannot exceed 255 characters" },
                description: { maxlength: "Description cannot exceed 1000 characters" },
                time_limit: { min: "Time limit must be at least 1 minute", max: "Time limit cannot exceed 1440 minutes" },
                'questions[*][question_text]': { required: "Please enter the question text", maxlength: "Question text cannot exceed 500 characters" },
                'questions[*][options]': "Please provide at least one option",
                'questions[*][correct_answer]': "Please specify the correct answer"
            },
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.mb-3').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>

@endsection