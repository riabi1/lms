@extends('User.layout.User_layout')

@section('title')
    My Courses | Easy Learning
@endsection

@section('userdashboard')
    <!-- Dépendances spécifiques à DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        .note-card {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s ease-in-out;
        }
        .note-card:hover {
            transform: translateY(-3px);
        }
        .note-card.favorite {
            border: 2px solid #ffc107;
        }
        .bg-light-blue { background-color: #e3f2fd; }
        .bg-light-green { background-color: #e8f5e9; }
        .bg-light-yellow { background-color: #fffde7; }
        .bg-light-pink { background-color: #fce4ec; }
        .note-header {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .note-actions {
            display: flex;
            gap: 10px;
        }
        .btn-add-note {
            background-color: #28a745;
            color: white;
            border-radius: 25px;
            padding: 8px 20px;
            transition: all 0.3s;
        }
        .btn-add-note:hover {
            background-color: #218838;
            transform: scale(1.05);
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
                        <li class="breadcrumb-item active" aria-current="page">My Courses</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Your Purchased Courses</h4>
                <div class="table-responsive">
                    <table id="coursesTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Image</th>
                                <th>Course Name</th>
                                <th>Instructor</th>
                                <th>Category</th>
                                <th>Price Paid</th>
                                <th>Action</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $key => $order)
                                @if ($order->course)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <img src="{{ $order->course->course_image ? Storage::url('upload/course_images/thumbnail/'.$order->course->course_image) : asset('images/no_image.jpg') }}" 
                                                 alt="{{ $order->course->course_name }}" 
                                                 style="width: 70px; height: 40px;" 
                                                 onerror="this.src='{{ asset('images/no_image.jpg') }}'">
                                        </td>
                                        <td>{{ $order->course->course_name }}</td>
                                        <td>{{ $order->course->instructor->name ?? 'Unknown Instructor' }}</td>
                                        <td>{{ $order->course->category->category_name ?? 'Uncategorized' }}</td>
                                        <td>${{ number_format($order->price, 2) }}</td>
                                        <td>
                                            @php
                                                $totalLectures = $order->course->sections->flatMap->lectures->count();
                                                $completedLectures = array_filter($order->progress ?? [], fn($completed) => $completed == 1);
                                                $progressPercentage = $totalLectures > 0 ? round((count($completedLectures) / $totalLectures) * 100) : 0;
                                                $learnUrl = url('mycourses/learn/'.$order->course->id.'/'.Str::slug($order->course->course_name));
                                            @endphp
                                            @if ($progressPercentage == 0)
                                                <a href="{{ $learnUrl }}" class="btn btn-success btn-sm">
                                                    <i class="bx bx-play"></i> Start Learning
                                                </a>
                                            @elseif ($progressPercentage < 100)
                                                <a href="{{ $learnUrl }}" class="btn btn-primary btn-sm">
                                                    <i class="bx bx-play"></i> Continue Learning
                                                </a>
                                            @else
                                                <a href="{{ $learnUrl }}" class="btn btn-info btn-sm">
                                                    <i class="bx bx-check"></i> Course Completed
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#notesModal{{ $order->course->id }}">
                                                <i class="bx bx-note"></i> Notes ({{ $order->course->notes->count() }})
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">You haven't purchased any courses yet. 
                                        <a href="{{ route('course.list') }}" class="text-primary">Explore Courses</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modals pour les notes -->
        @foreach ($orders as $order)
            @if ($order->course)
                <div class="modal fade" id="notesModal{{ $order->course->id }}" tabindex="-1" aria-labelledby="notesModalLabel{{ $order->course->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="notesModalLabel{{ $order->course->id }}">Notes for {{ $order->course->course_name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Formulaire pour ajouter une note -->
                                <form action="{{ route('mycourses.notes.store', $order->course->id) }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Title</label>
                                            <input type="text" class="form-control" name="title" placeholder="e.g., Key Concept" required>
                                            @error('title')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Due Date (optional)</label>
                                            <input type="date" class="form-control" name="due_date">
                                            @error('due_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Your Note</label>
                                        <textarea class="form-control" name="content" rows="3" placeholder="Write something personal..." required></textarea>
                                        @error('content')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" name="favorite" id="favorite{{ $order->course->id }}" value="1">
                                            <label class="form-check-label" for="favorite{{ $order->course->id }}">Mark as Favorite</label>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Note Color</label>
                                            <select class="form-control" name="color">
                                                <option value="bg-light-blue">Light Blue</option>
                                                <option value="bg-light-green">Light Green</option>
                                                <option value="bg-light-yellow">Light Yellow</option>
                                                <option value="bg-light-pink">Light Pink</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-add-note"><i class="bx bx-plus"></i> Add Note</button>
                                </form>

                                <!-- Messages de motivation EdaaLearning -->
                                <div class="alert alert-info mb-4">
                                    @php
                                        $motivations = [
                                            "With EdaaLearning, every note is a step towards mastering your skills!",
                                            "EdaaLearning is here for you: turn your ideas into knowledge!",
                                            "Stay motivated with EdaaLearning, your learning partner!",
                                            "At EdaaLearning, your notes reflect your journey to success!"
                                        ];
                                        $randomMotivation = $motivations[array_rand($motivations)];
                                    @endphp
                                    {{ $randomMotivation }}
                                </div>

                                <!-- Liste des notes existantes -->
                                <h6 class="mb-3">Your Personal Notes</h6>
                                @if ($order->course->notes->isEmpty())
                                    <p class="text-muted">No notes yet. Start your journey with EdaaLearning!</p>
                                @else
                                    @foreach ($order->course->notes as $note)
                                        <div class="note-card {{ $note->color ?? 'bg-light-blue' }} {{ $note->favorite ? 'favorite' : '' }} animate__animated animate__fadeIn" id="note-{{ $note->id }}">
                                            <!-- Affichage de la note -->
                                            <div class="note-display">
                                                <div class="note-header">
                                                    {{ $note->title }}
                                                    @if ($note->favorite)
                                                        <i class="bx bx-star text-warning ms-2"></i>
                                                    @endif
                                                </div>
                                                <p class="mb-2">{{ $note->content }}</p>
                                                @if ($note->due_date)
                                                    <small class="text-muted">Due: {{ \Carbon\Carbon::parse($note->due_date)->format('F j, Y') }}</small><br>
                                                @endif
                                                <small class="text-muted">Added on {{ $note->created_at->format('F j, Y, H:i') }}</small>
                                                <div class="note-actions mt-2">
                                                    <button class="btn btn-primary btn-sm edit-note-btn" data-note-id="{{ $note->id }}"><i class="bx bx-edit"></i> Edit</button>
                                                    <form action="{{ route('mycourses.notes.destroy', $note->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this note?')">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Formulaire d'édition (caché par défaut) -->
                                            <div class="note-edit-form" style="display: none;">
                                                <form action="{{ route('mycourses.notes.update', $note->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="mb-3">
                                                        <label class="form-label">Title</label>
                                                        <input type="text" class="form-control" name="title" value="{{ $note->title }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Your Note</label>
                                                        <textarea class="form-control" name="content" rows="3" required>{{ $note->content }}</textarea>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Due Date (optional)</label>
                                                            <input type="date" class="form-control" name="due_date" value="{{ $note->due_date ? $note->due_date->format('Y-m-d') : '' }}">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Note Color</label>
                                                            <select class="form-control" name="color">
                                                                <option value="bg-light-blue" {{ $note->color == 'bg-light-blue' ? 'selected' : '' }}>Light Blue</option>
                                                                <option value="bg-light-green" {{ $note->color == 'bg-light-green' ? 'selected' : '' }}>Light Green</option>
                                                                <option value="bg-light-yellow" {{ $note->color == 'bg-light-yellow' ? 'selected' : '' }}>Light Yellow</option>
                                                                <option value="bg-light-pink" {{ $note->color == 'bg-light-pink' ? 'selected' : '' }}>Light Pink</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 form-check">
                                                        <input type="checkbox" class="form-check-input" name="favorite" id="edit-favorite{{ $note->id }}" value="1" {{ $note->favorite ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="edit-favorite{{ $note->id }}">Mark as Favorite</label>
                                                    </div>
                                                    <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
                                                    <button type="button" class="btn btn-secondary btn-sm cancel-edit" data-note-id="{{ $note->id }}">Cancel</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Scripts spécifiques -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#coursesTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "lengthMenu": [5, 10, 25, 50],
                "pageLength": 10,
                "language": {
                    "search": "Search courses:",
                    "lengthMenu": "Show _MENU_ entries per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ courses",
                    "paginate": {
                        "previous": "Previous",
                        "next": "Next"
                    }
                }
            });

            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            // Afficher le formulaire d'édition
            $('.edit-note-btn').on('click', function() {
                const noteId = $(this).data('note-id');
                $('#note-' + noteId + ' .note-display').hide();
                $('#note-' + noteId + ' .note-edit-form').show();
            });

            // Annuler l'édition
            $('.cancel-edit').on('click', function() {
                const noteId = $(this).data('note-id');
                $('#note-' + noteId + ' .note-edit-form').hide();
                $('#note-' + noteId + ' .note-display').show();
            });
        });
    </script>
@endsection