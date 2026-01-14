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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique(); // e.g., ORD-20260111-001
            $table->string('order_token')->unique();
            $table->unsignedBigInteger('table_id')->nullable();
            $table->unsignedBigInteger('table_session_id')->nullable();
            $table->enum('order_type', ['dine_in', 'take_away'])->default('dine_in');
            $table->integer('total_price');
            $table->integer('total_qty')->default(0);
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
