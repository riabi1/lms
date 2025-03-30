@extends('admin.layout.Admin_layout')
@section('admin')

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Orders</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <!-- Possibilité d'ajouter des actions ici, ex. exporter ou filtrer -->
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="card">
        <div class="card-body">
            <!-- Messages Flash -->
            @if (session('message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table id="ordersTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Course Title</th>
                            <th>Instructor</th>
                            <th>Price</th>
                            <th>Payment Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->user->name ?? 'N/A' }}</td>
                                <td>{{ $order->course_title ?? 'N/A' }}</td>
                                <td>{{ $order->instructor->name ?? 'N/A' }}</td>
                                <td>${{ number_format($order->price ?? 0, 2) }}</td>
                                <td>
                                    <span class="badge {{ $order->payment_status === 'completed' ? 'bg-success' : ($order->payment_status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                        {{ ucfirst($order->payment_status ?? 'N/A') }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at ? $order->created_at->format('d M Y') : 'N/A' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary" title="View">
                                            <i class="lni lni-eye"></i>
                                        </a>
                                        <!-- Ajouter d'autres actions si nécessaire, ex. Edit ou Delete -->
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No orders found.</td>
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
        $('#ordersTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "emptyTable": "No orders available in table"
            },
            "order": [[6, 'desc']] // Tri par date décroissante par défaut
        });
    });
</script>

@endsection