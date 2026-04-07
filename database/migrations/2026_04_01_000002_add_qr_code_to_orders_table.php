<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('qr_code_path')->nullable()->after('order_number');
            $table->string('qr_code_data')->nullable()->after('qr_code_path');
            $table->foreignId('payment_method_id')->nullable()->after('address_id')->constrained('payment_methods')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn(['qr_code_path', 'qr_code_data', 'payment_method_id']);
        });
    }
};
