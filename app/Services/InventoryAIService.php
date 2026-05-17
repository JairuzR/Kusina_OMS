<?php

namespace App\Services;

use App\Models\AiReorderSuggestion;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Log;

class InventoryAIService
{
    public function __construct(private AIServiceManager $ai) {}

    public function generateReorderSuggestion(InventoryItem $item): AiReorderSuggestion
    {
        $prompt = $this->buildPrompt($item);
        $result = $this->ai->generateWithFallback($prompt, 'reorder_suggestions');
        $parsed = $this->parseResponse($result['text']);

        // Delete old suggestion for this item
        AiReorderSuggestion::where('inventory_item_id', $item->id)->delete();

        $suggestion = AiReorderSuggestion::create([
            'inventory_item_id'          => $item->id,
            'suggested_quantity'         => $parsed['suggested_quantity'],
            'urgency'                    => $parsed['urgency'],
            'reasoning'                  => $parsed['reasoning'],
            'recommended_supplier'       => $parsed['recommended_supplier'],
            'key_insights'               => $parsed['key_insights'],
            'estimated_days_until_stockout' => $parsed['estimated_days_until_stockout'],
            'provider_used'              => $result['provider'],
        ]);

        Log::channel('ai_audit')->info('AI Reorder Suggestion Generated', [
            'feature'       => 'reorder_suggestions',
            'item_id'       => $item->id,
            'item_name'     => $item->name,
            'urgency'       => $parsed['urgency'],
            'provider_used' => $result['provider'],
            'timestamp'     => now()->toIso8601String(),
        ]);

        return $suggestion;
    }

    private function buildPrompt(InventoryItem $item): string
    {
        $supplier = $item->supplier?->name ?? 'No supplier assigned';
        $recentTx = $item->transactions()->latest()->take(5)->get();

        $txLines = $recentTx->map(function ($tx) {
            return "- {$tx->type}: {$tx->quantity} {$tx->inventory_item->unit ?? ''} ({$tx->reason}) on {$tx->created_at->format('M d')}";
        })->implode("\n");

        if (empty($txLines)) {
            $txLines = "No recent transactions recorded.";
        }

        return <<<PROMPT
You are an expert restaurant inventory manager. Analyze this inventory item and provide a reorder recommendation.

ITEM DETAILS:
- Name: {$item->name}
- Current Stock: {$item->quantity} {$item->unit}
- Minimum Threshold: {$item->min_quantity} {$item->unit}
- Cost Per Unit: ₱{$item->cost_per_unit}
- Supplier: {$supplier}
- Category: {$item->category ?? 'Uncategorized'}
- Storage Location: {$item->storage_location ?? 'Not specified'}

RECENT TRANSACTIONS (last 5):
{$txLines}

Provide your analysis in EXACTLY this JSON format (raw JSON only, no markdown):
{
  "suggested_quantity": 50,
  "urgency": "high",
  "reasoning": "2-3 sentence explanation of why this quantity and urgency level.",
  "recommended_supplier": "Supplier name or null",
  "key_insights": ["insight one", "insight two", "insight three"],
  "estimated_days_until_stockout": 3
}

Rules:
- urgency must be exactly one of: low, medium, high, critical
- suggested_quantity must be a positive number
- key_insights must be 2 to 4 short phrases
- estimated_days_until_stockout must be an integer or null
- Return ONLY the JSON object, nothing else
CRITICAL: Your entire response must be a single valid JSON object. No text before or after.
PROMPT;
    }

    private function parseResponse(string $rawText): array
    {
        $clean = preg_replace('/```(?:json)?\s*|\s*```/', '', $rawText);
        $clean = trim($clean);

        if (preg_match('/\{.*\}/s', $clean, $matches)) {
            $clean = $matches[0];
        }

        $data = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
            Log::error('AI reorder suggestion JSON parse failed', [
                'raw'   => $rawText,
                'error' => json_last_error_msg(),
            ]);
            return [
                'suggested_quantity'          => $item->min_quantity * 2 ?? 10,
                'urgency'                     => 'medium',
                'reasoning'                   => 'Analysis could not be parsed. Please try again.',
                'recommended_supplier'        => null,
                'key_insights'                => [],
                'estimated_days_until_stockout' => null,
            ];
        }

        $validUrgencies = ['low', 'medium', 'high', 'critical'];
        $urgency = in_array($data['urgency'] ?? '', $validUrgencies)
            ? $data['urgency']
            : 'medium';

        return [
            'suggested_quantity'          => max(1, (float) ($data['suggested_quantity'] ?? 10)),
            'urgency'                     => $urgency,
            'reasoning'                   => substr($data['reasoning'] ?? 'No reasoning provided.', 0, 1000),
            'recommended_supplier'        => $data['recommended_supplier'] ?? null,
            'key_insights'                => array_slice($data['key_insights'] ?? [], 0, 4),
            'estimated_days_until_stockout' => isset($data['estimated_days_until_stockout'])
                ? (int) $data['estimated_days_until_stockout']
                : null,
        ];
    }
}