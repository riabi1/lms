@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.quiz.index') }}">All Quizzes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $quiz->title }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('instructor.quiz.edit', $quiz->id) }}" class="btn btn-info">Edit Quiz</a>
                <form action="{{ route('instructor.quiz.destroy', $quiz->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this quiz?');">Delete Quiz</button>
                </form>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-3">{{ $quiz->title }}</h4>

            <!-- Messages Flash -->
            @if (session('message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Course:</strong> {{ $quiz->course->course_name ?? 'N/A' }}</p>
                    <p><strong>Description:</strong> {{ $quiz->description ?? 'No description provided' }}</p>
                    <p><strong>Time Limit:</strong> {{ $quiz->time_limit ? $quiz->time_limit . ' minutes' : 'No limit' }}</p>
                    <p><strong>Total Questions:</strong> {{ $quiz->questions->count() }}</p>
                </div>
            </div>

            <h5 class="mt-4">Questions</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Options</th>
                            <th>Correct Answer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quiz->questions as $index => $question)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $question->question_text }}</td>
                                <td>{{ implode(', ', $question->options) }}</td>
                                <td><span class="badge bg-success">{{ $question->correct_answer }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No questions found for this quiz.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <a href="{{ route('instructor.quiz.index') }}" class="btn btn-secondary">Back to Quizzes</a>
            </div>
        </div>
    </div>
</div>

@endsection