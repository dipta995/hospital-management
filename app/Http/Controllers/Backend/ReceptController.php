<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Recept;
use App\Models\ReceptList;
use App\Models\ReceptPayment;
use App\Models\ServiceCategory;
use App\Models\User;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReceptController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.recepts.index";
    public $create_route = "admin.recepts.create";
    public $store_route = "admin.recepts.store";
    public $edit_route = "admin.recepts.edit";
    public $update_route = "admin.recepts.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Recepts",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/recepts'),
        ];
    }

    public function index()
    {
        $this->checkOwnPermission('recepts.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = Recept::where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.recepts.index', $data);
    }

    public function create()
    {
        $this->checkOwnPermission('recepts.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['user_data'] = User::find(request('for'));
        $data['service_categories'] = ServiceCategory::all();
        return view('backend.pages.recepts.create', $data);
    }

    public function store(Request $request)
    {
//        return $request;
        $this->checkOwnPermission('recepts.create');

        DB::beginTransaction(); // 🔹 Start transaction

        try {
            // 1️⃣ Create Recept
            $row = new Recept();
            $row->admin_id = auth()->id();
            $row->user_id = $request['customerDetails']['customer_id'];
            $row->branch_id = auth()->user()->branch_id;
            $row->total_amount = $request['paymentDetails']['total_amount'];
            $row->discount_amount = $request['paymentDetails']['discount_amount'];
            $row->created_date = Carbon::now('Asia/Dhaka')->format('Y-m-d');
            $row->discount_amount = $request['paymentDetails']['discount_amount'] ?? 0;
            $row->save();

            // Total products count (avoid undefined var)
            $totalProducts = count($request['services']) > 0 ? count($request['services']) : 1;

            // 2️⃣ Create ReceptList entries
            foreach ($request['services'] as $service) {
                ReceptList::create([
                    'branch_id'      => auth()->user()->branch_id,
                    'user_id'        => $request['customerDetails']['customer_id'],
                    'recept_id'      => $row->id,
                    'service_id'     => $service['service_id'],
                    'price'          => $service['price'],
                    'discount' => ($row->discount_amount / $totalProducts),
                    'amount' =>  $service['price']-($row->discount_amount / $totalProducts),
                ]);
            }

            // 3️⃣ Create Payment entry
            $duePaid = new ReceptPayment();
            $duePaid->paid_amount = $request['paymentDetails']['paid_amount'];
            $duePaid->recept_id = $row->id;
            $duePaid->branch_id = auth()->user()->branch_id;
            $duePaid->admin_id = auth()->id();
            $duePaid->creation_date = Carbon::now('Asia/Dhaka')->format('Y-m-d');
            $duePaid->save();

            // 🔹 Commit all only if every step was successful
            DB::commit();

            return response()->json(
                [
                    'recept_id' => $row->id,
                    'customer_name' => $row->user->name,
                ]
            );
        } catch (QueryException $e) {
            DB::rollBack(); // 🔹 Cancel everything on error
            return RedirectHelper::backWithInputFromException($e);
        } catch (\Exception $e) {
            DB::rollBack();
            return RedirectHelper::backWithInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $this->checkOwnPermission('recepts.edit');
        $data['pageHeader'] = $this->pageHeader;

        if ($data['edited'] = Recept::where('branch_id', auth()->user()->branch_id)->find($id)) {
            return view('backend.pages.recepts.edit', $data);
        } else {
            return RedirectHelper::routeError($this->index_route, 'Recept not found.');
        }
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('recepts.edit');
        $request->validate([
            'user_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'created_date' => 'required|date',
        ]);

        try {
            if ($row = Recept::where('branch_id', auth()->user()->branch_id)->find($id)) {
                $row->user_id = $request->user_id;
                $row->branch_id = auth()->user()->branch_id;
                $row->created_date = $request->created_date;

                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, 'Recept updated successfully.');
                } else {
                    return RedirectHelper::backWithInput();
                }
            } else {
                return RedirectHelper::routeError($this->index_route, 'Recept not found.');
            }
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('recepts.delete');
        $deleteData = Recept::where('branch_id', auth()->user()->branch_id)->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }

    public function receptPdfPreview ($id)
    {
        $data['recept'] = Recept::with('receptList')->where('branch_id', auth()->user()->branch_id)
            ->find($id);
        $qrCode = new QrCode($data['recept']->id);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $data['qrcode'] = base64_encode($result->getString());
// Set Dhaka timezone
        $nowDhaka = \Carbon\Carbon::now('Asia/Dhaka');

        // Compare with 2 PM
        if ($nowDhaka->lt($nowDhaka->copy()->setTime(14, 0))) {
            $data['deliverytime'] = $nowDhaka->copy()->setTime(16, 0)->format('jS F Y h a');
        } else {
            $data['deliverytime'] = $nowDhaka->copy()->addDay()->setTime(10, 0)->format('jS F Y h a');
        }

//        return Pdf::loadView('backend.pages.recepts.recept-pdf', $data)->stream($data['recept']->patient_name.'.pdf');
        return view('backend.pages.recepts.recept-regular', $data);
    }
}
