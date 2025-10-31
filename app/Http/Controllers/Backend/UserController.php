<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Pagination\Paginator;

class UserController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.users.index";
    public $create_route = "admin.users.create";
    public $store_route = "admin.users.store";
    public $edit_route = "admin.users.edit";
    public $update_route = "admin.users.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Users",
            'sub_title' => "",
            'plural_name' => "users",
            'singular_name' => "User",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/users'),

        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('users.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = User::orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.users.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('users.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.users.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        return $request;
        $this->checkOwnPermission('users.create');
        $rules = [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ];
        $request->validate($rules);
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);

            if ($user->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> User Created Successfully');
            } else {
                return RedirectHelper::backWithInput();
            }
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->checkOwnPermission('users.edit');
        $data['pageHeader'] = $this->pageHeader;
        $data['edited'] = User::find($id);
        return view('backend.pages.users.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('users.edit');

        if ($user = User::find($id)) {
            $request->validate([
                'name' => 'required|max:50',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|min:8|confirmed',
            ]);
            try {
                $user->name = $request->name;
                $user->email = $request->email;
                if ($request->password != null) {
                    $user->password = Hash::make($request->password);
                }
                if ($user->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> User Created Successfully');

                } else {
                    return RedirectHelper::backWithInput();
                }
            } catch (QueryException $e) {
                return RedirectHelper::backWithInputFromException();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function destroy($id)
    {
        $this->checkOwnPermission('users.delete');
        $deleteData = User::find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }
}
