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
        if (! Schema::hasTable('users')) {
            return;
        }

        $needsSecret = ! Schema::hasColumn('users', 'two_factor_secret');
        $needsRecoveryCodes = ! Schema::hasColumn('users', 'two_factor_recovery_codes');
        $needsConfirmation = ! Schema::hasColumn('users', 'two_factor_confirmed_at');

        if ($needsSecret || $needsRecoveryCodes || $needsConfirmation) {
            Schema::table('users', function (Blueprint $table) use ($needsSecret, $needsRecoveryCodes, $needsConfirmation) {
                if ($needsSecret) {
                    $table->text('two_factor_secret')->nullable();
                }

                if ($needsRecoveryCodes) {
                    $table->text('two_factor_recovery_codes')->nullable();
                }

                if ($needsConfirmation) {
                    $table->timestamp('two_factor_confirmed_at')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('This migration may have adopted an existing schema and cannot be safely reversed.');
    }
};
