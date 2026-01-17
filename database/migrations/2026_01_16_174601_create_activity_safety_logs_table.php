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
        Schema::create('activity_safety_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('activity_id')->constrained()->onDelete('cascade');
            $table->string('activity_type'); // zipline, swimming, etc.
            $table->datetime('activity_date');
            $table->integer('participants');
            $table->json('safety_checks')->nullable(); // Equipment check, weather, etc.
            $table->text('safety_notes')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->string('weather_conditions')->nullable();
            $table->string('equipment_status')->nullable(); // good, needs_repair, etc.
            $table->text('incident_report')->nullable();
            $table->boolean('incident_occurred')->default(false);
            $table->string('status')->default('completed'); // completed, cancelled, incident
            $table->foreignId('logged_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['activity_booking_id', 'activity_date']);
            $table->index('activity_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_safety_logs');
    }
};
