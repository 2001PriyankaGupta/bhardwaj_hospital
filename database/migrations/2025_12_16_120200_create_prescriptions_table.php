<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    // database/migrations/xxxx_xx_xx_create_prescriptions_table.php
public function up()
{
    Schema::create('prescriptions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('medical_record_id')->constrained('patient_medical_records')->onDelete('cascade');
        $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
        $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
        $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
        $table->text('medication_details')->nullable(); // JSON format for multiple medicines
        $table->text('instructions')->nullable();
        $table->text('follow_up_advice')->nullable();
        $table->date('prescription_date');
        $table->date('valid_until')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();

        // Indexes
        $table->index(['patient_id', 'prescription_date']);
        $table->index('appointment_id');
    });
}


    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
