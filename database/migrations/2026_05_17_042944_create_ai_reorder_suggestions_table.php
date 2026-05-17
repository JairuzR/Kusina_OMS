<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_reorder_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('suggested_quantity', 10, 2);
            $table->string('urgency');
            $table->text('reasoning');
            $table->string('recommended_supplier')->nullable();
            $table->json('key_insights')->nullable();
            $table->integer('estimated_days_until_stockout')->nullable();
            $table->string('provider_used');
            $table->boolean('is_acted_on')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index('inventory_item_id');
            $table->index('urgency');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_reorder_suggestions');
    }
};