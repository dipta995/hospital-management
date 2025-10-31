<?php

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\SmsBalance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

function phoneNoRegex()
{
    return "/^(?=.{11}$)(01[3-9])\d+$/"; // [3-9] for BD phone no
}

function redirectRouteHelper($route = null, $message = null)
{
    if ($route == null) {
        Alert::toast($message == null ? 'Something went wrong Try again !' : $message, 'error')->timerProgressBar();
        return back();
    } else {
        Alert::toast($message == null ? 'Successfully Created !' : $message, 'success')->timerProgressBar();
        return redirect()->route($route);
    }
}

function redirectUpdateRoute($route = null, $message = null)
{
    if ($route == null) {
        Alert::toast($message == null ? 'Something went wrong Try again !' : $message, 'error')->timerProgressBar();
        return back();
    } else {
        Alert::toast($message == null ? 'Successfully Updated !' : $message, 'success')->timerProgressBar();
        return redirect()->route($route);
    }
}

function redirectRouteHelperWithParams($route = null, $params = null, $message = null)
{
    if ($route == null) {
        Alert::toast($message == null ? 'Something went wrong Try again !' : $message, 'error')->timerProgressBar();
        return back();
    } else {
        Alert::toast($message == null ? 'Successfully Stored' : $message, 'success')->timerProgressBar();
        return redirect()->route($route, $params);
    }
}

function imageUpload($file, $file_path = null)
{
    $file_name = $file_path . '/' . time() . '.' . $file->getClientOriginalExtension();
    $file->move(public_path('images/' . $file_path . '/'), $file_name);
    return $file_name;
}


function removeOldImage($file)
{
    unset($file);
}

function expairyAlertNotification()
{
    $text = "";
    $today = \Carbon\Carbon::today();
    $in7days = $today->copy()->addDays(7);

    $rows = \App\Models\PurchaseItem::where(function ($query) use ($today, $in7days) {
        // Expiring within next 7 days
        $query->whereBetween('expiry_date', [$today, $in7days])
            // OR quantity usage exceeds 90%
            ->orWhereRaw('quantity_spend / quantity >= 0.9');
    })
        ->get();

    foreach ($rows as $row) {
        $remainingQuantity = $row->quantity - $row->quantity_spend;
        $expiryDate = \Carbon\Carbon::parse($row->expiry_date)->format('d-m-Y');
//        $usedPercentage = round(($row->quantity_spend / $row->quantity) * 100, 2);
        $usedPercentage = $row->quantity > 0
            ? round(($row->quantity_spend / $row->quantity) * 100, 2)
            : 0;

        $text .= "<div style='background-color: #ffffff; border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); transition: background-color 0.3s ease;'>
                    <p style='font-size: 1rem; color: #333; margin: 5px 0;'><strong>Item:</strong> " . ($row->item->name ?? 'N/A') . "</p>
                    <p style='font-size: 1rem; color: #333; margin: 5px 0;'><strong>Remaining Quantity:</strong> " . $remainingQuantity . " (Used: {$usedPercentage}%)</p>
                    <p style='font-size: 1rem; color: #333; margin: 5px 0;'><strong>Expiry Date:</strong> <span style='color: #d9534f; font-weight: bold;'>" . $expiryDate . "</span></p>
                  </div>";
    }

    return $text;
}
function expairyAlertNotificationCount()
{
    $today = \Carbon\Carbon::today();
    $in7days = $today->copy()->addDays(7);

    $count = \App\Models\PurchaseItem::where(function ($query) use ($today, $in7days) {
        $query->whereBetween('expiry_date', [$today, $in7days])
            ->orWhereRaw('quantity_spend / quantity >= 0.9');
    })->count();

    return $count;
}

use App\Models\Earn;
use App\Models\Cost;

function currentBalanceMonth()
{
    $fromDate = request()->from_date;
    $toDate = request()->to_date;

    $branchId = auth()->user()->branch_id;

//    $query = InvoicePayment::with(['invoice.invoiceList'])
//        ->whereHas('invoice', function ($q) use ($branchId) {
//            $q->where('branch_id', $branchId);
//        });

    $query = InvoicePayment::with(['invoice.invoiceList'])
        ->whereHas('invoice', function ($q) {
            $q->where('branch_id', auth()->user()->branch_id);
        });
    $nowDhaka = Carbon::now('Asia/Dhaka');
//    if ($fromDate && $toDate) {
//        $query->whereBetween('creation_date', [
//            Carbon::parse($fromDate)->startOfDay(),
//            Carbon::parse($toDate)->endOfDay()
//        ]);
//    }
    if (\request()->filled('from_date') && \request()->filled('to_date')) {
        $query->whereBetween('creation_date', [\request()->from_date, \request()->to_date]);
    } elseif (\request()->filled('from_date')) {
        $query->where('creation_date', '>=', \request()->from_date);
    } elseif (\request()->filled('to_date')) {
        $query->where('creation_date', '<=', \request()->to_date);
    }

    else {
        $now = Carbon::now('Asia/Dhaka');
        $query->whereMonth('creation_date', $now->month)
            ->whereYear('creation_date', $now->year);
    }
    $totalCollection = $query->sum('paid_amount');

    // Earn
    $earn = Earn::where('branch_id', $branchId);
    if (\request()->filled('from_date') && \request()->filled('to_date')) {
        $earn->whereBetween('date', [\request()->from_date, \request()->to_date]);
    } elseif (\request()->filled('from_date')) {
        $earn->where('date', '>=', \request()->from_date);
    } elseif (\request()->filled('to_date')) {
        $earn->where('date', '<=', \request()->to_date);
    }
    else {
        $now = Carbon::now('Asia/Dhaka');
        $earn->whereMonth('date', $now->month)
            ->whereYear('date', $now->year);
    }
    $totalEarn = $earn->sum('amount');

    // Cost
    $cost = Cost::where('branch_id', $branchId);
    if (\request()->filled('from_date') && \request()->filled('to_date')) {
        $cost->whereBetween('creation_date', [\request()->from_date, \request()->to_date]);
    } elseif (\request()->filled('from_date')) {
        $cost->where('creation_date', '>=', \request()->from_date);
    } elseif (\request()->filled('to_date')) {
        $cost->where('creation_date', '<=', \request()->to_date);
    } else {
        $now = Carbon::now('Asia/Dhaka');
        $cost->whereMonth('creation_date', $now->month)
            ->whereYear('creation_date', $now->year);
    }
    $totalCost = $cost->sum('amount');

    // Current Balance
    $currentBalance = $totalCollection + $totalEarn - $totalCost;

    // Title
    $title = '📅 Financial Summary for ';
    if ($fromDate && $toDate) {
        $title .= Carbon::parse($fromDate)->format('d M Y') . ' to ' . Carbon::parse($toDate)->format('d M Y');
    } else {
        $now = Carbon::now('Asia/Dhaka');
        $title .= $now->format('F Y');
    }

    // Return HTML
    return '
    <div style="font-family: Arial, sans-serif; border: 1px solid #ccc; padding: 20px; border-radius: 10px; max-width: 600px; background: #f9f9f9;">
        <h5 style="border-bottom: 1px solid #ddd; padding-bottom: 5px;">' . $title . '</h5>
        <p><strong>Collection:</strong> <span style="float:right;">৳ ' . number_format($totalCollection, 2) . '</span></p>
        <p><strong>Earn:</strong> <span style="float:right;">৳ ' . number_format($totalEarn, 2) . '</span></p>
        <hr>
        <p><strong>Amount:</strong> <span style="float:right;">৳ ' . number_format($totalCollection + $totalEarn, 2) . '</span></p>
        <hr>
        <p><strong>Cost:</strong> <span style="float:right;">৳ ' . number_format($totalCost, 2) . '</span></p>
        <hr style="border-top: 2px solid #000;">
        <h5><strong>💰 Current Balance:</strong> <span style="float:right;">৳ ' . number_format($currentBalance, 2) . '</span></h5>
    </div>';
}



function smsSent($branchId, $phoneNo, $message)
{
    $smsBalance = SmsBalance::where('branch_id', $branchId)->first();

    if ($smsBalance && $smsBalance->balance > 0) {
        $url = "https://api.bdbulksms.net/api.php?json";

        $data = json_encode([
            'token' => '1218900244217455190829fffe80bd63d3ca92c711b70e2aff2d4',
            'smsdata' => [
                [
                    'to' => '+88' . $phoneNo,
                    'message' => $message,
                ]
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        $length = mb_strlen($message);

        if ($length <= 68) {
            $number = 1;
        } elseif ($length <= 140) {
            $number = 2;
        } elseif ($length <= 210) {
            $number = 3;
        } elseif ($length <= 280) {
            $number = 4;
        } else {
            // Optional: if more than 4 parts, you can round up or handle it
            $number = ceil($length / 70); // or limit to 4
            // $number = 4; // fixed at 4 max if needed
        }

        if (isset($result[0]['status']) && $result[0]['status'] === 'SENT') {
            // Decrement balance only if SMS is actually sent
            $smsBalance->balance = $smsBalance->balance - $number;
            $smsBalance->save();

            return 'SMS sent successfully.';
        } else {
            return 'SMS not sent.';
        }
    }

    return 'Insufficient balance.';
}

function reAgents($invoiceListId) {
    $a = \App\Models\ReagentTrack::where('invoice_list_id', $invoiceListId)->get();
    $text = "";

    foreach ($a as $data) {
        $name = $data->purchaseItem->item->name ?? "N/A";
        $text .= "<span class='badge bg-success p-1 rounded m-1 d-inline-block'>" . $name . "</span>
<a class='badge bg-danger' href='/admin/labs/reagent-delete/".$data->id."'><i class='fas fa-trash'></i></a>
";
    }

    return $text;
}


function formatPhoneNumber($phone)
{
    if (!$phone) return null;

    // Remove all non-digit characters
    $phone = preg_replace('/\D/', '', $phone);

    // Remove leading 880 or 88
    if (str_starts_with($phone, '880')) {
        $phone = substr($phone, 3);
    } elseif (str_starts_with($phone, '88')) {
        $phone = substr($phone, 2);
    }

    // Add leading 0 if not present
    if (!str_starts_with($phone, '0')) {
        $phone = '0' . $phone;
    }

    // Validate BD mobile number: 11 digits, starts with 01[3-9]
    if (!preg_match('/^01[3-9]\d{8}$/', $phone)) {
        return null; // Invalid format
    }

    return $phone;
}


