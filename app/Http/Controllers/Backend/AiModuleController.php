<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class AiModuleController extends Controller
{
    public function __construct()
    {
        $this->checkGuard();
    }

    public function index()
    {
        $admin = auth('admin')->user();

        if (!$this->canAccessAi($admin)) {
            abort(403, 'You do not have permission to access AI features.');
        }

        $locale = app()->getLocale() === 'bn' ? 'bn' : 'en';

        return view('backend.pages.ai.index', [
            'schemaReady' => Schema::hasTable('ai_chat_sessions'),
            'canReports' => $admin->can('ai.reports') || $admin->hasRole('Super Admin'),
            'canHealth' => $admin->can('ai.health') || $admin->hasRole('Super Admin'),
            'canChat' => $admin->can('ai.chat') || $admin->hasRole('Super Admin'),
            'canAnalytics' => $admin->can('ai.analytics') || $admin->hasRole('Super Admin'),
            'docsUrl' => route('admin.help.show', [$locale, 'ai']),
        ]);
    }

    private function canAccessAi($admin): bool
    {
        if (!$admin) {
            return false;
        }

        if ($admin->hasRole('Super Admin')) {
            return true;
        }

        return $admin->can('ai.reports')
            || $admin->can('ai.health')
            || $admin->can('ai.chat')
            || $admin->can('ai.analytics');
    }
}
