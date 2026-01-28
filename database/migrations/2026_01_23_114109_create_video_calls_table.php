// database/migrations/xxxx_create_video_calls_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('video_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->string('channel_name');
            $table->string('token')->nullable();
            $table->string('status')->default('initiated'); // initiated, ongoing, completed, cancelled
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration')->nullable(); // in seconds
            $table->json('call_data')->nullable(); // store additional call data
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('video_calls');
    }
};