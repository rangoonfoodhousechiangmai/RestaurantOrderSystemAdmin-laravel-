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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_type', ['cash', 'online'])->nullable()->after('status');
            $table->boolean('payment_status')->default(false)->after('payment_type');
            $table->string('payment_image_path')->nullable()->after('payment_status');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_image_path');
            $table->unsignedBigInteger('payment_verified_by')->nullable()->after('payment_verified_at'); // admin who verified
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_type',
                'payment_image_path',
                'payment_verified_at',
                'payment_verified_by',
            ]);
        });
    }
};
