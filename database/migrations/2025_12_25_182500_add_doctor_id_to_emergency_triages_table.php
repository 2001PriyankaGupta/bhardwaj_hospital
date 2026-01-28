<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('emergency_triages', function (Blueprint $table) {
            if (!Schema::hasColumn('emergency_triages', 'doctor_id')) {
                $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete()->after('patient_name');
            }
        });
    }

    public function down()
    {
        Schema::table('emergency_triages', function (Blueprint $table) {
            if (Schema::hasColumn('emergency_triages', 'doctor_id')) {
                $table->dropConstrainedForeignId('doctor_id');
            }
        });
    }
};
