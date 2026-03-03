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
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('doctor_id')->nullable()->change();
            $table->foreignId('staff_id')->nullable()->after('doctor_id')->constrained('staff')->onDelete('cascade');
            $table->enum('applicant_type', ['doctor', 'staff'])->after('staff_id')->default('doctor');
        });
    }

    public function down(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('doctor_id')->nullable(false)->change();
            $table->dropForeign(['staff_id']);
            $table->dropColumn(['staff_id', 'applicant_type']);
        });
    }
};
