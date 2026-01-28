<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Single, Double, ICU, General Ward
            $table->text('description')->nullable();
            
            // Pricing Management
            $table->decimal('base_price', 10, 2);
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->json('seasonal_pricing')->nullable(); // {peak: 1.2, off_peak: 0.8}
            $table->json('discounts')->nullable(); // {weekly: 10, monthly: 15}
            
            // Amenities Setup (JSON format)
            $table->json('amenities')->nullable(); // ['AC', 'TV', 'Attached Bath', 'WiFi']
            
            // Capacity Planning
            $table->integer('max_capacity');
            $table->integer('current_utilization')->default(0);
            $table->integer('available_rooms')->default(0);
            $table->json('capacity_forecast')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_types');
    }
};