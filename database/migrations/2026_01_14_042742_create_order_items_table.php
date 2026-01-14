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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable(); // reference to orders
            $table->unsignedBigInteger('menu_id')->nullable(); // reference to menu_items
            $table->integer('quantity')->default(1);
            $table->integer('price')->default(0); // set price record, price can change overtime
            $table->integer('total_price')->default(0); // price * quantity
            $table->text('special_request')->nullable(); // special instructions
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('set null');

            $table->index(['order_id', 'menu_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
