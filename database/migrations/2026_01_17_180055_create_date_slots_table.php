<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
      
        
        // Nai table create karenge
        Schema::create('date_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->date('slot_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('slot_duration')->default(15); // minutes
            $table->integer('max_patients')->default(10);
            $table->integer('booked_slots')->default(0);
            $table->boolean('is_available')->default(true);
            $table->json('time_slots')->nullable(); // Generated time slots store karenge
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['doctor_id', 'slot_date', 'start_time'], 'doctor_date_time_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('date_slots');
        // Purani table wapas create karna ho toh yahan code add karein
    }
};