<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Lab;
use App\Models\Reefer;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Pagination\Paginator;

class AdminController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.admins.index";
    public $create_route = "admin.admins.create";
    public $store_route = "admin.admins.store";
    public $edit_route = "admin.admins.edit";
    public $update_route = "admin.admins.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Admins",
            'sub_title' => "",
            'plural_name' => "admins",
            'singular_name' => "Admin",
            'index_route' => route($this->index_route),
            'create_route' => route($this->create_route),
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/admins'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('admins.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = Admin::orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.admins.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('admins.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['roles'] = Role::all();
        $data['branches'] = Branch::orderBy('id', 'DESC')->get();
        $data['categories'] = Category::orderBy('id', 'DESC')->get();
        $data['reefers'] = Reefer::orderBy('id', 'DESC')->get();
        return view('backend.pages.admins.create', $data);
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
        $this->checkOwnPermission('admins.create');
        $rules = [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:admins',
            'username' => 'required|unique:admins',
            'password' => 'required|min:8|confirmed',
        ];
        $request->validate($rules);
        try {
            $user = new Admin();
            $user->branch_id = $request->branch_id;
            $user->name = $request->name;
            $user->username = $request->username.$request->branch_id;
            $user->email = $request->email;
            $user->language = $request->language;
            $user->password = Hash::make($request->password);

            if ($user->save()) {
                if ($request->roles) {
                    $user->assignRole($request->roles);
                }
                if ($request->category_ids){
                foreach ($request->category_ids as $key) {
                    // Check if the record already exists
                    $existingLab = Lab::where('branch_id', $user->branch_id)
                        ->where('admin_id', $user->id)
                        ->where('category_id', $key)
                        ->first();

                    // If the record does not exist, create a new entry
                    if (!$existingLab) {
                        $lab = new Lab();
                        $lab->branch_id = $user->branch_id;
                        $lab->admin_id = $user->id;
                        $lab->category_id = $key;
                        $lab->save();
                    }
                }
                }


                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Admin Created Successfully');

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
        $this->checkOwnPermission('admins.edit');
        $data['pageHeader'] = $this->pageHeader;
        if($data['edited'] = Admin::find($id)) {
        $data['roles'] = Role::all();
            $data['isLabCategory'] = Lab::where('branch_id', $data['edited']->branch_id)->where('admin_id', $data['edited']->id)->pluck('category_id')->toArray();
            $data['categories'] = Category::where('branch_id', $data['edited']->branch_id)->orderBy('id', 'DESC')->get();
            $data['branches'] = Branch::orderBy('id', 'DESC')->get();
            $data['reefers'] = Reefer::where('branch_id', $data['edited']->branch_id)->orderBy('id', 'DESC')->get();
            return view('backend.pages.admins.edit', $data);
        }else{
            return RedirectHelper::backWithInputFromException();
        }
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

        $this->checkOwnPermission('admins.edit');


            $request->validate([
                'name' => 'required|max:50',
                'email' => 'required|email|unique:admins,email,' . $id,
                'username' => 'required|unique:admins,username,' . $id,
                'password' => 'nullable|min:8|confirmed',
            ]);
            try {
                if ($user = Admin::find($id)){
                    $user->branch_id = $request->branch_id;
                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->language = $request->language;
                    if ($request->password != null) {
                        $user->password = Hash::make($request->password);
                    }
                    $user->roles()->detach();
                    if ($request->roles) {
                        $user->assignRole($request->roles);
                    }
                    if ($request->category_ids) {
                        $inputCategoryIds = $request->category_ids;

//                         Fetch all existing category IDs for this admin and branch
                        $existingCategoryIds = Lab::where('branch_id', $user->branch_id)
                            ->where('admin_id', $id)
                            ->pluck('category_id')
                            ->toArray();
                        $categoriesToDelete = array_diff($existingCategoryIds, $inputCategoryIds);
                        $categoriesToAdd = array_diff($inputCategoryIds, $existingCategoryIds);
                        if (!empty($categoriesToDelete)) {
                            Lab::where('branch_id', $user->branch_id)
                                ->where('admin_id', $id)
                                ->whereIn('category_id', $categoriesToDelete)
                                ->delete();
                        }
                        foreach ($categoriesToAdd as $categoryId) {
                            $lab = new Lab();
                            $lab->branch_id = $user->branch_id;
                            $lab->admin_id = $id;
                            $lab->category_id = $categoryId;
                            $lab->save();
                        }
                    }

                    if ($user->save()) {
                        if($ref = Reefer::find($request->reefer_id)){
                            $ref->admin_id = $id;
                            $ref->save();
                        }else{
                            $ref = Reefer::find($request->reefer_id);
                            $ref->admin_id = $id;
                            $ref->save();
                        }

                        return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Admin Created Successfully');

                    } else {
                        return RedirectHelper::backWithInput();
                    }
                }
            } catch (QueryException $e) {
                return $e;
                return RedirectHelper::backWithInputFromException();
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
        $this->checkOwnPermission('admins.delete');
        $deleteData = Admin::find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }
}
