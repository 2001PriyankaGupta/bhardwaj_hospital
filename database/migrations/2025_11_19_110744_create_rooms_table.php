<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->foreignId('room_type_id')->constrained('room_types')->onDelete('cascade');
            $table->string('floor_number');
            $table->string('ward_name')->nullable();

            // Status Management
            $table->enum('status', ['available', 'occupied', 'maintenance', 'cleaning'])->default('available');

            // Capacity Info
            $table->integer('bed_count');
            $table->integer('current_occupancy')->default(0);

            // Additional Amenities (room-specific)
            $table->json('additional_amenities')->nullable();

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
};
