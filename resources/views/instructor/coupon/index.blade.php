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
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Max Uses</th>
                            <th>Uses</th>
                            <th>Validity</th>
                            <th>Status</th>
                            <th>Course</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($coupons as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->code }}</td>
                                <td>
                                    {{ number_format($item->coupon_discount, 2) }}
                                    {{ $item->discount_type == 'percentage' ? '%' : 'Fixed' }}
                                </td>
                                <td>{{ $item->max_uses ?? 'Unlimited' }}</td>
                                <td>{{ $item->uses }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->coupon_validity)->format('d M Y') }}
                                    @if (\Carbon\Carbon::parse($item->coupon_validity)->isFuture() || \Carbon\Carbon::parse($item->coupon_validity)->isToday())
                                        <span class="badge bg-success ms-2">Valid</span>
                                    @else
                                        <span class="badge bg-danger ms-2">Expired</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status == 1)
                                        <span class="badge bg-primary">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $item->couponable ? $item->couponable->course_name : 'N/A' }}
                                </td>
                                <td>
                                    <a href="{{ route('instructor.coupon.edit', $item->id) }}" class="btn btn-info px-3">Edit</a>
                                    <form action="{{ route('instructor.coupon.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger px-3">Delete</button>
                                    </form>
                                    <form action="{{ route('instructor.coupon.toggleStatus', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to change the status of this coupon?');">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn {{ $item->status == 1 ? 'btn-warning' : 'btn-success' }} px-3">
                                            {{ $item->status == 1 ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No coupons found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery and DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            "order": [[0, "asc"]],
            "pageLength": 10
        });
    });
</script>

@endsection