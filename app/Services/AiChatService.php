<?php

namespace App\Services;

use App\Models\AiChatMessage;
use App\Models\AiChatSession;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AiChatService
{
    public function __construct(
        private AiClientService $aiClient,
        private AiIntelligenceService $intelligence,
    ) {}

    public function chat(int $branchId, int $adminId, string $message, ?int $sessionId = null): array
    {
        $message = trim($message);

        if ($message === '') {
            return [
                'success' => false,
                'message' => 'Message cannot be empty.',
            ];
        }

        $session = $this->resolveSession($branchId, $adminId, $sessionId, $message);
        $history = $this->loadHistory($session);

        $this->storeMessage($session, 'user', $message, 'user');

        $messages = $this->buildMessages($branchId, $history, $message);

        $result = $this->aiClient->chat(
            $messages,
            fn () => $this->fallbackReply($branchId, $message)
        );

        $this->storeMessage($session, 'assistant', $result['content'], $result['source']);

        return [
            'success' => true,
            'session_id' => $session->id,
            'reply' => $result['content'],
            'source' => $result['source'],
        ];
    }

    public function suggestions(int $branchId): array
    {
        $locale = app()->getLocale() === 'bn' ? 'bn' : 'en';

        return [
            'suggestions' => $this->intelligence->chatSuggestions($branchId, $locale),
        ];
    }

    public function history(int $branchId, int $adminId, ?int $sessionId = null): array
    {
        if (!Schema::hasTable('ai_chat_sessions')) {
            return ['messages' => []];
        }

        $query = AiChatSession::where('branch_id', $branchId)
            ->where('admin_id', $adminId)
            ->orderByDesc('updated_at');

        $session = $sessionId
            ? $query->where('id', $sessionId)->first()
            : $query->first();

        if (!$session) {
            return ['messages' => [], 'session_id' => null];
        }

        return [
            'session_id' => $session->id,
            'messages' => $session->messages()
                ->orderBy('id')
                ->get(['role', 'content', 'source', 'created_at'])
                ->toArray(),
        ];
    }

    private function buildMessages(int $branchId, array $history, string $message): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => $this->systemPrompt($branchId),
            ],
        ];

        foreach ($history as $row) {
            $messages[] = [
                'role' => $row['role'] === 'user' ? 'user' : 'assistant',
                'content' => $row['content'],
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        return $messages;
    }

    private function resolveSession(int $branchId, int $adminId, ?int $sessionId, string $message): AiChatSession
    {
        if (!Schema::hasTable('ai_chat_sessions')) {
            return new AiChatSession([
                'branch_id' => $branchId,
                'admin_id' => $adminId,
                'title' => Str::limit($message, 60),
            ]);
        }

        if ($sessionId) {
            $existing = AiChatSession::where('branch_id', $branchId)
                ->where('admin_id', $adminId)
                ->where('id', $sessionId)
                ->first();

            if ($existing) {
                $existing->touch();

                return $existing;
            }
        }

        return AiChatSession::create([
            'branch_id' => $branchId,
            'admin_id' => $adminId,
            'title' => Str::limit($message, 60),
        ]);
    }

    private function loadHistory(AiChatSession $session): array
    {
        if (!$session->exists || !Schema::hasTable('ai_chat_messages')) {
            return [];
        }

        return $session->messages()
            ->orderByDesc('id')
            ->limit(10)
            ->get(['role', 'content'])
            ->reverse()
            ->values()
            ->toArray();
    }

    private function storeMessage(AiChatSession $session, string $role, string $content, string $source): void
    {
        if (!$session->exists || !Schema::hasTable('ai_chat_messages')) {
            return;
        }

        AiChatMessage::create([
            'session_id' => $session->id,
            'role' => $role,
            'content' => $content,
            'source' => $source,
        ]);
    }

    private function systemPrompt(int $branchId): string
    {
        return implode("\n\n", [
            'You are HMS (Hospital Management System) AI assistant for admin staff.',
            'Help with invoices, lab reports, pharmacy, OPD serials, admits, reports, and dashboard metrics.',
            'Be concise, actionable, and professional. Suggest specific modules when helpful.',
            'Never expose unnecessary patient data.',
            'Live branch context:',
            $this->intelligence->contextPrompt($branchId),
        ]);
    }

    private function fallbackReply(int $branchId, string $message): string
    {
        $lower = Str::lower($message);
        $ctx = $this->intelligence->branchContext($branchId);

        if (Str::contains($lower, ['collection', 'revenue', 'আদায়', 'লাভ'])) {
            return 'Today\'s collection: ৳'.number_format($ctx['collection_today'], 0)
                .'. Net: ৳'.number_format($ctx['net_today'], 0)
                .'. Check Dashboard → Collections for details.';
        }

        if (Str::contains($lower, ['lab', 'test', 'pending', 'ল্যাব'])) {
            return $ctx['pending_labs'].' lab test(s) pending. Open Labs module to process queue.';
        }

        if (Str::contains($lower, ['pharmacy', 'stock', 'medicine', 'ফার্মেসি', 'স্টক'])) {
            return 'Pharmacy: '.$ctx['pharmacy_stock_out'].' out of stock, '.$ctx['pharmacy_stock_low'].' low stock. Open Pharmacy → Products.';
        }

        if (Str::contains($lower, ['due', 'outstanding', 'বকেয়া'])) {
            return 'Outstanding dues: ৳'.number_format($ctx['outstanding_due'], 0).'. Review Invoices with unpaid balance.';
        }

        if (Str::contains($lower, ['patient', 'profile', '360', 'রোগী'])) {
            return 'Open Users → select patient → Patient 360 for full clinical and billing history.';
        }

        $analysis = $this->intelligence->businessAnalysis($branchId);
        if (!empty($analysis['priority_actions'])) {
            $top = $analysis['priority_actions'][0];

            return "Health score {$analysis['health_score']}/100. Priority: {$top['title']} — {$top['detail']}";
        }

        return 'Ask about today\'s revenue, pending labs, pharmacy stock, dues, or patient profiles.';
    }
}
