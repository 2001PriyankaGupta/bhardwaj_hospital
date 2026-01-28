<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('beds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');

            $table->string('bed_number');
            $table->enum('status', ['available', 'occupied', 'maintenance', 'cleaning'])->default('available');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('last_occupancy_date')->nullable();
            $table->date('next_availability_date')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
};
