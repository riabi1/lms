@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $course->course_name }} - Sections & Lectures</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">Add Section</button>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="row">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="{{ $course->course_image ? asset('storage/upload/course_images/thumbnail/' . $course->course_image) : asset('upload/no_image.jpg') }}"
                             class="rounded-circle p-1 border" width="90" height="90" alt="{{ $course->course_name }}">
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mt-0">{{ $course->course_name }}</h5>
                            <p class="mb-0">{{ $course->course_title }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Flash -->
            @if (session('message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @forelse ($sections as $key => $item)
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $item->section_title }}</h6>
                            <div class="btn-group">
                                <a href="{{ route('instructor.course_sections.edit', [$course->id, $item->id]) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('instructor.course_sections.destroy', [$course->id, $item->id]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this section and its lectures?');">Delete</button>
                                </form>
                                <button class="btn btn-sm btn-primary" onclick="toggleLectureForm('lectureContainer{{ $key }}')">Add Lecture</button>
                            </div>
                        </div>

                        <!-- Lectures existantes -->
                        <div class="mt-3">
                            @forelse ($item->lectures as $lecture)
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                    <span><strong>{{ $loop->iteration }}.</strong> {{ $lecture->lecture_title }}</span>
                                    <div class="btn-group">
                                        <a href="{{ route('instructor.course_lectures.show', [$course->id, $lecture->id]) }}" class="btn btn-sm btn-primary">View</a>
                                        <a href="{{ route('instructor.course_lectures.edit', [$course->id, $lecture->id]) }}" class="btn btn-sm btn-info">Edit</a>
                                        <form action="{{ route('instructor.course_lectures.destroy', [$course->id, $lecture->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this lecture?');">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">No lectures available for this section.</p>
                            @endforelse
                        </div>

                        <!-- Formulaire dynamique pour ajouter une leçon -->
                        <div id="lectureContainer{{ $key }}" class="mt-3" style="display: none;">
                            <form id="lectureForm{{ $key }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                <input type="hidden" name="section_id" value="{{ $item->id }}">

                                <h6>Lecture Content</h6>
                                <div class="border p-3 mb-3 bg-light">
                                    <div class="mb-3">
                                        <label class="form-label">Lecture Title <span class="text-danger">*</span></label>
                                        <input type="text" name="lecture_title" class="form-control" placeholder="Enter Lecture Title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Content</label>
                                        <textarea name="content" class="form-control" placeholder="Enter Lecture Content"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Video URL</label>
                                        <input type="url" name="url" class="form-control" placeholder="Add Video URL">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Main Video (MP4/WebM, max 100MB)</label>
                                        <input type="file" name="video" class="form-control" accept="video/mp4,video/webm">
                                    </div>
                                </div>

                                <h6>Additional Resources</h6>
                                <div class="border p-3 mb-3 bg-light">
                                    <div class="mb-3">
                                        <label class="form-label">Additional Video (MP4/WebM, max 100MB)</label>
                                        <input type="file" name="additional_video" class="form-control" accept="video/mp4,video/webm">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Resource File (PDF/DOC/JPG/PNG, max 20MB)</label>
                                        <input type="file" name="file_path" class="form-control" accept=".pdf,.doc,.docx,image/jpeg,image/png">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">External Link</label>
                                        <input type="url" name="external_link" class="form-control" placeholder="Add External Link">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Resources Description</label>
                                        <textarea name="resources_description" class="form-control" placeholder="Describe the resources"></textarea>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-primary" onclick="saveLecture('{{ $course->id }}', '{{ $item->id }}', 'lectureForm{{ $key }}')">Save Lecture</button>
                                <button type="button" class="btn btn-secondary" onclick="toggleLectureForm('lectureContainer{{ $key }}')">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info mt-3" role="alert">
                    No sections available for this course. Add a section to get started.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal for Adding Section -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSectionModalLabel">Add Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sectionForm" action="{{ route('instructor.course_sections.store', $course->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="section_title" class="form-label">Section Title <span class="text-danger">*</span></label>
                        <input type="text" name="section_title" class="form-control @error('section_title') is-invalid @enderror" id="section_title" value="{{ old('section_title') }}" required>
                        @error('section_title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Section</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleLectureForm(containerId) {
        const container = document.getElementById(containerId);
        container.style.display = container.style.display === 'none' || container.style.display === '' ? 'block' : 'none';
    }

    function saveLecture(courseId, sectionId, formId) {
        const form = document.getElementById(formId);
        const formData = new FormData(form);

        fetch("{{ route('instructor.course_lectures.store', $course->id) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            if (data.success) {
                Toast.fire({
                    icon: 'success',
                    title: data.success
                }).then(() => {
                    window.location.reload(); // Recharge la page pour afficher la nouvelle leçon
                });
            } else {
                Toast.fire({
                    icon: 'error',
                    title: data.error || 'Error saving lecture'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!',
            });
        });
    }
</script>
@endsection