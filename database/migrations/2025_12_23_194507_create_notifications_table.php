<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // Ye automatically 'id' naam se bigint unsigned AUTO_INCREMENT banayega

            $table->unsignedBigInteger('user_id');
            $table->string('type', 255);
            $table->string('title', 255)->nullable();
            $table->json('meta_data')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps(); // Ye automatically 'created_at' aur 'updated_at' columns banayega

            // Foreign keys add karein (agar chahiye toh)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
