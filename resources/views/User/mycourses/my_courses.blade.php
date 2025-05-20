@extends('User.layout.User_layout')

@section('title')
    My Courses | Easy Learning
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        .notes-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        .notes-list {
            max-height: 400px;
            overflow-y: auto;
            margin-top: 15px;
        }
        .note-card {
            border-left-width: 5px;
            transition: transform 0.2s;
        }
        .note-card:hover {
            transform: translateY(-2px);
        }
        .bg-light-blue { background-color: #e7f1ff; border-left-color: #007bff; }
        .bg-light-green { background-color: #e6ffed; border-left-color: #28a745; }
        .bg-light-yellow { background-color: #fff3cd; border-left-color: #ffc107; }
        .bg-light-pink { background-color: #ffeef5; border-left-color: #e83e8c; }
        .favorite-star { cursor: pointer; font-size: 1.2rem; }
        .tags-container { display: flex; flex-wrap: wrap; gap: 5px; }
        .tag { background-color: #007bff; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.85rem; }
        .filter-buttons { margin-bottom: 15px; }
        .add-note-btn { margin-right: 10px; }
        .note-form-container { display: none; }
        @media (max-width: 991px) {
            .notes-section { padding: 15px; }
            .notes-list { max-height: 300px; }
            .note-card .card-body { padding: 12px; }
        }
    </style>
@endpush

@section('userdashboard')
    <div class="page-content">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Courses</li>
                </ol>
            </nav>
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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $key => $order)
                                @if ($order->course)
                                    <tr class="course-row" data-course-id="{{ $order->course->id }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <img src="{{ $order->course->course_image ? asset('upload/course_images/thumbnail/' . $order->course->course_image) : asset('upload/no_image.jpg') }}" 
                                                 alt="{{ $order->course->course_name }}" 
                                                 style="width: 70px; height: 40px; object-fit: cover;" 
                                                 class="img-fluid"
                                                 onerror="this.src='{{ asset('upload/no_image.jpg') }}'">
                                        </td>
                                        <td>{{ $order->course->course_name }}</td>
                                        <td>{{ $order->instructor->name ?? 'N/A' }}</td>
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
                                            <button class="btn btn-secondary btn-sm mt-1 toggle-notes" 
                                                    data-course-id="{{ $order->course->id }}" 
                                                    data-course-name="{{ $order->course->course_name }}"
                                                    data-notes='{{ json_encode($order->course->notes->where('user_id', Auth::id())->toArray()) }}'
                                                    aria-expanded="false">
                                                <i class="bx bx-note"></i> Notes
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        You haven't purchased any courses yet. 
                                        <a href="{{ route('course.list') }}" class="text-primary">Explore Courses</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            const table = $('#coursesTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                lengthMenu: [5, 10, 25, 50],
                pageLength: 10,
                language: {
                    search: "Search courses:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ courses",
                    paginate: { previous: "Previous", next: "Next" }
                },
                rowReorder: false,
                drawCallback: function() {
                    attachNotesEventListeners();
                }
            });

            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            function getNotesSectionHtml(courseId, courseName, notes) {
                return `
                    <div class="notes-section" id="notes-section-${courseId}">
                        <div class="p-3">
                            <h5>Notes for ${courseName}</h5>
                            <div class="filter-buttons">
                                <button class="btn btn-outline-secondary btn-sm filter-all" data-course-id="${courseId}">All Notes</button>
                                <button class="btn btn-outline-warning btn-sm filter-favorites" data-course-id="${courseId}">Favorites</button>
                                <button class="btn btn-outline-primary btn-sm add-note-btn" data-course-id="${courseId}">Add Note</button>
                            </div>
                            <div class="note-form-container" id="noteFormContainer-${courseId}">
                                <form id="noteForm-${courseId}" action="/mycourses/notes/store/${courseId}" method="POST" class="mb-3">
                                    <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                                    <input type="hidden" name="_method" id="formMethod-${courseId}" value="POST">
                                    <input type="hidden" name="note_id" id="noteId-${courseId}">
                                    <div class="mb-3">
                                        <label for="title-${courseId}" class="form-label">Note Title</label>
                                        <input type="text" name="title" id="title-${courseId}" class="form-control" required maxlength="255">
                                    </div>
                                    <div class="mb-3">
                                        <label for="content-${courseId}" class="form-label">Content</label>
                                        <textarea name="content" id="content-${courseId}" class="form-control" rows="4" required maxlength="1000"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="due_date-${courseId}" class="form-label">Due Date (Optional)</label>
                                        <input type="date" name="due_date" id="due_date-${courseId}" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="color-${courseId}" class="form-label">Color</label>
                                        <select name="color" id="color-${courseId}" class="form-select">
                                            <option value="bg-light-blue">Light Blue</option>
                                            <option value="bg-light-green">Light Green</option>
                                            <option value="bg-light-yellow">Light Yellow</option>
                                            <option value="bg-light-pink">Light Pink</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tags-${courseId}" class="form-label">Tags (comma-separated, optional)</label>
                                        <input type="text" name="tags" id="tags-${courseId}" class="form-control" placeholder="e.g., urgent,review">
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" name="favorite" id="favorite-${courseId}" class="form-check-input" value="1">
                                        <label for="favorite-${courseId}" class="form-check-label">Mark as Favorite</label>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" id="submitNoteBtn-${courseId}" class="btn btn-primary">Save Note</button>
                                        <button type="button" id="resetNoteBtn-${courseId}" class="btn btn-outline-secondary">Reset</button>
                                    </div>
                                </form>
                            </div>
                            <div id="notesList-${courseId}" class="notes-list"></div>
                        </div>
                    </div>`;
            }

            function attachNotesEventListeners() {
                $('.toggle-notes').off('click').on('click', function(e) {
                    e.preventDefault();
                    const $row = $(this).closest('tr');
                    const row = table.row($row);
                    const courseId = $(this).data('course-id');
                    const courseName = $(this).data('course-name');
                    const notes = $(this).data('notes') || [];
                    const $button = $(this);

                    if (row.child.isShown()) {
                        row.child.hide();
                        $row.removeClass('shown');
                        $button.attr('aria-expanded', 'false');
                    } else {
                        table.rows().every(function() {
                            if (this.child.isShown()) {
                                this.child.hide();
                                $(this.node()).removeClass('shown');
                                $(this.node()).find('.toggle-notes').attr('aria-expanded', 'false');
                            }
                        });
                        row.child(getNotesSectionHtml(courseId, courseName, notes)).show();
                        $row.addClass('shown');
                        $button.attr('aria-expanded', 'true');
                        $(`#noteFormContainer-${courseId}`).hide(); // Hide form by default
                        populateNotes(courseId, notes);
                        attachChildEventListeners(courseId);
                    }
                });
            }

            function attachChildEventListeners(courseId) {
                const $form = $(`#noteForm-${courseId}`);
                const $notesList = $(`#notesList-${courseId}`);
                const $formContainer = $(`#noteFormContainer-${courseId}`);

                // Add Note Button
                $(`.add-note-btn[data-course-id="${courseId}"]`).on('click', function() {
                    $formContainer.show();
                    $form[0].reset();
                    $form.data('mode', 'create');
                    $form.attr('action', `/mycourses/notes/store/${courseId}`);
                    $(`#formMethod-${courseId}`).val('POST');
                    $(`#submitNoteBtn-${courseId}`).text('Save Note');
                    $(`#noteId-${courseId}`).remove();
                });

                // Reset Form
                $(`#resetNoteBtn-${courseId}`).on('click', function() {
                    $form[0].reset();
                    $form.data('mode', 'create');
                    $form.attr('action', `/mycourses/notes/store/${courseId}`);
                    $(`#formMethod-${courseId}`).val('POST');
                    $(`#submitNoteBtn-${courseId}`).text('Save Note');
                    $(`#noteId-${courseId}`).remove();
                    $formContainer.hide(); // Hide form on reset
                });

                // Handle Note Form Submission
                $form.off('submit').on('submit', function(e) {
                    e.preventDefault();
                    const mode = $(this).data('mode') || 'create';
                    const url = mode === 'create' ? $(this).attr('action') : `/mycourses/notes/update/${$(`#noteId-${courseId}`).val()}`;
                    const formData = $(this).serializeArray();
                    if (mode === 'edit') {
                        formData.push({ name: '_method', value: 'PUT' });
                    }

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            toastr.success(response.success);
                            const notes = $(`#coursesTable tr[data-course-id="${courseId}"] .toggle-notes`).data('notes') || [];
                            if (mode === 'create') {
                                notes.push(response.note);
                            } else {
                                const index = notes.findIndex(n => n.id === response.note.id);
                                if (index !== -1) notes[index] = response.note;
                            }
                            $(`#coursesTable tr[data-course-id="${courseId}"] .toggle-notes`).data('notes', notes);
                            populateNotes(courseId, notes);
                            $form[0].reset();
                            $form.data('mode', 'create');
                            $form.attr('action', `/mycourses/notes/store/${courseId}`);
                            $(`#formMethod-${courseId}`).val('POST');
                            $(`#submitNoteBtn-${courseId}`).text('Save Note');
                            $(`#noteId-${courseId}`).remove();
                            $formContainer.hide(); // Hide form after submission
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'An error occurred.');
                        }
                    });
                });

                // Filter Notes - All Notes
                $(`.filter-all[data-course-id="${courseId}"]`).on('click', function() {
                    const notes = $(`#coursesTable tr[data-course-id="${courseId}"] .toggle-notes`).data('notes') || [];
                    populateNotes(courseId, notes);
                    $formContainer.hide(); // Hide form when showing all notes
                });

                // Filter Notes - Favorites
                $(`.filter-favorites[data-course-id="${courseId}"]`).on('click', function() {
                    const notes = $(`#coursesTable tr[data-course-id="${courseId}"] .toggle-notes`).data('notes') || [];
                    const favoriteNotes = notes.filter(note => note.favorite);
                    populateNotes(courseId, favoriteNotes);
                    $formContainer.hide(); // Hide form when showing favorites
                });

                // Edit Note
                $notesList.off('click', '.edit-note').on('click', '.edit-note', function() {
                    const note = $(this).data('note');
                    $form.data('mode', 'edit');
                    $form.attr('action', `/mycourses/notes/update/${note.id}`);
                    $(`#formMethod-${courseId}`).val('PUT');
                    if (!$(`#noteId-${courseId}`).length) {
                        $form.append(`<input type="hidden" name="note_id" id="noteId-${courseId}" value="${note.id}">`);
                    }
                    $(`#title-${courseId}`).val(note.title);
                    $(`#content-${courseId}`).val(note.content);
                    $(`#due_date-${courseId}`).val(note.due_date || '');
                    $(`#favorite-${courseId}`).prop('checked', note.favorite ? true : false);
                    $(`#color-${courseId}`).val(note.color);
                    $(`#tags-${courseId}`).val(note.tags || '');
                    $(`#submitNoteBtn-${courseId}`).text('Update Note');
                    $formContainer.show(); // Show form when editing
                });

                // Toggle Favorite
                $notesList.off('click', '.favorite-star').on('click', '.favorite-star', function() {
                    const noteId = $(this).data('id');
                    $.ajax({
                        url: `/mycourses/notes/toggle-favorite/${noteId}`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            toastr.success(response.success);
                            const notes = $(`#coursesTable tr[data-course-id="${courseId}"] .toggle-notes`).data('notes') || [];
                            const note = notes.find(n => n.id === noteId);
                            if (note) note.favorite = response.favorite;
                            populateNotes(courseId, notes);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'An error occurred.');
                        }
                    });
                });

                // Delete Note
                $notesList.off('click', '.delete-note').on('click', '.delete-note', function() {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'You want to delete this note?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const noteId = $(this).data('id');
                            $.ajax({
                                url: `/mycourses/notes/destroy/${noteId}`,
                                method: 'POST',
                                data: {
                                    _method: 'DELETE',
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    toastr.success(response.success);
                                    const notes = $(`#coursesTable tr[data-course-id="${courseId}"] .toggle-notes`).data('notes') || [];
                                    const updatedNotes = notes.filter(n => n.id !== noteId);
                                    $(`#coursesTable tr[data-course-id="${courseId}"] .toggle-notes`).data('notes', updatedNotes);
                                    populateNotes(courseId, updatedNotes);
                                },
                                error: function(xhr) {
                                    toastr.error(xhr.responseJSON?.message || 'An error occurred.');
                                }
                            });
                        }
                    });
                });
            }

            function populateNotes(courseId, notes) {
                const $notesList = $(`#notesList-${courseId}`);
                $notesList.empty();
                if (notes.length === 0) {
                    $notesList.append('<p class="text-muted">No notes available.</p>');
                    return;
                }
                notes.forEach(note => {
                    const tagsHtml = note.tags ? note.tags.split(',').map(tag => `<span class="tag">${tag.trim()}</span>`).join(' ') : '';
                    const noteHtml = `
                        <div class="card note-card mb-2 ${note.color}">
                            <div class="card-body">
                                <h6 class="card-title">
                                    ${note.title}
                                    <i class="bx ${note.favorite ? 'bxs-star' : 'bx-star'} favorite-star text-${note.favorite ? 'warning' : 'muted'}" data-id="${note.id}"></i>
                                </h6>
                                <p class="card-text">${note.content}</p>
                                ${note.due_date ? `<p class="card-text"><small class="text-muted">Due: ${note.due_date}</small></p>` : ''}
                                ${tagsHtml ? `<div class="tags-container">${tagsHtml}</div>` : ''}
                                <div class="mt-2">
                                    <button class="edit-note btn btn-sm btn-outline-primary" data-course-id="${courseId}" data-note='${JSON.stringify(note)}'>Edit</button>
                                    <button class="delete-note btn btn-sm btn-outline-danger" data-id="${note.id}">Delete</button>
                                </div>
                            </div>
                        </div>`;
                    $notesList.append(noteHtml);
                });
            }

            attachNotesEventListeners();
        });
    </script>
@endpush