@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Quizzes</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('instructor.quiz.create') }}" class="btn btn-primary px-5">Add Quiz</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Course</th>
                            <th>Title</th>
                            <th>Time Limit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quizzes as $key => $quiz)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $quiz->course->course_name }}</td>
                            <td>{{ $quiz->title }}</td>
                            <td>{{ $quiz->time_limit ? $quiz->time_limit . ' min' : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('instructor.quiz.show', $quiz->id) }}" class="btn btn-primary btn-sm" title="View"><i class="lni lni-eye"></i></a>
                                <a href="{{ route('instructor.quiz.edit', $quiz->id) }}" class="btn btn-info btn-sm" title="Edit"><i class="lni lni-eraser"></i></a>
                                <form action="{{ route('instructor.quiz.destroy', $quiz->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this quiz?');"><i class="lni lni-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No quizzes found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- DataTables Scripts -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable();
    });
</script>

@endsection