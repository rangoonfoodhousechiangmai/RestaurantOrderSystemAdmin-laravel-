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
        Schema::table('tables', function (Blueprint $table) {
            // drop old unique indexes
            $table->dropUnique('tables_slug_unique');
            $table->dropUnique('tables_qr_token_unique');

            // add soft-delete-aware unique indexes
            $table->unique(['slug', 'deleted_at']);
            $table->unique(['qr_token', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropUnique(['slug', 'deleted_at']);
            $table->dropUnique(['qr_token', 'deleted_at']);

            $table->unique('slug');
            $table->unique('qr_token');
        });
    }
};
