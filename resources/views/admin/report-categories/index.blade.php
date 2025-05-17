@extends('admin.layout.admin_layout')

@section('admin')
<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Report Categories</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('admin.report-categories.create') }}" class="btn btn-primary">Add New Category</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>All Report Categories</h4>
        </div>
        <div class="card-body">
            @if (session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger mb-3">{{ session('error') }}</div>
            @endif

            <table id="categoriesTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->slug }}</td>
                        <td>{{ $category->description ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('admin.report-categories.edit', $category) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('admin.report-categories.destroy', $category) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection