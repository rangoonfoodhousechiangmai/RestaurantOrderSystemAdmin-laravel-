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
        // 1. Add new columns
        Schema::table('modifiers', function (Blueprint $table) {
            $table->string('eng_name')->after('name');
            $table->string('mm_name')->nullable()->after('eng_name');
        });

        // 2. Copy data
        DB::statement('UPDATE modifiers SET eng_name = name');

        // 3. Drop old column
        Schema::table('modifiers', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        // 1. Restore old column
        Schema::table('modifiers', function (Blueprint $table) {
            $table->string('name')->after('id');
        });

        // 2. Copy data back
        DB::statement('UPDATE modifiers SET name = eng_name');

        // 3. Drop new columns
        Schema::table('modifiers', function (Blueprint $table) {
            $table->dropColumn(['eng_name', 'mm_name']);
        });
    }
};
