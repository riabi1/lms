@extends('instructor.instructor_dashboard')
@section('instructor')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<div class="page-content">
    <div class="row">
        <div class="col-12"> 
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset($course->course_image) }}" class="rounded-circle p-1 border" width="90" height="90" alt="...">
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mt-0">{{ $course->course_name }}</h5>
                            <p class="mb-0">{{ $course->course_title }}</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Add Section</button>
                    </div>
                </div>
            </div>

            {{-- Add Section and Lecture --}}
            @foreach ($section as $key => $item)  
            <div class="container">
                <div class="main-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body p-4 d-flex justify-content-between">
                                    <h6>{{ $item->section_title }}</h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <form action="{{ route('instructor.delete.section', ['id' => $item->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger px-2 ms-auto">Delete Section</button> 
                                        </form>
                                        <a class="btn btn-primary ms-2" onclick="addLectureDiv({{ $course->id }}, {{ $item->id }}, 'lectureContainer{{ $key }}')" id="addLectureBtn{{ $key }}">Add Lecture</a>
                                    </div>                      
                                </div>

                                <div class="courseHide" id="lectureContainer{{ $key }}">
                                    <div class="container">
                                        @foreach ($item->lectures as $lecture) 
                                        <div class="lectureDiv mb-3 d-flex align-items-center justify-content-between">
                                            <div>
                                                <strong>{{ $loop->iteration }}. {{ $lecture->lecture_title }}</strong>
                                            </div>
                                            <div class="btn-group">
                                                <a href="{{ route('instructor.edit.lecture', ['id' => $lecture->id]) }}" class="btn btn-sm btn-primary">Edit</a> 
                                                <a href="{{ route('instructor.delete.lecture', ['id' => $lecture->id]) }}" class="btn btn-sm btn-danger" id="delete">Delete</a>
                                            </div> 
                                        </div> 
                                        @endforeach 
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach   
            {{-- End Add Section and Lecture --}}
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
                <form action="{{ route('instructor.add.course.section') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $course->id }}">
                    <div class="form-group mb-3">
                        <label for="input1" class="form-label">Course Section</label>
                        <input type="text" name="section_title" class="form-control" id="input1">
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
        lectureContainer.style.display = 'block'; // Ensure container is visible

        const newLectureDiv = document.createElement('div');
        newLectureDiv.classList.add('lectureDiv', 'mb-3');
        newLectureDiv.id = 'newLectureDiv'; // Unique ID for targeting

        newLectureDiv.innerHTML = `
            <div class="container">
                <h6>Lecture Title</h6>
                <input type="text" class="form-control lecture-title" placeholder="Enter Lecture Title">
                <textarea class="form-control mt-2 lecture-content" placeholder="Enter Lecture Content"></textarea>
                <h6 class="mt-3">Add Video URL</h6>
                <input type="text" name="url" class="form-control lecture-url" placeholder="Add URL">
                <button class="btn btn-primary mt-3" onclick="saveLecture('${courseId}', ${sectionId}, '${containerId}')">Save Lecture</button>
                <button class="btn btn-secondary mt-3" onclick="hideLectureContainer('${containerId}')">Cancel</button>
            </div>
        `;

        lectureContainer.appendChild(newLectureDiv);
    }

    function hideLectureContainer(containerId) {
        const lectureContainer = document.getElementById(containerId);
        const newLectureDiv = document.getElementById('newLectureDiv');
        if (newLectureDiv) newLectureDiv.remove(); // Remove the new lecture div
        // lectureContainer.style.display = 'none'; // Uncomment if you want to hide the container
        location.reload(); // Optional: Remove if you don’t want full reload
    }

    function saveLecture(courseId, sectionId, containerId) {
        const lectureContainer = document.getElementById(containerId);
        const lectureTitle = lectureContainer.querySelector('.lecture-title').value;
        const lectureContent = lectureContainer.querySelector('.lecture-content').value;
        const lectureUrl = lectureContainer.querySelector('.lecture-url').value;

        fetch("{{ route('instructor.save.lecture') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                course_id: courseId,
                section_id: sectionId,
                lecture_title: lectureTitle,
                lecture_url: lectureUrl,
                content: lectureContent,
            }),
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);

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
                    hideLectureContainer(containerId); // Hide after success message
                });
            } else {
                Toast.fire({
                    icon: 'error',
                    title: 'Error saving lecture'
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