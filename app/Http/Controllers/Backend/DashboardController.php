<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Cost;
use App\Models\Earn;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\ReceptPayment;
use App\Models\Setting;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $nowDhaka = Carbon::now('Asia/Dhaka');
        $branchId = auth()->user()->branch_id;

        // Today's collection (invoice payments + recept payments + earns)
        $data['todaysInvoiceCollection'] = InvoicePayment::where('branch_id', $branchId)
            ->whereDate('creation_date', $nowDhaka->toDateString())
            ->sum('paid_amount');

        $data['todaysReceptCollection'] = ReceptPayment::where('branch_id', $branchId)
            ->whereDate('creation_date', $nowDhaka->toDateString())
            ->sum('paid_amount');

        $data['todaysEarnCollection'] = Earn::where('branch_id', $branchId)
            ->whereDate('date', $nowDhaka->toDateString())
            ->sum('amount');

        $data['todaysTotalCollection'] = $data['todaysInvoiceCollection']
            + $data['todaysReceptCollection']
            + $data['todaysEarnCollection'];

        // Today's credit/cost
        $data['todaysCost'] = Cost::where('branch_id', $branchId)
            ->whereDate('creation_date', $nowDhaka->toDateString())
            ->sum('amount');

// Last month's total
        $lastMonthStart = $nowDhaka->copy()->startOfMonth()->subMonth();
        $lastMonthEnd = $nowDhaka->copy()->subMonth()->endOfMonth();
        $data['lastMonthTotal'] = Invoice::where('branch_id', $branchId)
            ->whereBetween('creation_date', [$lastMonthStart, $lastMonthEnd]);
        $data['lastMonthCost'] = Cost::where('branch_id', $branchId)
            ->whereBetween('creation_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

// Last week's total
        $lastWeekStart = $nowDhaka->copy()->startOfWeek()->subWeek();
        $lastWeekEnd = $nowDhaka->copy()->startOfWeek()->subDay();
        $data['lastWeekTotal'] = Invoice::where('branch_id', $branchId)
            ->whereBetween('creation_date', [$lastWeekStart, $lastWeekEnd]);
        $data['lastWeekCost'] = Cost::where('branch_id', $branchId)
            ->whereBetween('creation_date', [$lastWeekStart, $lastWeekEnd])
            ->sum('amount');

// Last year's total
        $lastYearStart = $nowDhaka->copy()->startOfYear()->subYear();
        $lastYearEnd = $nowDhaka->copy()->endOfYear()->subYear();
        $data['lastYearTotal'] = Invoice::where('branch_id', $branchId)
            ->whereBetween('creation_date', [$lastYearStart, $lastYearEnd]);
        $data['lastYearCost'] = Cost::where('branch_id', $branchId)
            ->whereBetween('creation_date', [$lastYearStart, $lastYearEnd])
            ->sum('amount');


        return view('backend.pages.dashboards.index',$data);
    }
}
