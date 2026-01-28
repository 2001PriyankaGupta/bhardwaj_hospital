<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('doctor_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->date('performance_date');
            $table->integer('total_appointments')->default(0);
            $table->integer('completed_appointments')->default(0);
            $table->integer('cancelled_appointments')->default(0);
            $table->integer('no_show_appointments')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->decimal('revenue_generated', 10, 2)->default(0);
            $table->integer('patient_satisfaction_score')->default(0); // 1-100
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('doctor_performances');
    }
};
