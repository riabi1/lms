@extends('User.layout.User_layout')

@section('title')
    My Quiz Attempts | Easy Learning
@endsection

@section('userdashboard')
    <!-- Dépendances spécifiques à DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        .quiz-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .quiz-table th, .quiz-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            vertical-align: middle; /* Alignement vertical pour les icônes */
        }
        .quiz-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .quiz-table tr:hover {
            background-color: #f1f1f1;
        }
        /* Styles pour Passed et Failed */
        .passed-row {
            background-color: #e8f5e9; /* Fond vert clair */
        }
        .failed-row {
            background-color: #fce4ec; /* Fond rouge clair */
        }
        .passed {
            color: #28a745;
            font-weight: bold;
        }
        .failed {
            color: #dc3545;
            font-weight: bold;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }
        .passed .status-badge {
            background-color: #28a745;
            color: white;
        }
        .failed .status-badge {
            background-color: #dc3545;
            color: white;
        }
        /* Styles pour DataTables */
        .dataTables_wrapper .dataTables_filter {
            float: right;
            margin-bottom: 15px;
            display: block !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            margin-left: 10px;
            width: 200px;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 5px 10px;
            margin: 0 2px;
            border-radius: 4px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #e9ecef;
            border: 1px solid #ddd;
        }
    </style>

    <div class="page-content">
        <!-- Breadcrumb -->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Quiz Attempts</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Your Completed Quizzes</h4>
                <div class="table-responsive">
                    @if ($quizAttempts->isEmpty())
                        <p class="text-muted">You haven’t attempted any quizzes yet. Start a course to take some quizzes!</p>
                    @else
                        <table id="quizTable" class="table table-striped table-bordered quiz-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Quiz Title</th>
                                    <th>Course</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                    <th>Completed At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($quizAttempts as $key => $attempt)
                                    <tr class="{{ $attempt->passed ? 'passed-row' : 'failed-row' }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $attempt->quiz->title }}</td>
                                        <td>{{ $attempt->quiz->course->course_name }}</td>
                                        <td>{{ $attempt->score }}%</td>
                                        <td class="{{ $attempt->passed ? 'passed' : 'failed' }}">
                                            <span class="status-badge">
                                                <i class="bx {{ $attempt->passed ? 'bx-check' : 'bx-x' }}"></i>
                                                {{ $attempt->passed ? 'Passed' : 'Failed' }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($attempt->completed_at)->format('F j, Y, H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts spécifiques à DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#quizTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "lengthMenu": [5, 10, 25, 50],
                "pageLength": 10,
                "language": {
                    "search": "Search quizzes:",
                    "lengthMenu": "Show _MENU_ entries per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ quiz attempts",
                    "paginate": {
                        "previous": "Previous",
                        "next": "Next"
                    }
                }
            });

            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif
        });
    </script>
@endsection