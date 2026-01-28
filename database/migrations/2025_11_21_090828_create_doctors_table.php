<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone');
            $table->string('license_number')->unique();
            $table->foreignId('specialty_id')->constrained('specialties')->onDelete('cascade');
            $table->text('qualifications')->nullable();
            $table->text('experience')->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_image')->nullable();
            $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active');
            $table->decimal('consultation_fee', 8, 2)->default(0);
            $table->time('average_consultation_time')->default('00:15:00');
            $table->json('working_days')->nullable(); // ['monday', 'tuesday', ...]
            $table->time('shift_start_time')->default('09:00:00');
            $table->time('shift_end_time')->default('17:00:00');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('doctors');
    }
};
