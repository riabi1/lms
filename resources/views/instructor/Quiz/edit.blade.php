@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.quiz.index') }}">All Quizzes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Quiz</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-3">Edit Quiz: {{ $quiz->title }}</h4>

            <!-- Affichage des erreurs de validation -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Affichage du message de succès (optionnel) -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('instructor.quiz.update', $quiz->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="course_id" class="form-label">Course</label>
                        <select name="course_id" id="course_id" class="form-control @error('course_id') is-invalid @enderror" required>
                            <option value="">Select a course</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id', $quiz->course_id) == $course->id ? 'selected' : '' }}>{{ $course->course_name }}</option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $quiz->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $quiz->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                    <input type="number" name="time_limit" id="time_limit" class="form-control @error('time_limit') is-invalid @enderror" value="{{ old('time_limit', $quiz->time_limit) }}" min="1">
                    @error('time_limit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div id="questions">
                    <h5>Questions</h5>
                    @foreach ($quiz->questions as $index => $question)
                        <div class="question-block mb-3 p-3 border rounded">
                            <div class="mb-3">
                                <label class="form-label">Question Text</label>
                                <input type="text" name="questions[{{ $index }}][question_text]" class="form-control @error("questions.$index.question_text") is-invalid @enderror" value="{{ old("questions.$index.question_text", $question->question_text) }}" required>
                                @error("questions.$index.question_text")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Options (comma-separated)</label>
                                <input type="text" name="questions[{{ $index }}][options]" class="form-control @error("questions.$index.options") is-invalid @enderror" value="{{ old("questions.$index.options", implode(',', $question->options)) }}" required>
                                @error("questions.$index.options")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Correct Answer</label>
                                <input type="text" name="questions[{{ $index }}][correct_answer]" class="form-control @error("questions.$index.correct_answer") is-invalid @enderror" value="{{ old("questions.$index.correct_answer", $question->correct_answer) }}" required>
                                @error("questions.$index.correct_answer")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-secondary mb-3" onclick="addQuestion()">Add Question</button>
                <button type="submit" class="btn btn-primary">Update Quiz</button>
            </form>
        </div>
    </div>
</div>

<script>
    let questionCount = {{ count($quiz->questions) }};
    function addQuestion() {
        const questionsDiv = document.getElementById('questions');
        const newQuestion = `
            <div class="question-block mb-3 p-3 border rounded">
                <div class="mb-3">
                    <label class="form-label">Question Text</label>
                    <input type="text" name="questions[${questionCount}][question_text]" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Options (comma-separated)</label>
                    <input type="text" name="questions[${questionCount}][options]" class="form-control" placeholder="Option 1, Option 2, Option 3" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Correct Answer</label>
                    <input type="text" name="questions[${questionCount}][correct_answer]" class="form-control" required>
                </div>
            </div>`;
        questionsDiv.insertAdjacentHTML('beforeend', newQuestion);
        questionCount++;
    }
</script>

@endsection