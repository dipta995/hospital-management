<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\NumberCategory;
use App\Models\PhoneNumber;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Imports\PhoneNumbersImport;
use Maatwebsite\Excel\Facades\Excel;
class PhoneNumberController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.phone_numbers.index";
    public $create_route = "admin.phone_numbers.create";
    public $store_route = "admin.phone_numbers.store";
    public $edit_route = "admin.phone_numbers.edit";
    public $update_route = "admin.phone_numbers.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Phone Numbers",
            'sub_title' => "",
            'plural_name' => "phone_numbers",
            'singular_name' => "PhoneNumber",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/number-categories'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('phone_numbers.index');
        $data['pageHeader'] = $this->pageHeader;
        $query = PhoneNumber::where('branch_id', auth()->user()->branch_id);

if (request()->filled('number_category_id')) {
    $query->where('number_category_id', request('number_category_id'));
}

$data['datas'] = $query->orderBy('id', 'DESC')->paginate(50);

        $data['numberCategories'] = NumberCategory::where('branch_id',auth()->user()->branch_id)->get();

        return view('backend.pages.phone_numbers.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('phone_numbers.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['numberCategories'] = NumberCategory::where('branch_id',auth()->user()->branch_id)->get();
        return view('backend.pages.phone_numbers.create', $data);
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
        $this->checkOwnPermission('phone_numbers.create');
        $rules = [
                'number_category_id' => 'required',
                'name' => 'nullable|string',
                'address' => 'nullable|string|max:200',
                'number' => 'required|string|size:11',
        ];
        $request->validate($rules);
        $exists = PhoneNumber::where('branch_id', auth()->user()->branch_id)
        ->where('number', $request->number)
        ->exists();

        if ($exists) {
            return back()->withErrors(['number' => 'This number number already exists.']);
        }
        try {

            $row = new PhoneNumber();
            $row->branch_id = auth()->user()->branch_id;
            $row->number_category_id = $request->number_category_id;
            $row->name = $request->name;
            $row->address = $request->address;
            $row->number = $request->number;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> PhoneNumber Created Successfully');

            } else {
                return RedirectHelper::backWithInput();
            }
        } catch (QueryException $e) {
            return $e;
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
        $this->checkOwnPermission('phone_numbers.edit');
        $data['pageHeader'] = $this->pageHeader;
        if ($data['edited'] = PhoneNumber::where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
            $data['numberCategories'] = NumberCategory::where('branch_id',auth()->user()->branch_id)->get();

            return view('backend.pages.phone_numbers.edit', $data);
        } else {
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
        $this->checkOwnPermission('phone_numbers.edit');
        $request->validate([
            'name' => 'required|max:200',
            'address' => 'nullable|string|max:200',

        ]);
        try {
            if ($row = PhoneNumber::where('branch_id', auth()->user()->branch_id)
                ->find($id)) {
                $row->number_category_id = $request->number_category_id;
                $row->name = $request->name;
                $row->number = $request->number;
                $row->address = $request->address;
                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> PhoneNumber Created Successfully');

                } else {
                    return RedirectHelper::backWithInput();
                }
            } else {
                return RedirectHelper::routeError($this->index_route, '<strong>Sorry !!!</strong>Data not found');

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
        $this->checkOwnPermission('phone_numbers.delete');
        $deleteData = PhoneNumber::where('branch_id', auth()->user()->branch_id)
            ->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }

    public function phoneNumberUpload(Request $request)
    {
        $request->validate([
            'number_category_id' => 'required|exists:number_categories,id',
            'numbers' => 'required|file|mimes:xlsx,csv,txt',
        ]);

        try {
            Excel::import(new PhoneNumbersImport($request->number_category_id), $request->file('numbers'));
            return back()->with('message', 'Phone numbers uploaded successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['numbers' => 'Import failed. Please check the file format.']);
        }
    }

    public function phoneNumberSend(Request $request)
    {
        if (!is_array($request->selected_ids) || empty($request->selected_ids)) {
            return response()->json(['error' => 'No selected_ids provided'], 400);
        }

        foreach ($request->selected_ids as $row) {
            if (isset($row) && !empty($row)) {
                smsSent(auth()->user()->branch_id,$row, $request->sms_message);
            }
        }

        return response()->json(['message' => 'SMS sent successfully.']);
    }



}
