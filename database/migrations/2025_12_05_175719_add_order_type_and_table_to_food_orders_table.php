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
            $table->string('order_type')->default('room_service'); // room_service, dine_in
            $table->string('table_number')->nullable(); // for dine-in orders
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('cascade'); // link to room booking
            $table->boolean('added_to_room_bill')->default(false);
            $table->string('kot_number')->unique(); // Kitchen Order Ticket number
            $table->timestamp('kot_time')->nullable(); // when KOT was generated
            $table->timestamp('kitchen_completed_time')->nullable(); // when kitchen completed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_type',
                'table_number',
                'booking_id',
                'added_to_room_bill',
                'kot_number',
                'kot_time',
                'kitchen_completed_time'
            ]);
            $table->dropForeign(['booking_id']);
        });
    }
};
