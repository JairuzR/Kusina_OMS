<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('mfa_enabled')->default(false)->after('status');
            $table->string('mfa_code', 6)->nullable()->after('mfa_enabled');
            $table->timestamp('mfa_code_expires_at')->nullable()->after('mfa_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mfa_enabled', 'mfa_code', 'mfa_code_expires_at']);
        });
    }
};