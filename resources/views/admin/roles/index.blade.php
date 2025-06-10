@extends('admin.layout.Admin_layout')
@section('admin')
<div class="page-content p-4 bg-light">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4 border-bottom pb-2">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none"><i class="bx bx-home-alt"></i> Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Roles & Permissions</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4 border-0" id="roleTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">Profile</button>
        </li>
        @can('admin.roles.create')
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="create-role-tab" data-bs-toggle="tab" data-bs-target="#create-role" type="button" role="tab">Create Role</button>
            </li>
        @endcan
        @can('admin.permissions.manage')
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions" type="button" role="tab">Manage Permissions</button>
            </li>
        @endcan
        @can('admin.roles.assign')
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="admins-tab" data-bs-toggle="tab" data-bs-target="#admins" type="button" role="tab">Manage Admins</button>
            </li>
        @endcan
    </ul>

    <div class="tab-content" id="roleTabsContent">
        <!-- Profile Tab -->
        <div class="tab-pane fade show active" id="profile" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white rounded-top-3">
                    <h5 class="mb-0">Your Profile</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 col-sm-12 text-center mb-4 mb-md-0">
                            <img src="{{ auth('admin')->user()->photo ? asset('upload/admin_images/' . auth('admin')->user()->photo) : asset('upload/no_image.jpg') }}" alt="{{ auth('admin')->user()->name }}" class="img-fluid rounded-circle border" style="width: 120px; height: 120px; object-fit: cover;">
                            <h6 class="mt-3 fw-bold">{{ auth('admin')->user()->name }}</h6>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="mb-3"><strong>Email:</strong> {{ auth('admin')->user()->email }}</div>
                            <div class="mb-3"><strong>Phone:</strong> {{ auth('admin')->user()->phone ?: 'Not provided' }}</div>
                            <div class="mb-3"><strong>Roles:</strong> {{ auth('admin')->user()->roles->pluck('name')->implode(', ') ?: 'No roles assigned' }}</div>
                        </div>
                        @can('admin.roles.assign')
                            <div class="col-md-5 col-sm-12">
                                <form action="{{ route('admin.roles.assign', auth('admin')->user()->id) }}" method="POST">
                                    @csrf
                                    <label for="roles-current" class="form-label fw-bold">Assign Roles</label>
                                    <select name="roles[]" id="roles-current" multiple class="form-select mb-3" aria-describedby="rolesHelp">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}" {{ auth('admin')->user()->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm px-4">Update Roles</button>
                                </form>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Role Tab -->
        @can('admin.roles.create')
            <div class="tab-pane fade" id="create-role" role="tabpanel">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-info text-white rounded-top-3">
                        <h5 class="mb-0">Create New Role</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.roles.create') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="mb-3">
                                        <label for="roleName" class="form-label fw-bold">Role Name</label>
                                        <input type="text" class="form-control" id="roleName" name="name" placeholder="Enter role name" required>
                                        @error('name')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-sm px-4">Create Role</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan

        <!-- Permissions Tab -->
        @can('admin.permissions.manage')
            <div class="tab-pane fade" id="permissions" role="tabpanel">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-success text-white rounded-top-3">
                        <h5 class="mb-0">Manage Permissions</h5>
                    </div>
                    <div class="card-body p-4">
                        @if ($roles->count())
                            @php
                                $permissionGroups = [
                                    'Roles' => array_filter($permissions->toArray(), fn($p) => str_contains($p['name'], 'admin.roles')),
                                    'Categories' => array_filter($permissions->toArray(), fn($p) => str_contains($p['name'], 'admin.categories')),
                                    'Subcategories' => array_filter($permissions->toArray(), fn($p) => str_contains($p['name'], 'admin.subcategories')),
                                    'Courses' => array_filter($permissions->toArray(), fn($p) => str_contains($p['name'], 'admin.courses')),
                                    'Instructors' => array_filter($permissions->toArray(), fn($p) => str_contains($p['name'], 'admin.instructors')),
                                    'Reviews' => array_filter($permissions->toArray(), fn($p) => str_contains($p['name'], 'admin.pending.review') || str_contains($p['name'], 'admin.active.review') || str_contains($p['name'], 'admin.update.review.status')),
                                    'Orders' => array_filter($permissions->toArray(), fn($p) => str_contains($p['name'], 'admin.orders')),
                                    'Coupons' => array_filter($permissions->toArray(), fn($p) => str_contains($p['name'], 'admin.coupon')),
                                    'Users' => array_filter($permissions->toArray(), fn($p) => str_contains($p['name'], 'admin.users')),
                                    'Settings' => array_filter($permissions->toArray(), fn($p) => str_contains($p['name'], 'admin.site')),
                                    'Blog' => array_filter($permissions->toArray(), fn($p) => str_contains($p['name'], 'admin.blog')),
                                    'Comments' => array_filter($permissions->toArray(), fn($p) => str_contains($p['name'], 'admin.comments')),
                                    'Earnings' => array_filter($permissions->toArray(), fn($p) => str_contains($p['name'], 'admin.earnings')),
                                    'Reports' => array_filter($permissions->toArray(), fn($p) => str_contains($p['name'], 'admin.reports') || str_contains($p['name'], 'admin.report-categories')),
                                    'Excel' => array_filter($permissions->toArray(), fn($p) => str_contains($p['name'], 'admin.excel')),
                                ];
                            @endphp
                            @foreach ($roles as $role)
                                <div class="mb-4">
                                    <h5 class="fw-bold text-primary">{{ $role->name }}</h5>
                                    @if ($role->name !== 'admin')
                                        <form action="{{ route('admin.roles.permissions', $role->id) }}" method="POST">
                                            @csrf
                                            <div class="accordion" id="permAccordion-{{ $role->id }}">
                                                @foreach ($permissionGroups as $groupName => $groupPermissions)
                                                    @if (!empty($groupPermissions))
                                                        <div class="accordion-item border-0 mb-2">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button collapsed bg-light fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#perm-{{ $role->id }}-{{ $groupName }}">
                                                                    {{ $groupName }}
                                                                </button>
                                                            </h2>
                                                            <div id="perm-{{ $role->id }}-{{ $groupName }}" class="accordion-collapse collapse">
                                                                <div class="accordion-body">
                                                                    <div class="row">
                                                                        @foreach ($groupPermissions as $permission)
                                                                            <div class="col-md-4 col-sm-6 mb-2">
                                                                                <div class="form-check">
                                                                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $permission['name'] }}" id="perm-{{ $role->id }}-{{ $permission['id'] }}" {{ $role->hasPermissionTo($permission['name']) ? 'checked' : '' }}>
                                                                                    <label class="form-check-label" for="perm-{{ $role->id }}-{{ $permission['id'] }}">{{ $permission['name'] }}</label>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm px-4 mt-3">Update Permissions</button>
                                        </form>
                                    @else
                                        <div class="text-muted">All permissions assigned (Admin role)</div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info text-center">No roles found.</div>
                        @endif
                    </div>
                </div>
            </div>
        @endcan

        <!-- Admins Tab -->
        @can('admin.roles.assign')
            <div class="tab-pane fade" id="admins" role="tabpanel">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-secondary text-white rounded-top-3">
                        <h5 class="mb-0">Manage Admins</h5>
                    </div>
                    <div class="card-body p-4">
                        @if ($admins->count())
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" style="width: 5%">#</th>
                                            <th scope="col" style="width: 20%">Name</th>
                                            <th scope="col" style="width: 25%">Email</th>
                                            <th scope="col" style="width: 25%">Roles</th>
                                            <th scope="col" style="width: 25%">Assign Roles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($admins as $key => $admin)
                                            <tr>
                                                <td>{{ $admins->firstItem() + $key }}</td>
                                                <td>{{ $admin->name }}</td>
                                                <td>{{ $admin->email }}</td>
                                                <td>{{ $admin->roles->pluck('name')->implode(', ') ?: 'No roles assigned' }}</td>
                                                <td>
                                                    <form action="{{ route('admin.roles.assign', $admin->id) }}" method="POST">
                                                        @csrf
                                                        <select name="roles[]" multiple class="form-select mb-2" style="min-width: 150px;">
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role->name }}" {{ $admin->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="btn btn-primary btn-sm px-4">Update</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                {{ $admins->links('vendor.pagination.bootstrap-5') }}
                            </div>
                        @else
                            <div class="alert alert-info text-center">No other admins found.</div>
                        @endif
                    </div>
                </div>
            </div>
        @endcan
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for role selection
        $('.form-select').select2({
            placeholder: 'Select roles',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
@endsection