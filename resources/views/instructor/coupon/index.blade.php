@extends('Instructor.layout.Instructor_layout')
@section('instructor')

<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('instructor.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">All Coupons</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('instructor.coupon.create') }}" class="btn btn-primary px-5">Add Coupon</a>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Coupon Name</th>
                            <th>Coupon Discount</th>
                            <th>Coupon Status</th>
                            <th>Course</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coupons as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->coupon_name }}</td>
                                <td>{{ $item->coupon_discount }}%</td>
                                <td>
                                    @if ($item->coupon_validity >= Carbon\Carbon::now()->format('Y-m-d'))
                                        <span class="badge bg-success">Valid</span>
                                    @else
                                        <span class="badge bg-danger">Invalid</span>
                                    @endif
                                </td>
                                <td>{{ $item->course ? $item->course->course_name : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('instructor.coupon.edit', $item->id) }}" class="btn btn-info px-5">Edit</a>
                                    <form action="{{ route('instructor.coupon.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger px-5">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Inclure DataTables JS si utilisé -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable();
    });
</script>

@endsection