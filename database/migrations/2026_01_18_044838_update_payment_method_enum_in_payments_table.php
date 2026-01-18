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
        // Since SQLite doesn't support changing columns directly, we have to recreate the table or just drop the check constraint logic.
        // However, Laravel's schema builder for SQLite is limited. 
        // The easiest way for SQLite is to allow 'pending' by recreating the constraint or ignoring it if we can't easily change it.
        // But for a production-like fix in Laravel with SQLite:
        
        // We will modify the column to string to allow any value, or update the enum definition.
        // Note: SQLite doesn't natively support ENUMs, Laravel emulates them with CHECK constraints.
        // Dropping the column and re-adding it is risky for data loss.
        
        // Let's use a raw statement to drop the check constraint if possible, or just accept that for SQLite we might need to recreate the table.
        // Actually, for this specific error "Integrity constraint violation: 19 CHECK constraint failed: payment_method",
        // it means the value 'pending' is NOT in the allowed list ['cash', 'card', 'bank_transfer', 'online'].
        
        // We need to add 'pending' to the allowed values.
        
        Schema::table('payments', function (Blueprint $table) {
            // We can't easily modify the check constraint in SQLite via Schema builder.
            // But we can try to change the column definition.
            $table->string('payment_method')->change(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Revert back to enum is tricky with data that might not fit.
            // We'll leave it as string for safety in down.
        });
    }
};
