<?php

namespace App\Jobs;

use App\Models\InventoryItem;
use App\Services\InventoryAIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateReorderSuggestion implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(public InventoryItem $item) {}

    public function handle(InventoryAIService $service): void
    {
        Log::info("Generating AI reorder suggestion for: {$this->item->name}");
        $service->generateReorderSuggestion($this->item);
        Log::info("AI reorder suggestion complete for: {$this->item->name}");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateReorderSuggestion job failed for: {$this->item->name}", [
            'error' => $exception->getMessage(),
        ]);
    }
}