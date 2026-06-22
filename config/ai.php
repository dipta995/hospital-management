<?php

return [

  'enabled' => env('AI_ENABLED', true),

  'provider' => env('AI_PROVIDER', 'openai'),

  'api_key' => env('AI_API_KEY'),

  'api_url' => env('AI_API_URL', 'https://api.openai.com/v1/chat/completions'),

  'model' => env('AI_MODEL', 'gpt-4o-mini'),

  'max_tokens' => (int) env('AI_MAX_TOKENS', 2048),

  'temperature' => (float) env('AI_TEMPERATURE', 0.4),

  'timeout' => (int) env('AI_TIMEOUT', 60),

  'rate_limit_per_minute' => (int) env('AI_RATE_LIMIT', 20),

];
