<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('appointment_id')->nullable()->after('invoice_id');
            $table->unsignedBigInteger('patient_id')->nullable()->after('appointment_id');
            $table->string('currency', 10)->default('INR')->after('amount');
            $table->string('status')->default('pending')->after('currency');
            $table->json('meta')->nullable()->after('notes');

            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
        });

        // Make invoice_id nullable (direct statement to avoid dbal dependency)
        DB::statement('ALTER TABLE payments MODIFY invoice_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'appointment_id')) {
                $table->dropForeign(['appointment_id']);
                $table->dropColumn('appointment_id');
            }
            if (Schema::hasColumn('payments', 'patient_id')) {
                $table->dropForeign(['patient_id']);
                $table->dropColumn('patient_id');
            }
            if (Schema::hasColumn('payments', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('payments', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('payments', 'meta')) {
                $table->dropColumn('meta');
            }
        });

        // Revert invoice_id not-null (assumes no nulls exist)
        DB::statement('ALTER TABLE payments MODIFY invoice_id BIGINT UNSIGNED NOT NULL');
    }
};
