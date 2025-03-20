@extends('Instructor.layout.Instructor_layout')
@section('instructor')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<div class="page-content">
    <div class="row">
        <div class="col-12"> 
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset($course->course_image) }}" class="rounded-circle p-1 border" width="90" height="90" alt="{{ $course->course_name }}">
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mt-0">{{ $course->course_name }}</h5>
                            <p class="mb-0">{{ $course->course_title }}</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Add Section</button>
                    </div>
                </div>
            </div>

            @forelse ($sections as $key => $item)  
            <div class="container">
                <div class="main-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body p-4 d-flex justify-content-between">
                                    <h6>{{ $item->section_title }}</h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <form action="{{ route('instructor.course_sections.destroy', [$course->id, $item->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger px-2 ms-auto" onclick="return confirm('Are you sure you want to delete this section?');">Delete Section</button> 
                                        </form>
                                        <a class="btn btn-primary ms-2" onclick="addLectureDiv({{ $course->id }}, {{ $item->id }}, 'lectureContainer{{ $key }}')" id="addLectureBtn{{ $key }}">Add Lecture</a>
                                    </div>                      
                                </div>

                                <div class="courseHide" id="lectureContainer{{ $key }}">
                                    <div class="container">
                                        @forelse ($item->lectures as $lecture) 
                                        <div class="lectureDiv mb-3 d-flex align-items-center justify-content-between">
                                            <div>
                                                <strong>{{ $loop->iteration }}. {{ $lecture->lecture_title }}</strong>
                                            </div>
                                            <div class="btn-group">
                                                <a href="{{ route('instructor.course_lectures.edit', [$course->id, $lecture->id]) }}" class="btn btn-sm btn-primary">Edit</a> 
                                                <form action="{{ route('instructor.course_lectures.destroy', [$course->id, $lecture->id]) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#" class="btn btn-sm btn-danger" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this lecture?')) this.closest('form').submit();">Delete</a>
                                                </form>
                                            </div> 
                                        </div> 
                                        @empty
                                            <p>No lectures available for this section.</p>
                                        @endforelse 
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
                <p>No sections available for this course.</p>
            @endforelse   
        </div>
    </div> 
</div>   

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"> 
                <form action="{{ route('instructor.course_sections.store', $course->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <div class="form-group mb-3">
                        <label for="section_title" class="form-label">Course Section</label>
                        <input type="text" name="section_title" class="form-control @error('section_title') is-invalid @enderror" id="section_title" value="{{ old('section_title') }}">
                        @error('section_title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="modal-footer">   
                        <button type="submit" class="btn btn-primary">Save changes</button> 
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function addLectureDiv(courseId, sectionId, containerId) {
        const lectureContainer = document.getElementById(containerId);
        lectureContainer.style.display = 'block';

        const newLectureDiv = document.createElement('div');
        newLectureDiv.classList.add('lectureDiv', 'mb-3');
        newLectureDiv.id = 'newLectureDiv';

        newLectureDiv.innerHTML = `
            <div class="container">
                <h6>Lecture Title</h6>
                <input type="text" class="form-control lecture-title" placeholder="Enter Lecture Title">
                <textarea class="form-control mt-2 lecture-content" placeholder="Enter Lecture Content"></textarea>
                <h6 class="mt-3">Add Video URL</h6>
                <input type="text" name="url" class="form-control lecture-url" placeholder="Add URL">
                <h6 class="mt-3">Upload Video (Optional)</h6>
                <input type="file" name="video" class="form-control lecture-video" accept="video/*">
                <button class="btn btn-primary mt-3" onclick="saveLecture('${courseId}', ${sectionId}, '${containerId}')">Save Lecture</button>
                <button class="btn btn-secondary mt-3" onclick="hideLectureContainer('${containerId}')">Cancel</button>
            </div>
        `;

        lectureContainer.appendChild(newLectureDiv);
    }

    function hideLectureContainer(containerId) {
        const lectureContainer = document.getElementById(containerId);
        const newLectureDiv = document.getElementById('newLectureDiv');
        if (newLectureDiv) newLectureDiv.remove();
        location.reload();
    }

    function saveLecture(courseId, sectionId, containerId) {
        const lectureContainer = document.getElementById(containerId);
        const lectureTitle = lectureContainer.querySelector('.lecture-title').value;
        const lectureContent = lectureContainer.querySelector('.lecture-content').value;
        const lectureUrl = lectureContainer.querySelector('.lecture-url').value;
        const lectureVideo = lectureContainer.querySelector('.lecture-video').files[0];

        let formData = new FormData();
        formData.append('course_id', courseId);
        formData.append('section_id', sectionId);
        formData.append('lecture_title', lectureTitle);
        formData.append('url', lectureUrl);
        formData.append('content', lectureContent);
        if (lectureVideo) formData.append('video', lectureVideo);

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
                timer: 6000
            });

            if (data.success) {
                Toast.fire({
                    icon: 'success',
                    title: data.success
                }).then(() => {
                    hideLectureContainer(containerId);
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