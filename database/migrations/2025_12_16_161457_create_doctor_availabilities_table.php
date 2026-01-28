<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('doctor_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');

            $table->enum('status', ['available', 'busy', 'break', 'unavailable'])->default('available');
            $table->integer('current_patient_id')->nullable();
            $table->integer('room_number')->nullable();
            $table->integer('max_patients_per_day')->default(30);
            $table->integer('patients_seen_today')->default(0);

            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();

            $table->boolean('is_accepting_patients')->default(true);
            $table->integer('estimated_wait_time')->default(15); // minutes

            $table->json('schedule')->nullable(); // Weekly schedule

            $table->timestamps();

            $table->unique('doctor_id');
            $table->index(['status', 'is_accepting_patients']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('doctor_availabilities');
    }
};
