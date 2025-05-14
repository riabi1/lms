@extends('Instructor.layout.Instructor_layout')

@section('instructor')
<div class="page-content">
  <!-- Breadcrumb -->
  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">All Courses</li>
        </ol>
      </nav>
    </div>
    <div class="ms-auto">
      <a href="{{ route('instructor.courses.create') }}" class="btn btn-primary px-5">Add Course</a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table id="example" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr>
              <th>Sl</th>
              <th>Image</th>
              <th>Course Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Discount</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($courses as $key => $item)
            <tr>
              <td>{{ $key + 1 }}</td>
              <td>
                <img src="{{ $item->course_image ? asset('upload/course_images/thumbnail/' . $item->course_image) : asset('upload/no_image.jpg') }}"
                  alt="{{ $item->course_name }}"
                  style="width: 70px; height: 40px; object-fit: cover;"
                  class="img-fluid"
                  onerror="this.src='{{ asset('upload/no_image.jpg') }}'">
              </td>
              <td>{{ $item->course_name }}</td>
              <td>{{ $item->category ? $item->category->category_name : 'No Category' }}</td>
              <td>{{ $item->selling_price ? number_format($item->selling_price, 2) . ' TND' : 'N/A' }}</td>
              <td>{{ $item->discount_price ? number_format($item->discount_price, 2) . ' TND' : 'N/A' }}</td>
              <td>
                <a href="{{ route('instructor.courses.show', $item->id) }}" class="btn btn-primary btn-sm" title="View"><i class="lni lni-eye"></i></a>
                <a href="{{ route('instructor.courses.edit', $item->id) }}" class="btn btn-info btn-sm" title="Edit"><i class="lni lni-eraser"></i></a>
                <a href="{{ route('instructor.course_sections.index', $item->id) }}" class="btn btn-warning btn-sm" title="Sections"><i class="lni lni-list"></i></a>
                <a href="{{ route('instructor.quiz.create', ['course_id' => $item->id]) }}" class="btn btn-success btn-sm" title="Create Quiz"><i class="lni lni-plus"></i></a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center">No courses found.</td>
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
    $('#example').DataTable({
      "order": [
        [0, "asc"]
      ], // Sort by "Sl" column by default
      "pageLength": 10 // Entries per page
    });
  });
</script>
@endsection