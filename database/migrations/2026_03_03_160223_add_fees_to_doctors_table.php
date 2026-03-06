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
        Schema::table('doctors', function (Blueprint $table) {
            $table->decimal('new_patient_fee', 10, 2)->nullable()->after('consultation_fee');
            $table->decimal('old_patient_fee', 10, 2)->nullable()->after('new_patient_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['new_patient_fee', 'old_patient_fee']);
        });
    }
};
