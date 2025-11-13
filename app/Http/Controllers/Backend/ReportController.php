<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Cost;
use App\Models\Invoice;
use App\Models\InvoiceList;
use App\Models\InvoicePayment;
use App\Models\Recept;
use App\Models\ReceptPayment;
use App\Models\Reefer;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class ReportController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.reports.index";
    public $create_route = "admin.reports.create";
    public $store_route = "admin.reports.store";
    public $edit_route = "admin.reports.edit";
    public $update_route = "admin.reports.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Costs",
            'sub_title' => "",
            'plural_name' => "reports",
            'singular_name' => "Cost",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/reports'),

        ];
    }

    public function collections(Request $request)
    {
        $this->checkOwnPermission('reports.index');

        $data['pageHeader'] = [
            'title' => "Collections",
            'sub_title' => "",
            'plural_name' => "collections",
            'singular_name' => "Collection",
            'base_url' => url('admin/collections'),
        ];

        $nowDhaka = Carbon::now('Asia/Dhaka');

        // Query payments linked to invoices of the same branch
        $query = InvoicePayment::with(['invoice.invoiceList'])
            ->whereHas('invoice', function ($q) {
                $q->where('branch_id', auth()->user()->branch_id);
            });

        // Query invoices for accurate total calculations
        $invQuery = Invoice::where('branch_id', auth()->user()->branch_id);

        // Apply date filters
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $query->whereDate('creation_date', $nowDhaka->toDateString());
            $invQuery->whereDate('creation_date', $nowDhaka->toDateString());
        } else {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('creation_date', [$request->start_date, $request->end_date]);
                $invQuery->whereBetween('creation_date', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('start_date')) {
                $query->where('creation_date', '>=', $request->start_date);
                $invQuery->where('creation_date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->where('creation_date', '<=', $request->end_date);
                $invQuery->where('creation_date', '<=', $request->end_date);
            }
        }

        // Get unique invoices to prevent duplicate amount calculations
        $uniqueInvoices = $invQuery->get()->keyBy('id');

        // Sum total amounts and discounts once per invoice
        $overallTotalAmount = $uniqueInvoices->sum('total_amount');
        $overallTotalDiscount = $uniqueInvoices->sum('discount_amount');

        // Sum payments (since each payment entry is unique)
        $overallTotalCollection = $query->sum('paid_amount');

        // Fetch payment data
        $dataPaginator = $query->orderBy('id', 'asc');

        // If exporting as PDF, fetch all data
        if ($request->query('export') == 'pdf') {
            $dataPaginator = $dataPaginator->get();
        } else {
            $dataPaginator = $dataPaginator->paginate(2000);
        }

        // Group payments by DATE and INVOICE to avoid duplicate invoice totals
        $groupedDatas = collect($dataPaginator instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $dataPaginator->items()
            : $dataPaginator
        )->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->creation_date)->format('Y-m-d'); // Group by date
        })->map(function ($dateGroup) {
            return $dateGroup->groupBy('invoice_id')->map(function ($group) {
                $firstInvoice = $group->first()->invoice; // Get invoice data only once

                return [
                    'data' => $group,
                    'total_collection' => $group->sum('paid_amount'), // Sum of payments per invoice
                    'total_amount' => $firstInvoice->total_amount ?? 0, // Total invoice amount (once)
                    'total_discount' => $firstInvoice->discount_amount ?? 0, // Total discount (once)
                ];
            });
        });

        // If not exporting as PDF, set paginated results
        if ($request->query('export') != 'pdf') {
            $dataPaginator->setCollection(collect($groupedDatas));
        }

        // Assign data to view
        $data['datas'] = $groupedDatas ?? collect();
        $data['overall_total_collection'] = $overallTotalCollection;
        $data['overall_total_amount'] = $overallTotalAmount;
        $data['overall_total_discount'] = $overallTotalDiscount;
//return $data;
        // Return the correct view
        if ($request->query('export') == 'pdf') {
            return view('backend.pages.reports.collections-pdf', $data);
        }
        return view('backend.pages.reports.collections', $data);
    }

    public function hospitalCollections(Request $request)
    {
        $this->checkOwnPermission('reports.index');

        $data['pageHeader'] = [
            'title' => "Hospital Collections",
            'sub_title' => "",
            'plural_name' => "hospital_collections",
            'singular_name' => "Hospital Collection",
            'base_url' => url('admin/hospital_collections'),
        ];

        $nowDhaka = Carbon::now('Asia/Dhaka');

        // Query payments linked to invoices of the same branch
        $query = ReceptPayment::with(['recept.receptList'])
            ->whereHas('recept', function ($q) {
                $q->where('branch_id', auth()->user()->branch_id);
            });

        // Query invoices for accurate total calculations
        $invQuery = Recept::where('branch_id', auth()->user()->branch_id);

        // Apply date filters
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $query->whereDate('creation_date', $nowDhaka->toDateString());
            $invQuery->whereDate('created_date', $nowDhaka->toDateString());
        } else {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('creation_date', [$request->start_date, $request->end_date]);
                $invQuery->whereBetween('created_date', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('start_date')) {
                $query->where('creation_date', '>=', $request->start_date);
                $invQuery->where('created_date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->where('creation_date', '<=', $request->end_date);
                $invQuery->where('created_date', '<=', $request->end_date);
            }
        }

        // Get unique invoices to prevent duplicate amount calculations
        $uniqueInvoices = $invQuery->get()->keyBy('id');

        // Sum total amounts and discounts once per invoice
        $overallTotalAmount = $uniqueInvoices->sum('total_amount');
        $overallTotalDiscount = $uniqueInvoices->sum('discount_amount');

        // Sum payments (since each payment entry is unique)
        $overallTotalCollection = $query->sum('paid_amount');

        // Fetch payment data
        $dataPaginator = $query->orderBy('id', 'asc');

        // If exporting as PDF, fetch all data
        if ($request->query('export') == 'pdf') {
            $dataPaginator = $dataPaginator->get();
        } else {
            $dataPaginator = $dataPaginator->paginate(2000);
        }

        // Group payments by DATE and INVOICE to avoid duplicate invoice totals
        $groupedDatas = collect($dataPaginator instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $dataPaginator->items()
            : $dataPaginator
        )->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->creation_date)->format('Y-m-d'); // Group by date
        })->map(function ($dateGroup) {
            return $dateGroup->groupBy('recept_id')->map(function ($group) {
                $firstInvoice = $group->first()->recept; // Get invoice data only once

                return [
                    'data' => $group,
                    'total_collection' => $group->sum('paid_amount'), // Sum of payments per invoice
                    'total_amount' => $firstInvoice->total_amount ?? 0, // Total invoice amount (once)
                    'total_discount' => $firstInvoice->discount_amount ?? 0, // Total discount (once)
                ];
            });
        });

        // If not exporting as PDF, set paginated results
        if ($request->query('export') != 'pdf') {
            $dataPaginator->setCollection(collect($groupedDatas));
        }

        // Assign data to view
        $data['datas'] = $groupedDatas ?? collect();
        $data['overall_total_collection'] = $overallTotalCollection;
        $data['overall_total_amount'] = $overallTotalAmount;
        $data['overall_total_discount'] = $overallTotalDiscount;
//return $data;
        // Return the correct view
        if ($request->query('export') == 'pdf') {
            return view('backend.pages.reports.recept-collections-pdf', $data);
        }
        return view('backend.pages.reports.recept-collections', $data);
    }
    public function references(Request $request)
    {
        $this->checkOwnPermission('reports.index');
        $data['pageHeader'] = [
            'title' => "References",
            'sub_title' => "",
            'plural_name' => "references",
            'singular_name' => "Reference",
            'base_url' => url('admin/references'),

        ];
        $data['startDate'] = $request->start_date ?? date('Y-m-d');
        $data['endDate'] = $request->end_date ?? date('Y-m-d');
        $nowDhaka = Carbon::now('Asia/Dhaka');
        $data['reffers'] = Reefer::where('branch_id', auth()->user()->branch_id)
            ->get();

        $query = Invoice::withSum('paidAmount', 'paid_amount')->with('reeferDr', 'reeferBy', 'costs')->where('branch_id', auth()->user()->branch_id);
        if ($request->filled('refer_id')) {
            $query->where('refer_id', $request->refer_id);

        }

        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $query->whereDate('creation_date', $nowDhaka->toDateString());
        } else {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('creation_date', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('start_date')) {
                $query->where('creation_date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->where('creation_date', '<=', $request->end_date);
            }
        }

        $allInvoices = (clone $query)->get();
        $data['totalPaidAmount'] = $allInvoices->sum(function ($invoice) {
            return $invoice->costs->sum('amount');
        });
        $data['totalAmount'] = (clone $query)->sum('refer_fee_total');
        $data['totalDueAmount'] = $data['totalAmount']-$data['totalPaidAmount'];


        if ($request->query('export') == 'pdf') {
            $data['datas'] = $query->get();
            return Pdf::loadView('backend.pages.reports.references-pdf', $data)->stream('reports.pdf');
        } else {
            $data['datas'] = $query->paginate(30);
        }

        return view('backend.pages.reports.references', $data);
    }

    public function referencesPayment(Request $request)
    {
        $this->checkOwnPermission('reports.index');
        $data['pageHeader'] = [
            'title' => "References",
            'sub_title' => "",
            'plural_name' => "references",
            'singular_name' => "Reference",
            'base_url' => url('admin/references'),

        ];
        $data['startDate'] = $request->start_date ?? date('Y-m-d');
        $data['endDate'] = $request->end_date ?? date('Y-m-d');
        $nowDhaka = Carbon::now('Asia/Dhaka');
        $data['reffers'] = Reefer::where('branch_id', auth()->user()->branch_id)
            ->get();

        $query = Invoice::withSum('paidAmount', 'paid_amount')->with('reeferDr', 'reeferBy', 'costs')->where('branch_id', auth()->user()->branch_id);
        if ($request->filled('refer_id')) {
            $query->where('refer_id', $request->refer_id);

        }

        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $query->whereDate('creation_date', $nowDhaka->toDateString());
        } else {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('creation_date', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('start_date')) {
                $query->where('creation_date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->where('creation_date', '<=', $request->end_date);
            }
        }

        $allInvoices = (clone $query)->get();
        $data['totalPaidAmount'] = $allInvoices->sum(function ($invoice) {
            return $invoice->costs->sum('amount');
        });
        $data['totalAmount'] = (clone $query)->sum('refer_fee_total');
        $data['totalDueAmount'] = $data['totalAmount']-$data['totalPaidAmount'];


        if ($request->query('export') == 'pdf') {
            $data['datas'] = $query->get();
            return Pdf::loadView('backend.pages.reports.references-pdf', $data)->stream('reports.pdf');
        } else {
            $data['datas'] = $query->get();
        }

        return view('backend.pages.reports.references-payment', $data);
    }

    public function referencesDoctor(Request $request)
    {
        $this->checkOwnPermission('prescriptions.index');
        $data['pageHeader'] = [
            'title' => "References",
            'sub_title' => "",
            'plural_name' => "references",
            'singular_name' => "Reference",
            'base_url' => url('admin/references'),

        ];
        $referdoctor = Reefer::where('admin_id', auth()->id())->first();
        $nowDhaka = Carbon::now('Asia/Dhaka');

        $query = Invoice::with('reeferDr', 'reeferBy', 'costs')->where('branch_id', auth()->user()->branch_id);
        if ($request->filled('refer_id')) {
            $query->where('refer_id', $referdoctor->id);

        }

        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $query->whereDate('creation_date', $nowDhaka->toDateString());
        } else {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('creation_date', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('start_date')) {
                $query->where('creation_date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->where('creation_date', '<=', $request->end_date);
            }
        }

        if ($request->query('export') == 'pdf') {
            $data['datas'] = $query->get();
            return Pdf::loadView('backend.pages.reports.references-doctor-pdf', $data)->stream('reports.pdf');
        } else {
            $data['datas'] = $query->paginate(20);
        }

        return view('backend.pages.reports.references-doctor', $data);
    }

    public function balance(Request $request)
    {
        $this->checkOwnPermission('reports.index');
        $data['pageHeader'] = [
            'title' => "Balance",
            'sub_title' => "",
            'plural_name' => "balances",
            'singular_name' => "Category",
            'base_url' => url('admin/balances'),

        ];
        if ($request->query('pdf') == 'yes') {
            return view('backend.pages.reports.balance-pdf', $data);
        }
        return view('backend.pages.reports.balance', $data);
    }

    public function categories(Request $request)
    {
        $this->checkOwnPermission('reports.index');
        $data['pageHeader'] = [
            'title' => "Categories",
            'sub_title' => "",
            'plural_name' => "categories",
            'singular_name' => "Category",
            'base_url' => url('admin/categories'),

        ];
        $data['reffers'] = Reefer::where('branch_id', auth()->user()->branch_id)
            ->where('type',Reefer::$typeArray[0])->get();
        $data['categories'] = Category::where('branch_id', auth()->user()->branch_id)
            ->get(['id', 'name']);
        $nowDhaka = Carbon::now('Asia/Dhaka');

        $query = InvoiceList::with('product.category');

        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $query->whereDate('created_at', $nowDhaka->toDateString());
        } else {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } elseif ($request->filled('start_date')) {
                $query->where('created_at', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->where('created_at', '<=', $request->end_date);
            }
        }
        if ($request->filled('category_id')) {
            $query->whereHas('product.category', function ($q) use ($request) {
                $q->where('id', $request->category_id);
            });
        }
        if ($request->filled('dr_refer_id')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('dr_refer_id', $request->dr_refer_id);
            });

        }

        $invoiceLists = $query->where('branch_id', auth()->user()->branch_id)
            ->get();

        $data['datas'] = $invoiceLists->groupBy(function ($invoiceList) {
            return $invoiceList->product->category->name; // Group by category name (or use category_id)
        })->map(function ($groupedInvoices) {
            return [
                'invoices' => $groupedInvoices,
                'total_price' => $groupedInvoices->sum('price'),
                'discount_price' => $groupedInvoices->sum('discount_price'),
                'total_count' => $groupedInvoices->count()
            ];
        });
        if ($request->query('export') == 'pdf') {
            return Pdf::loadView('backend.pages.reports.categories-pdf', $data)->stream('reports.pdf');
        }
        return view('backend.pages.reports.categories', $data);
    }

    public function cost(Request $request)
    {
        $this->checkOwnPermission('reports.index');

        $data['pageHeader'] = [
            'title' => "Costs",
            'sub_title' => "",
            'plural_name' => "costs",
            'singular_name' => "Cost",
            'base_url' => url('admin/costs'),

        ];
        $nowDhaka = Carbon::now('Asia/Dhaka');
        $query = Cost::query();
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $query->whereDate('creation_date', $nowDhaka->toDateString());
        } else {
            // Filter by date range if provided
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('creation_date', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('start_date')) {
                $query->where('creation_date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->where('creation_date', '<=', $request->end_date);
            }
        }
        $costQuery = $query->where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'desc');

        $data['datas'] = $costQuery->paginate(10);

        $data['totalAmount'] = $query->where('branch_id', auth()->user()->branch_id)
            ->sum('amount');
        return view('backend.pages.reports.costs', $data);
    }

    public function costPdf(Request $request)
    {
        $this->checkOwnPermission('reports.index');

        $data['pageHeader'] = $this->pageHeader;
        $nowDhaka = Carbon::now('Asia/Dhaka');

        // Base query with eager loading
        $query = Cost::with('costCategory')
            ->where('branch_id', auth()->user()->branch_id);

        // Apply date filters
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $query->whereDate('creation_date', $nowDhaka->toDateString());
        } else {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('creation_date', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('start_date')) {
                $query->where('creation_date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->where('creation_date', '<=', $request->end_date);
            }
        }

        // Fetch data (Paginate only if not exporting)
        $costs = $query->get();

        // Group by Date → Then by Cost Category
        $groupedCosts = collect($costs instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $costs->items()
            : $costs
        )->groupBy(function ($cost) {
            return Carbon::parse($cost->creation_date)->format('Y-m-d'); // Group by Date
        })->map(function ($group) {
            $totalPerDate = $group->sum('amount'); // Calculate total per date
            $groupedByCategory = $group->groupBy('cost_category_id')->map(function ($categoryGroup) {
                return [
                    'category_name' => optional($categoryGroup->first()->costCategory)->name ?? 'PC',
                    'total_amount' => $categoryGroup->sum('amount'),
                    'data' => $categoryGroup,
                ];
            });

            return [
                'total_per_date' => $totalPerDate, // Add total for this date
                'categories' => $groupedByCategory
            ];
        });

        // Summary Data
        $data['datas'] = $groupedCosts ?? collect();
        $data['totalAmount'] = $query->sum('amount');

        return view('backend.pages.reports.cost-pdf', $data);

    }

    public function costCategoryPdf(Request $request)
    {
        $this->checkOwnPermission('reports.index');

        $data['pageHeader'] = $this->pageHeader;
        $nowDhaka = Carbon::now('Asia/Dhaka');

        // Base query with eager loading
        $query = Cost::with('costCategory')
            ->where('branch_id', auth()->user()->branch_id);

        // Apply date filters
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $query->whereDate('creation_date', $nowDhaka->toDateString());
        } else {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('creation_date', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('start_date')) {
                $query->where('creation_date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->where('creation_date', '<=', $request->end_date);
            }
        }

        // Fetch data (Paginate only if not exporting)
        $costs = $query->get();

        // Group by Cost Category
        $groupedCosts = collect($costs instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $costs->items()
            : $costs
        )->groupBy('cost_category_id')->map(function ($categoryGroup) {
            return [
                'category_name' => optional($categoryGroup->first()->costCategory)->name ?? 'PC',
                'total_amount' => $categoryGroup->sum('amount'),
                'data' => $categoryGroup,
            ];
        });

        // Summary Data
        $data['datas'] = $groupedCosts ?? collect();
        $data['totalAmount'] = $query->sum('amount');


        return view('backend.pages.reports.cost-category-pdf', $data);
    }

    public function costCategoryIdPdf(Request $request)
    {
        $this->checkOwnPermission('reports.index');

        $data['pageHeader'] = $this->pageHeader;
        $nowDhaka = Carbon::now('Asia/Dhaka');

// Base query with eager loading
        $query = Cost::with('costCategory')
            ->where('branch_id', auth()->user()->branch_id);

// Apply cost_category_id filter
        if ($request->filled('cost_category_id')) {
            $query->where('cost_category_id', $request->cost_category_id);
        } else {
            $query->whereNull('cost_category_id');
        }

// Apply date filters
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $query->whereDate('creation_date', $nowDhaka->toDateString());
        } else {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('creation_date', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('start_date')) {
                $query->where('creation_date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->where('creation_date', '<=', $request->end_date);
            }
        }

// Fetch data (Paginate only if not exporting)
        $costs = $query->get();


// Summary Data
        $data['datas'] = $costs;
        $data['totalAmount'] = $query->sum('amount');


        return view('backend.pages.reports.cost-category-pdf-id', $data);
    }

}
