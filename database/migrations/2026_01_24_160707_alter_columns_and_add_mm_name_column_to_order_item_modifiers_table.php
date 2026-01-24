<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_item_modifiers', function (Blueprint $table) {
            $table->string('eng_name')->after('name');
            $table->string('mm_name')->after('eng_name');
        });

        DB::statement('UPDATE order_item_modifiers SET eng_name = name');

        // 3. Drop old column
        Schema::table('order_item_modifiers', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Restore old column
        Schema::table('order_item_modifiers', function (Blueprint $table) {
            $table->string('name')->after('id');
        });

        // 2. Copy data back
        DB::statement('UPDATE order_item_modifiers SET name = eng_name');

        // 3. Drop new columns
        Schema::table('order_item_modifiers', function (Blueprint $table) {
            $table->dropColumn(['eng_name', 'mm_name']);
        });
    }
};
