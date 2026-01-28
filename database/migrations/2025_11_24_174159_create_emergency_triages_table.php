<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmergencyTriagesTable extends Migration
{
    public function up()
    {
        Schema::create('emergency_triages', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->string('patient_name');
            $table->integer('age');
            $table->string('gender');
            $table->text('symptoms');
            $table->string('triage_level'); // Red, Yellow, Green, Blue
            $table->string('priority_score');
            $table->integer('assigned_staff')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->text('notes')->nullable();
            $table->timestamp('arrival_time')->useCurrent();
            $table->timestamp('treatment_time')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('emergency_triages');
    }
}