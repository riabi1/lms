@extends('instructor.instructor_dashboard')
@section('instructor')
<div class="page-content">
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
                        @foreach ($courses as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td><img src="{{ asset($item->course_image) }}" alt="{{ $item->course_name }}" style="width: 70px; height:40px;" onerror="this.src='{{ asset('upload/no_image.jpg') }}'"></td>
                            <td>{{ $item->course_name }}</td> 
                            <td>{{ optional($item->category)->category_name ?? 'No Category' }}</td>
                            <td>{{ $item->selling_price }}</td>
                            <td>{{ $item->discount_price }}</td>
                            <td>
                                <a href="{{ route('instructor.courses.show', $item->id) }}" class="btn btn-primary btn-sm" title="View"><i class="lni lni-eye"></i></a>
                                <a href="{{ route('instructor.courses.edit', $item->id) }}" class="btn btn-info btn-sm" title="Edit"><i class="lni lni-eraser"></i></a>   
                                <form action="{{ route('instructor.courses.destroy', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this course?');"><i class="lni lni-trash"></i></button>
                                </form>
                                <a href="{{ route('instructor.course_sections.index', $item->id) }}" class="btn btn-warning btn-sm" title="Sections"><i class="lni lni-list"></i></a>                    
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection