<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->boolean('is_liked')->default(true);
            $table->timestamps();
            
            // Ek user ek doctor ko sirf ek baar like kar sake
            $table->unique(['user_id', 'doctor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_likes');
    }
};