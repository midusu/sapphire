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
        Schema::table('food_orders', function (Blueprint $table) {
            $table->string('menu_type')->default('room_service'); // room_service, restaurant
            $table->timestamp('scheduled_time')->nullable(); // for future orders
            $table->boolean('is_scheduled')->default(false); // flag for scheduled orders
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // link to authenticated user
            $table->string('order_source')->default('guest'); // guest, admin
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_orders', function (Blueprint $table) {
            $table->dropColumn([
                'menu_type',
                'scheduled_time',
                'is_scheduled',
                'user_id',
                'order_source'
            ]);
            $table->dropForeign(['user_id']);
        });
    }
};
