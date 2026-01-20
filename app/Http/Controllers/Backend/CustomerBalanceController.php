<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\CustomerBalance;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class CustomerBalanceController extends Controller
{
    public $pageHeader;
    public $index_route = 'admin.customer_balances.index';
    public $create_route = 'admin.customer_balances.create';
    public $store_route = 'admin.customer_balances.store';
    public $edit_route = 'admin.customer_balances.edit';
    public $update_route = 'admin.customer_balances.update';

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => 'Customer Balance',
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/customer-balances'),
        ];
    }

    public function index()
    {
        $this->checkOwnPermission('customer_balances.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = CustomerBalance::with('user')
            ->where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('backend.pages.customer_balances.index', $data);
    }

    public function create()
    {
        $this->checkOwnPermission('customer_balances.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['users'] = User::orderBy('name')->get();

        return view('backend.pages.customer_balances.create', $data);
    }

    public function store(Request $request)
    {
        $this->checkOwnPermission('customer_balances.create');

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'balance' => 'required|numeric',
        ]);

        $branchId = auth()->user()->branch_id;

        try {
            $exists = CustomerBalance::where('user_id', $request->user_id)
                ->where('branch_id', $branchId)
                ->exists();

            if ($exists) {
                return RedirectHelper::backWithInput()->withErrors(['user_id' => 'Balance already exists for this patient in this branch.']);
            }

            $row = new CustomerBalance();
            $row->user_id = $request->user_id;
            $row->branch_id = $branchId;
            $row->balance = $request->balance;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, 'Customer balance created successfully.');
            }

            return RedirectHelper::backWithInput();
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }

    public function edit($id)
    {
        $this->checkOwnPermission('customer_balances.edit');
        $data['pageHeader'] = $this->pageHeader;

        if ($data['edited'] = CustomerBalance::with('user')
            ->where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
            return view('backend.pages.customer_balances.edit', $data);
        }

        return RedirectHelper::routeError($this->index_route, 'Customer balance not found.');
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('customer_balances.edit');

        $request->validate([
            'balance' => 'required|numeric',
        ]);

        try {
            if ($row = CustomerBalance::where('branch_id', auth()->user()->branch_id)->find($id)) {
                $row->balance = $request->balance;

                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, 'Customer balance updated successfully.');
                }

                return RedirectHelper::backWithInput();
            }

            return RedirectHelper::routeError($this->index_route, 'Customer balance not found.');
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('customer_balances.delete');

        $deleteData = CustomerBalance::where('branch_id', auth()->user()->branch_id)->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            }

            return response()->json(['status' => 422]);
        }
    }
}
