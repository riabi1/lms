@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">All SubCategory</li>
        </ol>
      </nav>
    </div>
    <div class="ms-auto">
      <div class="btn-group">
        <a href="{{ route('admin.add.subcategory') }}" class="btn btn-primary px-5">Add SubCategory</a>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table id="example" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr>
              <th>Sl</th>
              <th>Category Name</th>
              <th>SubCategory Name</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($subcategories as $key => $item)
            <tr>
              <td>{{ $key + 1 }}</td>
              <td>{{ $item->category->category_name }}</td>
              <td>{{ $item->subcategory_name }}</td>
              <td>
                <a href="{{ route('admin.edit.subcategory', $item->id) }}" class="btn btn-info px-5">Edit</a>
                <form action="{{ route('admin.delete.subcategory', $item->id) }}" method="POST" style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger px-5" onclick="return confirm('Are you sure you want to delete this subcategory?')">Delete</button>
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

@endsection