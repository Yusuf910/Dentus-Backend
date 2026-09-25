<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('id', 'DESC')->paginate(10);
        return view('admin.rolenew.index', compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::all();
        return view('admin.rolenew.create', ['permission' => $permission]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|max:255',
            'role_slug' => 'required|max:255',
            'roles_permissions' => 'required|array|min:1',
        ], [
            'role_name.required' => 'Role name is required.',
            'role_name.max' => 'Role name exceeds allowed length.',
            'role_slug.required' => 'Role slug is required.',
            'role_slug.max' => 'Role slug exceeds allowed length.',
            'roles_permissions.required' => 'At least one permission must be selected.',
            'roles_permissions.min' => 'At least one permission must be selected.',
        ]);

        $role = new Role();

        $role->name = $request->role_name;
        $role->slug = $request->role_slug;
        $role->guard_name = 'admin';
        $role->status = $request->status;

        $role->save();
        foreach ($request->roles_permissions as $permissionid) {
            $role->permissions()->attach($permissionid);
            $role->save();
        }
        $message = 'Role created successfully!';
        if (count($request->roles_permissions) > 5) {
            $message .= ' Role created with full access; confirmation shown.';
        }

        return redirect()->route('admin.roles.index')
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return view('admin.rolenew.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $permission = Permission::all();
        return view('admin.rolenew.edit', ['role' => $role, 'permission' => $permission]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'role_name' => 'required|max:255',
            'role_slug' => 'required|max:255',
            'roles_permissions' => 'required|array|min:1',
        ], [
            'role_name.required' => 'Role name is required.',
            'role_name.max' => 'Role name exceeds allowed length.',
            'role_slug.required' => 'Role slug is required.',
            'role_slug.max' => 'Role slug exceeds allowed length.',
            'roles_permissions.required' => 'At least one permission must be selected.',
            'roles_permissions.min' => 'At least one permission must be selected.',
        ]);


        $role->name = $request->role_name;
        $role->slug = $request->role_slug;
        $role->status = $request->status;
        $role->save();

        // $role->permissions()->delete();
        $role->permissions()->detach();

        foreach ($request->roles_permissions as $permissionid) {
            $role->permissions()->attach($permissionid);
            $role->save();
        }
        $message = 'Role updated successfully!';
        if (count($request->roles_permissions) > 5) {
            $message .= ' Role updated with full access; confirmation shown.';
        }
        // $role->syncPermissions($request->input('permission'));
        return redirect()->route('admin.roles.index')
            ->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Role $role)
    // {
    //     // $role->permissions()->delete();
    //     $role->delete();
    //     $role->permissions()->detach();
    //     return redirect()->route('admin.roles.index')
    //         ->with('success', 'Role deleted successfully !');
    // }

    public function destroy_role(Request $request)
    {
        $input = $request->all();
        $a = Role::find($input['id']);
        $a->delete();
        $a->permissions()->detach();
        // $a->status = 2;
        // $a->save();
        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully !');
    }
}
