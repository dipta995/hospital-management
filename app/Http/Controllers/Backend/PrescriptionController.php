<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceList;
use App\Models\Prescription;
use App\Models\Drug;
use App\Models\Product;
use App\Models\Reefer;
use App\Models\TestReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\Paginator;

class PrescriptionController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.prescriptions.index";
    public $create_route = "admin.prescriptions.create";
    public $store_route = "admin.prescriptions.store";
    public $edit_route = "admin.prescriptions.edit";
    public $update_route = "admin.prescriptions.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Prescription",
            'sub_title' => "",
            'plural_name' => "prescriptions",
            'singular_name' => "Prescription",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/prescriptions'),

        ];


    }


    public function index()
    {
        $pageHeader = [
            'title' => 'Prescriptions',
            'create_route' => 'admin.prescriptions.create',
            'create_route' => 'admin.prescriptions.create',
            'store_route' => 'admin.prescriptions.store',
            'update_route' => 'admin.prescriptions.update',
            'show_route' => 'admin.prescriptions.show', // Add this line
            'edit_route' => 'admin.prescriptions.edit', // Ensure this is also defined
            'delete_route' => 'admin.prescriptions.destroy', // Ensure this is also defined
        ];
        $doctorRefferId = (Reefer::where('admin_id', auth()->id())->first())->id;
        $prescriptions = Prescription::with('doctor')->latest()
            ->where('reefer_id', $doctorRefferId)
            ->paginate(10);

        return view('backend.pages.prescriptions.index', compact('prescriptions', 'pageHeader'));
    }

    public function create()
    {
        $this->checkOwnPermission('prescriptions.create');

        $data['pageHeader'] = $this->pageHeader;
        $data['doctorId'] = (Reefer::where('admin_id', auth()->id())->first())->id;
        $branchId = auth()->user()->branch_id;

        $data['products'] = Product::where('branch_id', $branchId) // Always filter by branch
        ->get(['name', 'price', 'id']);

        return view('backend.pages.prescriptions.create', $data);
    }

    public function store(Request $request)
    {

        $request->validate([
            'investigation' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'note' => 'nullable|string|max:190',
        ]);
//
//        try {
        $totalAmount = 0;
        foreach ($request->product_ids as $item) {
            $test = Product::find($item);
            $totalAmount += $test->price;
        }
        $discountOnlyTotal = ($totalAmount * $request->discount) / 100;
        $discountedAmount = ($totalAmount - ($totalAmount * $request->discount) / 100);
//            Invoice by doctor
        $row = new Invoice();
        $row->branch_id = auth()->user()->branch_id;
        $row->patient_no = self::getNextPatientNo();
        $row->dr_refer_id = $request->doctor;
        $row->refer_id = $request->doctor;
        $row->admin_id = auth()->id();
        $row->invoice_number = self::generateInvoiceNumber();
        $row->discount_by = auth()->user()->name;
        $row->total_amount = $discountedAmount;
        $row->discount_amount = $discountOnlyTotal;
        $row->refer_fee_total = self::refferAmount($request->doctor, ($discountedAmount + $discountOnlyTotal), $discountOnlyTotal);
        $row->refer_fee_total_agent = 0.00;
        $row->delivery_at = Carbon::now('Asia/Dhaka');
        $row->payment_type = 'Cash';
        $row->patient_name = $request->patient_name;
        $row->patient_age_year = $request->patient_age_year;
        $row->patient_gender = $request->patient_gender;
        $row->patient_blood_group = $request->patient_blood_group;
        $row->note = $request->note;
        $row->creation_date = Carbon::now('Asia/Dhaka')->format('Y-m-d');
        $row->save();

        foreach ($request->product_ids as $product) {
            $test = Product::find($product);
            InvoiceList::create([
                'branch_id' => auth()->user()->branch_id,
                'invoice_id' => $row->id,
//                        'admin_id' => auth()->id(),
                'product_id' => $test->id,
                'price' => $test->price,
                'refer_fee' => $test->refer_fee,
            ]);
        }
//            Invoice by doctor


        $prescription = Prescription::create([
            'branch_id' => auth()->user()->branch_id,
            'invoice_id' => $row->id,
            'reefer_id' => $request->doctor,
            'investigation' => $request->investigation,
            'diagnosis' => $request->diagnosis,
        ]);

        if ($request->drug_name) {
            foreach ($request->drug_name as $index => $name) {
                $drug = new Drug();
                $drug->prescription_id = $prescription->id;
                $drug->name = $request->drug_name[$index];
                $drug->rule = $request->drug_rule[$index];
                $drug->time = $request->drug_time[$index];
                $drug->note = $request->drug_note[$index];
                $drug->duration = $request->drug_duration[$index];

                $drug->save();
            }
        }

        return RedirectHelper::routeSuccess('admin.prescriptions.index', '<strong>Success!</strong> Prescription created successfully!');
//        } catch (\Exception $e) {
//            return RedirectHelper::backWithInputFromException($e);
//        }
    }

    public function show($id)
    {
        $this->checkOwnPermission('prescriptions.index');

        $data['pageHeader'] = $this->pageHeader;
        if ($data['prescription'] = Prescription::with('drugs', 'doctor', 'invoice')->find($id)) {
            $data['tests'] = InvoiceList::where('invoice_id', $data['prescription']->invoice_id)->get();
            return view('backend.pages.prescriptions.show', $data);
        } else {
            return RedirectHelper::backWithInputFromException('<strong>Sorry!!! </strong> No Data Found.');
        }

    }

    public function edit($id)
    {
        $this->checkOwnPermission('prescriptions.edit');

        $data['prescription'] = Prescription::with('drugs')->findOrFail($id);
        $data['reefers'] = Reefer::where('branch_id', auth()->user()->branch_id)
            ->where('type', Reefer::$typeArray[0])
            ->get(['id', 'name']);
        $data['pageHeader'] = $this->pageHeader;

        return view('backend.pages.prescriptions.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('prescriptions.edit');

        $request->validate([
            'reefer_id' => 'required|exists:reefers,id',
            'investigation' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'drug_name' => 'required|array',
            'drug_name.*' => 'required|string',
        ]);

        try {
            $prescription = Prescription::findOrFail($id);
            $prescription->reefer_id = $request->reefer_id;
            $prescription->investigation = $request->investigation;
            $prescription->diagnosis = $request->diagnosis;
            $prescription->save();

            Drug::where('prescription_id', $id)->delete();

            foreach ($request->drug_name as $index => $name) {
                $drug = new Drug();
                $drug->prescription_id = $id;
                $drug->name = $request->drug_name[$index];
                $drug->rule = $request->drug_rule[$index];
                $drug->time = $request->drug_time[$index];
                $drug->note = $request->drug_note[$index];
                $drug->duration = $request->drug_duration[$index];

                $drug->save();
            }

            return RedirectHelper::routeSuccess('admin.prescriptions.index', '<strong>Success!</strong> Prescription updated successfully!');
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('prescriptions.delete');

        try {
            $prescription = Prescription::findOrFail($id);
            $prescription->delete();

            return RedirectHelper::routeSuccess('admin.prescriptions.index', '<strong>Success!</strong> Prescription deleted successfully!');
        } catch (\Exception $e) {
            return RedirectHelper::backWithInputFromException($e);
        }

    }

    public function generateInvoiceNumber()
    {
        $latestInvoice = Invoice::where('branch_id', auth()->user()->branch_id)
            ->whereYear('creation_date', Carbon::now('Asia/Dhaka')->year)
            ->whereMonth('creation_date', Carbon::now('Asia/Dhaka')->month)
            ->latest('invoice_number')
            ->first();
//dd($latestInvoice);
        if ($latestInvoice) {
            $lastNumber = (int)substr($latestInvoice->invoice_number, 4);
            $invoiceNumber = 'INV-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $invoiceNumber = 'INV-0001';
        }
        return $invoiceNumber;
    }

    public function refferAmount($refferId, $totalAmount, $discount)
    {
        $ref = Reefer::find($refferId);
//        dd(round((($ref->percent*$totalAmount)/100)-$discount));
        return round((($ref->percent * $totalAmount) / 100) - $discount) > 0 ? round((($ref->percent * $totalAmount) / 100) - $discount) : 0;
    }

    public function getNextPatientNo()
    {
        $latestPatientNo = Invoice::where('branch_id',auth()->user()->branch_id)->max('patient_no');
        return $latestPatientNo ? $latestPatientNo + 1 : 1;
    }


}
