@extends('User.layout.User_layout')

@section('title')
My Courses | Easy Learning
@endsection

@section('userdashboard')
<!-- Inclure jQuery et Toastr (facultatif pour le moment) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<div class="page-content">
    <!--breadcrumb-->
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
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-3">Your Purchased Courses</h4>
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
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
                            @if ($order->course) <!-- Vérifie que le cours existe -->
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
                                        // Vérifie si l'utilisateur a commencé le cours (logique temporaire)
                                        $progress = $order->progress ?? 0; // À remplacer par la vraie logique
                                    @endphp
                                    @if ($progress > 0)
                                        <a href="{{ url('course/learn/'.$order->course->id.'/'.Str::slug($order->course->course_name)) }}" class="btn btn-primary btn-sm">
                                            <i class="bx bx-play"></i> Continue Learning
                                        </a>
                                    @else
                                        <a href="{{ url('course/learn/'.$order->course->id.'/'.Str::slug($order->course->course_name)) }}" class="btn btn-success btn-sm">
                                            <i class="bx bx-play"></i> Start Course
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">You haven't purchased any courses yet. <a href="{{ route('course.list') }}" class="text-primary">Explore Courses</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script pour DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "lengthMenu": [5, 10, 25, 50],
            "pageLength": 10
        });
    });
</script>

@endsection