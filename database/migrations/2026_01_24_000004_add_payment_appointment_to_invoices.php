<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'payment_id')) {
                $table->unsignedBigInteger('payment_id')->nullable()->after('id');
                $table->unsignedBigInteger('appointment_id')->nullable()->after('payment_id');
                $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
                $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'payment_id')) {
                $table->dropForeign(['payment_id']);
                $table->dropColumn('payment_id');
            }
            if (Schema::hasColumn('invoices', 'appointment_id')) {
                $table->dropForeign(['appointment_id']);
                $table->dropColumn('appointment_id');
            }
        });
    }
};
