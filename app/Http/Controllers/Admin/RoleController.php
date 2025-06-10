<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:admin.permissions.manage', ['only' => ['index', 'assignPermissions']]);
    }

    public function index()
    {
        $admins = Admin::with('roles')
            ->where('id', '!=', auth('admin')->id())
            ->paginate(10);
        $roles = Role::all();
        $permissions = Permission::all();
        return view('admin.roles.index', compact('admins', 'roles', 'permissions'));
    }

    public function assignRole(Request $request, Admin $admin)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $admin->syncRoles($request->roles);
        return redirect()->route('admin.roles.index')->with('success', 'Roles assigned successfully.');
    }

    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
        ]);

        Role::create(['name' => $request->name, 'guard_name' => 'admin']);
        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function assignPermissions(Request $request, Role $role)
    {
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot modify permissions for Admin role.');
        }

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->syncPermissions($request->permissions ?? []);
        return redirect()->route('admin.roles.index')->with('success', 'Permissions assigned successfully.');
    }
}