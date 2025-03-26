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
                    <li class="breadcrumb-item active" aria-current="page">Quiz Details</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-3">{{ $quiz->title }}</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Course:</strong> {{ $quiz->course->course_name }}</p>
                    <p><strong>Description:</strong> {{ $quiz->description ?? 'N/A' }}</p>
                    <p><strong>Time Limit:</strong> {{ $quiz->time_limit ? $quiz->time_limit . ' minutes' : 'No limit' }}</p>
                </div>
            </div>

            <h5 class="mt-4">Questions</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Options</th>
                            <th>Correct Answer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quiz->questions as $question)
                            <tr>
                                <td>{{ $question->question_text }}</td>
                                <td>{{ implode(', ', $question->options) }}</td>
                                <td>{{ $question->correct_answer }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No questions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <a href="{{ route('instructor.quiz.edit', $quiz->id) }}" class="btn btn-info">Edit Quiz</a>
                <a href="{{ route('instructor.quiz.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>

@endsection