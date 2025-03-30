@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<div class="page-content">
    <!-- Breadcrumb -->
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
    <!-- End Breadcrumb -->

    <div class="card">
        <div class="card-body">
            <!-- Messages Flash -->
            @if (session('message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table id="quizTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Course</th>
                            <th>Title</th>
                            <th>Time Limit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quizzes as $key => $quiz)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $quiz->course->course_name ?? 'N/A' }}</td>
                                <td>{{ $quiz->title }}</td>
                                <td>{{ $quiz->time_limit ? $quiz->time_limit . ' min' : 'No Limit' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('instructor.quiz.show', $quiz->id) }}" class="btn btn-sm btn-primary" title="View">
                                            <i class="lni lni-eye"></i>
                                        </a>
                                        <a href="{{ route('instructor.quiz.edit', $quiz->id) }}" class="btn btn-sm btn-info" title="Edit">
                                            <i class="lni lni-eraser"></i>
                                        </a>
                                        <form action="{{ route('instructor.quiz.destroy', $quiz->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete" 
                                                    onclick="return confirm('Are you sure you want to delete this quiz?');">
                                                <i class="lni lni-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#quizTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "emptyTable": "No quizzes available in table"
            }
        });
    });
</script>

@endsection