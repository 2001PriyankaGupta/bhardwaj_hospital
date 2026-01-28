<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_managements', function (Blueprint $table) {
            $table->id();
            $table->string('queue_number')->unique();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->enum('queue_type', ['normal', 'emergency', 'follow_up'])->default('normal');
            $table->enum('status', ['waiting', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('waiting');
            $table->integer('estimated_wait_time')->nullable()->comment('In minutes');
            $table->integer('priority_score')->default(0);
            $table->text('reason_for_visit')->nullable();
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('consultation_start_time')->nullable();
            $table->timestamp('consultation_end_time')->nullable();
            $table->string('current_room')->nullable();
            $table->boolean('is_priority')->default(false);
            $table->integer('position')->default(0);
            $table->json('vital_signs')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['doctor_id', 'status']);
            $table->index(['status', 'position']);
            $table->index(['queue_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_managements');
    }
};
