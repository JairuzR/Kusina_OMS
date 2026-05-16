<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('waiter')->after('email');
            $table->string('status')->default('active')->after('role');
            $table->string('avatar')->nullable()->after('status');
            $table->string('phone')->nullable()->after('avatar');
            $table->timestamp('last_login_at')->nullable()->after('phone');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'status', 'avatar', 'phone',
                'last_login_at', 'last_login_ip'
            ]);
        });
    }
};
