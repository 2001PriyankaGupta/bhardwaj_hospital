<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('medical_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
        $table->string('record_type'); // diagnosis, prescription, lab_report, etc.
        $table->text('description');
        $table->text('notes')->nullable();
        $table->string('attachment')->nullable();
        $table->date('record_date');
        $table->foreignId('created_by')->constrained('users');
        $table->timestamps();
    });
    }


    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
