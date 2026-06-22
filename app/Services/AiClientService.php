<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiClientService
{
    public function isConfigured(): bool
    {
        return config('ai.enabled')
            && !empty(config('ai.api_key'));
    }

    public function complete(string $systemPrompt, string $userPrompt, ?callable $fallback = null): array
    {
        return $this->chat(
            [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            $fallback
        );
    }

    public function chat(array $messages, ?callable $fallback = null): array
    {
        if (!$this->isConfigured()) {
            return [
                'content' => $fallback ? (string) $fallback() : __('language.ai.not_configured'),
                'source' => 'engine',
            ];
        }

        try {
            $response = Http::timeout(config('ai.timeout', 60))
                ->withToken(config('ai.api_key'))
                ->post(config('ai.api_url'), [
                    'model' => config('ai.model'),
                    'max_tokens' => config('ai.max_tokens'),
                    'temperature' => (float) config('ai.temperature', 0.4),
                    'messages' => $messages,
                ]);

            if (!$response->successful()) {
                Log::warning('AI API error', ['status' => $response->status()]);

                return [
                    'content' => $fallback ? (string) $fallback() : __('language.ai.request_failed'),
                    'source' => 'engine',
                ];
            }

            $content = $response->json('choices.0.message.content');

            if (!is_string($content) || trim($content) === '') {
                return [
                    'content' => $fallback ? (string) $fallback() : __('language.ai.empty_response'),
                    'source' => 'engine',
                ];
            }

            return [
                'content' => trim($content),
                'source' => 'ai',
            ];
        } catch (\Throwable $e) {
            Log::warning('AI request exception', ['message' => $e->getMessage()]);

            return [
                'content' => $fallback ? (string) $fallback() : __('language.ai.request_failed'),
                'source' => 'engine',
            ];
        }
    }
}
