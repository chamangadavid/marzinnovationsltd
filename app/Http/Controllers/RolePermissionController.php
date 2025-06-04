<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function rolesAndPermission()
    {
        return Inertia::render('MyMARZ/Admin/rolesAndPermission');
    }

    public function roles()
    {
        return response()->json([
            'roles' => Role::with('permissions')->get(),
            'permissions' => Permission::all()->pluck('name')
        ]);
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role->load('permissions')
        ], 201);
    }

    public function permissions()
    {
        return response()->json([
            'permissions' => Permission::all()
        ]);
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name'
        ]);

        $permission = Permission::create(['name' => $request->name]);

        return response()->json([
            'message' => 'Permission created successfully',
            'permission' => $permission
        ], 201);
    }

    public function users()
    {
        return response()->json([
            'users' => User::with('roles')->get(),
            'roles' => Role::all()->pluck('name')
        ]);
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->syncRoles($request->roles ?? []);

        return response()->json([
            'message' => 'Roles assigned successfully',
            'user' => $user->load('roles')
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$id,
            'permissions' => 'array',
        ]);
        
        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions']);
        
        return response()->json(['message' => 'Role updated successfully']);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Prevent deletion of protected roles
        if (in_array($role->name, ['Super Admin'])) {
            return response()->json(['message' => 'This role cannot be deleted'], 403);
        }
        
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:roles,id',
        ]);
        
        // Filter out protected roles
        $roles = Role::whereIn('id', $validated['ids'])
            ->whereNotIn('name', ['Super Admin'])
            ->get();
        
        $roles->each->delete();
        
        return response()->json([
            'message' => 'Roles deleted successfully',
            'count' => $roles->count()
        ]);
    }

}
