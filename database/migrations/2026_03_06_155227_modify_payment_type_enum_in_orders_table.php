<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Change enum to include both old and new values temporarily
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_type', ['cash', 'online', 'prompt_pay'])->nullable()->change();
        });

        // Step 2: Update any 'online' values to 'prompt_pay'
        DB::statement("UPDATE orders SET payment_type = 'prompt_pay' WHERE payment_type = 'online'");

        // Step 3: Change enum to final values only
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_type', ['cash', 'prompt_pay'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Change enum to include both old and new values temporarily
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_type', ['cash', 'online', 'prompt_pay'])->nullable()->change();
        });

        // Step 2: Update any 'prompt_pay' values back to 'online'
        DB::statement("UPDATE orders SET payment_type = 'online' WHERE payment_type = 'prompt_pay'");

        // Step 3: Change enum back to original values
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_type', ['cash', 'online'])->nullable()->change();
        });
    }
};

