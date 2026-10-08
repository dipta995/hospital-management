<?php

namespace App\Services;

use App\Models\InvoiceCancelRequest;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CancelRequestNotificationService
{
    private const REVIEWED_DAYS = 3;

    private const LIST_LIMIT = 10;

    public function __construct(private SecurityService $security)
    {
    }

    /**
     * @return array{pending: Collection, pending_count: int, reviewed: Collection, unseen_count: int, count: int}
     */
    public function forAdmin($admin = null): array
    {
        $admin = $admin ?: auth('admin')->user();
        $result = ['pending' => collect(), 'pending_count' => 0, 'reviewed' => collect(), 'unseen_count' => 0, 'count' => 0];
        if (!$admin || !$this->security->tablesReady()) {
            return $result;
        }

        $branchId = (int) $admin->branch_id;
        if ($this->security->allows('security.cancel_approve', $admin)) {
            $pending = InvoiceCancelRequest::with('requester:id,name')
                ->where('branch_id', $branchId)
                ->where('status', InvoiceCancelRequest::STATUS_PENDING);
            $result['pending_count'] = (clone $pending)->count();
            $result['pending'] = $pending->latest('id')->limit(self::LIST_LIMIT)->get();
        }

        $seenAt = $this->seenAt($admin->id);
        $result['reviewed'] = InvoiceCancelRequest::with('reviewer:id,name')
            ->where('branch_id', $branchId)
            ->where('requested_by', $admin->id)
            ->whereIn('status', [InvoiceCancelRequest::STATUS_APPROVED, InvoiceCancelRequest::STATUS_REJECTED])
            ->where('reviewed_at', '>=', now()->subDays(self::REVIEWED_DAYS))
            ->latest('reviewed_at')
            ->limit(self::LIST_LIMIT)
            ->get()
            ->each(fn ($item) => $item->setAttribute('is_unseen', !$seenAt || $item->reviewed_at->gt($seenAt)));
        $result['unseen_count'] = $result['reviewed']->where('is_unseen', true)->count();
        $result['count'] = $result['pending_count'] + $result['unseen_count'];

        return $result;
    }

    public function markSeen(int $adminId): void
    {
        Cache::forever($this->seenKey($adminId), now()->toIso8601String());
    }

    private function seenAt(int $adminId): ?Carbon
    {
        $value = Cache::get($this->seenKey($adminId));

        return $value ? Carbon::parse($value) : null;
    }

    private function seenKey(int $adminId): string
    {
        return 'cancel_notes_seen:' . config('database.connections.mysql.database') . ':' . $adminId;
    }
}
