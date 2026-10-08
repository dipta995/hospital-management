<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceCancelRequest;
use App\Services\CancelRequestNotificationService;
use App\Services\InvoiceDeletionService;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class InvoiceCancelRequestController extends Controller
{
    public function __construct(private SecurityService $security)
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
    }

    public function index(Request $request)
    {
        $this->checkOwnPermission('invoices.index');
        $canApprove = $this->security->allows('security.cancel_approve');
        $tableReady = $this->security->tablesReady();

        $requests = collect();
        $pendingCount = 0;
        if ($tableReady) {
            $query = InvoiceCancelRequest::with(['requester', 'reviewer', 'invoice'])
                ->where('branch_id', auth()->user()->branch_id);
            if (!$canApprove) {
                $query->where('requested_by', auth()->id());
            }
            $pendingCount = (clone $query)->where('status', InvoiceCancelRequest::STATUS_PENDING)->count();
            $status = $request->input('status', InvoiceCancelRequest::STATUS_PENDING);
            if ($status !== 'all') {
                $query->where('status', $status);
            }
            $requests = $query->latest()->paginate(30)->withQueryString();
        }

        return view('backend.pages.security.cancel-requests', compact('requests', 'canApprove', 'tableReady', 'pendingCount'));
    }

    public function notifications(CancelRequestNotificationService $notifications)
    {
        $notes = $notifications->forAdmin();
        $latestPending = $notes['pending']->first();
        $latestReviewed = $notes['reviewed']->firstWhere('is_unseen', true);

        return response()->json([
            'count' => $notes['count'],
            'html' => view('backend.layouts.partials.cancel-notifications', ['cancelNotes' => $notes])->render(),
            'latest_pending' => $latestPending ? [
                'id' => $latestPending->id,
                'text' => ($latestPending->requester->name ?? 'Unknown') . ' invoice ' . $latestPending->invoice_number . ' cancel করতে চেয়েছে।',
            ] : null,
            'latest_reviewed' => $latestReviewed ? [
                'id' => $latestReviewed->id,
                'text' => 'আপনার invoice ' . $latestReviewed->invoice_number . ' cancel request '
                    . ($latestReviewed->status === InvoiceCancelRequest::STATUS_APPROVED ? 'approve' : 'reject') . ' হয়েছে।',
            ] : null,
        ]);
    }

    public function markNotificationsSeen(CancelRequestNotificationService $notifications)
    {
        $notifications->markSeen((int) auth()->id());

        return response()->json(['status' => 200]);
    }

    public function approve(Request $request, InvoiceCancelRequest $cancelRequest)
    {
        $this->authorizeReview($cancelRequest);
        $note = trim((string) $request->input('review_note', ''));

        $invoice = Invoice::where('branch_id', auth()->user()->branch_id)->find($cancelRequest->invoice_id);
        $reason = sprintf(
            'Cancel request #%d by %s: %s%s',
            $cancelRequest->id,
            $cancelRequest->requester->name ?? 'Unknown',
            $cancelRequest->reason,
            $note !== '' ? ' | Approved: ' . $note : ''
        );

        try {
            DB::transaction(function () use ($invoice, $reason, $cancelRequest, $note) {
                if ($invoice) {
                    app(InvoiceDeletionService::class)->delete($invoice, $reason);
                }
                $cancelRequest->update([
                    'status' => InvoiceCancelRequest::STATUS_APPROVED,
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                    'review_note' => $note !== '' ? $note : null,
                ]);
            });
        } catch (\Throwable $e) {
            report($e);
            return RedirectHelper::backWithInputFromException('<strong>Sorry!!!</strong> Invoice cancel করা যায়নি।');
        }

        return RedirectHelper::back('<strong>Approved!</strong> Invoice ' . e($cancelRequest->invoice_number) . ' বাতিল করা হয়েছে এবং Trash-এ সংরক্ষিত আছে।');
    }

    public function reject(Request $request, InvoiceCancelRequest $cancelRequest)
    {
        $this->authorizeReview($cancelRequest);
        $note = trim((string) $request->input('review_note', ''));

        $cancelRequest->update([
            'status' => InvoiceCancelRequest::STATUS_REJECTED,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $note !== '' ? $note : null,
        ]);

        return RedirectHelper::back('<strong>Rejected!</strong> Invoice ' . e($cancelRequest->invoice_number) . ' যেমন ছিল তেমনই আছে।');
    }

    private function authorizeReview(InvoiceCancelRequest $cancelRequest): void
    {
        if (!$this->security->allows('security.cancel_approve')) {
            abort(403, 'Cancel request review করার অনুমতি (security.cancel_approve) নেই।');
        }
        if ((int) $cancelRequest->branch_id !== (int) auth()->user()->branch_id) {
            abort(404);
        }
        if (!$cancelRequest->isPending()) {
            abort(409, 'This request has already been reviewed.');
        }
    }
}
