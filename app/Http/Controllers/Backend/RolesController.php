<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Pagination\Paginator;

class RolesController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.roles.index";
    public $create_route = "admin.roles.create";
    public $store_route = "admin.roles.store";
    public $edit_route = "admin.roles.edit";
    public $update_route = "admin.roles.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Roles",
            'sub_title' => "",
            'plural_name' => "roles",
            'singular_name' => "Role",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/roles'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('roles.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = Role::orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.roles.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('roles.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['permission_groups'] = Admin::getpermissionGroups();
        $data['permissions'] = Permission::all();
        return view('backend.pages.roles.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->checkOwnPermission('roles.create');
        $request->validate([
            'name' => 'required|max:100|unique:roles'
        ]);
        $role = Role::create(['name' => $request->name, 'guard_name' => 'admin']);
        $permissions = $request->permissions;
        if ($role) {
            if (!empty($permissions)) {
                foreach ($permissions as $permissionId) {
                    $permission = Permission::findById($permissionId, 'admin');
                    if ($permission) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
            return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Admin Created Successfully');

        } else {
            return RedirectHelper::backWithInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->checkOwnPermission('roles.edit');
        $data['pageHeader'] = $this->pageHeader;
        $data['edited'] = Role::findById($id, 'admin');
        $data['permission_groups'] = Admin::getpermissionGroups();
        $data['permissions'] = Permission::all();
        return view('backend.pages.roles.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('roles.edit');
        $request->validate([
            'name' => 'required|max:100'
        ], [
            'name.required' => 'Please Insert New Role Name'
        ]);
        $role = Role::findById($id, 'admin');
        $permissions = $request->permissions;

        if ($role) {
            // Update the role name
            $role->name = $request->name;
            $role->save();

            // Sync permissions only if they are provided and valid
            if (!empty($permissions)) {
                $validPermissions = Permission::whereIn('id', $permissions)->where('guard_name', 'admin')->pluck('id')->toArray();
                $role->syncPermissions($validPermissions);
            } else {
                // If no permissions are provided, sync an empty array
                $role->syncPermissions([]);
            }

            return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Admin Created Successfully');

        } else {
            return RedirectHelper::backWithInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->checkOwnPermission('roles.delete');
        $deleteData = Role::find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }
}
