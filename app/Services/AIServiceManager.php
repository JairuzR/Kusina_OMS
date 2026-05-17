<?php

namespace App\Services;

use App\Models\AiUsageLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIServiceManager
{
    public function generateWithFallback(string $prompt, string $feature = 'general'): array
    {
        $chain = config('ai.fallback_chain', ['gemini', 'ollama']);

        foreach ($chain as $provider) {
            try {
                $result = $this->callProvider($provider, $prompt);
                $this->logUsage($provider, $feature, $result['usage'] ?? [], true);
                return [
                    'text'     => $result['text'],
                    'provider' => $provider,
                ];
            } catch (\Exception $e) {
                Log::warning("AI provider [{$provider}] failed: " . $e->getMessage());
                $this->logUsage($provider, $feature, [], false, $e->getMessage());
                continue;
            }
        }

        throw new \RuntimeException('All AI providers are unavailable. Please try again later.');
    }

    private function callProvider(string $provider, string $prompt): array
    {
        return match ($provider) {
            'gemini' => $this->callGemini($prompt),
            'ollama' => $this->callOllama($prompt),
            default  => throw new \InvalidArgumentException("Unknown provider: {$provider}"),
        };
    }

    private function callGemini(string $prompt): array
    {
        $config  = config('ai.providers.gemini');
        $apiKey  = $config['api_key'];
        $model   = $config['model'];
        $baseUrl = $config['base_url'];

        if (empty($apiKey)) {
            throw new \RuntimeException('Gemini API key is not configured.');
        }

        $response = Http::timeout($config['timeout'])
            ->post("{$baseUrl}/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature'     => 0.3,
                    'maxOutputTokens' => 1024,
                ],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Gemini API error: ' . $response->body());
        }

        $data = $response->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text']
            ?? throw new \RuntimeException('Unexpected Gemini response structure.');

        return [
            'text'  => trim($text),
            'usage' => [
                'input_tokens'  => $data['usageMetadata']['promptTokenCount'] ?? 0,
                'output_tokens' => $data['usageMetadata']['candidatesTokenCount'] ?? 0,
                'model'         => $model,
            ],
        ];
    }

    private function callOllama(string $prompt): array
    {
        $config  = config('ai.providers.ollama');
        $baseUrl = $config['base_url'];
        $model   = $config['model'];

        if (!$config['enabled']) {
            throw new \RuntimeException('Ollama is disabled.');
        }

        $response = Http::timeout($config['timeout'])
            ->post("{$baseUrl}/api/generate", [
                'model'  => $model,
                'prompt' => $prompt,
                'stream' => false,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Ollama error: ' . $response->body());
        }

        $text = $response->json('response')
            ?? throw new \RuntimeException('Unexpected Ollama response structure.');

        return [
            'text'  => trim($text),
            'usage' => ['model' => $model, 'input_tokens' => 0, 'output_tokens' => 0],
        ];
    }

    private function logUsage(string $provider, string $feature, array $usage, bool $success, ?string $error = null): void
    {
        AiUsageLog::create([
            'provider'      => $provider,
            'feature'       => $feature,
            'model_used'    => $usage['model'] ?? $provider,
            'input_tokens'  => $usage['input_tokens'] ?? 0,
            'output_tokens' => $usage['output_tokens'] ?? 0,
            'cost_estimate' => 0,
            'success'       => $success,
            'error_message' => $error,
        ]);
    }
}