<?php

namespace App\Listeners;

use App\Models\Admin;
use App\Models\LoginHistory;
use App\Services\SecurityService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class RecordLoginHistory
{
    public function handle(object $event): void
    {
        if (($event->guard ?? 'admin') !== 'admin' && !$event instanceof Lockout) {
            return;
        }

        try {
            if (!app(SecurityService::class)->tablesReady()) {
                return;
            }

            [$type, $admin, $email] = match (true) {
                $event instanceof Login => [LoginHistory::EVENT_LOGIN, $event->user, $event->user?->email],
                $event instanceof Logout => [LoginHistory::EVENT_LOGOUT, $event->user, $event->user?->email],
                $event instanceof Failed => [LoginHistory::EVENT_FAILED, $event->user, $event->credentials['email'] ?? null],
                $event instanceof Lockout => [LoginHistory::EVENT_LOCKED, null, $event->request->input('email')],
                default => [null, null, null],
            };

            if (!$type) {
                return;
            }

            $admin = $admin ?: ($email ? Admin::where('email', $email)->first() : null);

            LoginHistory::create([
                'branch_id' => $admin?->branch_id,
                'admin_id' => $admin?->id,
                'email' => $email ? mb_substr($email, 0, 190) : null,
                'event' => $type,
                'ip_address' => request()?->ip(),
                'user_agent' => mb_substr((string) request()?->userAgent(), 0, 1000),
            ]);

            if ($type === LoginHistory::EVENT_LOGIN) {
                LoginHistory::where('created_at', '<', now()->subMonth())->delete();
            }
        } catch (\Throwable $e) {
            Log::warning('Login history not recorded', ['error' => $e->getMessage()]);
        }
    }
}
