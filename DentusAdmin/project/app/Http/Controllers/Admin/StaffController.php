<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Role;

use Illuminate\Support\Facades\Auth;
use DB;
use Hash;
use Illuminate\Support\Arr;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Admin::orderBy('id', 'DESC')->paginate(5);
        return view('admin.staffnew.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::where('status', 1)->where('id', $request->role_id)->first();
            $permissions = $roles->permissions;

            return $permissions;
        }
        $roles = Role::where('status', 1)->get();
        return view('admin.staffnew.create', ['roles' => $roles]);
    }

    public function store(Request $request)
    {


        // print_r($request->phone); die;
        $this->validate($request, [
            'name'        => 'required',
            'email'       => 'required|email|unique:admins,email',
            'password'    => 'required',
            'phone'       => 'required|digits:10|unique:admins,phone',
            'permissions' => 'required|array|min:1', // At least one permission must be selected
        ], [
            'permissions.required' => 'Please select at least one permission.',
            'permissions.min' => 'Please select at least one permission.',
        ]);
        // print_r($request->phone); die;
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = Admin::create($input);
        if ($request->role != null) {
            $user->roles()->attach($request->role);
            $user->save();
        }

        if ($request->permissions != null) {
            foreach ($request->permissions as $permission) {
                $user->permissions()->attach($permission);
                $user->save();
            }
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'User created successfully');
    }

    public function show($id)
    {
        $user = Admin::find($id);
        return view('admin.staffnew.show', compact('user'));
    }

    public function adminedit($id)
    {
        $user = Admin::find($id);
        // dd("ddd");
        return view('admin.staffnew.adminedit', compact('user'));
    }




    public function adminupdate(Request $request, $id)
    {
        // print_r("Ddd"); die;
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:admins,email,' . $id,
            'password' => 'same:confirm-password',
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        // if (isset($input['image']))
        // {
        //     // print_r("ddd"); die;
        //     $fileNameWithTheExtension = $input['image']->getClientOriginalName();
        //     $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
        //     $extension = $input['image']->getClientOriginalExtension();
        //     $display_image = 'image_'.$fileName . '_' . time() . '.' . $extension;
        //     $filePath = $input['image']->move(public_path('adminassets/images'), $display_image);
        //     $input['image'] = $display_image;
        // }

        $user = Admin::find($id);
        $user->update($input);


        return redirect()->route('admin.home')
            ->with('success', 'User updated successfully');
    }


    public function edit(Admin $admin, Request $request)
    {
        // print_r("sddd"); die;
        if ($request->ajax()) {
            $roles = Role::where('id', $request->role_id)->first();
            $permissions = $roles->permissions;

            return $permissions;
        }
        $user = $admin;
        $roles = Role::get();
        $userRole = $user->roles->first();
        if ($userRole != null) {
            $rolePermissions = $userRole->allRolePermissions;
        } else {
            $rolePermissions = null;
        }
        $userPermissions = $user->permissions;
        return view('admin.staffnew.edit', [
            'user' => $user,
            'roles' => $roles,
            'userRole' => $userRole,
            'rolePermissions' => $rolePermissions,
            'userPermissions' => $userPermissions
        ]);
    }

    public function listrequest(Request $request)
    {
        $roles = Role::where('id', $request->role_id)->first();
        $permissions = $roles->permissions;

        echo json_encode($permissions);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:admins,email,' . $id,
            // 'password' => 'same:confirm-password',
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $user = Admin::find($id);
        $user->update($input);

        $user->roles()->detach();
        $user->permissions()->detach();

        if ($request->role != null) {
            $user->roles()->attach($request->role);
            $user->save();
        }

        if ($request->permissions != null) {
            foreach ($request->permissions as $permission) {
                $user->permissions()->attach($permission);
                $user->save();
            }
        }

        return redirect()->route('admin.stafflist')
            ->with('success', 'User updated successfully');
    }


    public function destroy_staff(Request $request)
    {
        $input = $request->all();
        $a = Admin::find($input['id']);
        $a->roles()->detach();
        $a->permissions()->detach();
        $a->delete();
        return redirect()->route('admin.stafflist')
            ->with('success', 'User deleted successfully');
    }



    // public function destroy(Admin $admin)
    // {
    //     $admin->roles()->detach();
    //     $admin->permissions()->detach();
    //     $admin->delete();
    //     return redirect()->route('admin.stafflist')
    //         ->with('success', 'User deleted successfully');
    // }
}
