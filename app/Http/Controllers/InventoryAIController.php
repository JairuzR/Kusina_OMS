<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateReorderSuggestion;
use App\Models\AiReorderSuggestion;
use App\Models\AiUsageLog;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryAIController extends Controller
{
    public function suggest(InventoryItem $inventoryItem)
    {
        GenerateReorderSuggestion::dispatch($inventoryItem)->onQueue('ai-tasks');

        return back()->with('success', 'AI reorder suggestion queued. Refresh in a few seconds.');
    }

    public function markActedOn(AiReorderSuggestion $suggestion)
    {
        $suggestion->update(['is_acted_on' => true]);
        return back()->with('success', 'Suggestion marked as acted on.');
    }

    public function dashboard()
    {
        $suggestions = AiReorderSuggestion::with('inventoryItem')
            ->where('is_acted_on', false)
            ->latest()
            ->get();

        $stats = AiUsageLog::selectRaw('
            provider,
            COUNT(*) as total_calls,
            SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful,
            SUM(CASE WHEN success = 0 THEN 1 ELSE 0 END) as failed
        ')
        ->groupBy('provider')
        ->get();

        $recentLogs = AiUsageLog::latest()->take(15)->get();

        return view('inventory.ai-dashboard', compact('suggestions', 'stats', 'recentLogs'));
    }
}