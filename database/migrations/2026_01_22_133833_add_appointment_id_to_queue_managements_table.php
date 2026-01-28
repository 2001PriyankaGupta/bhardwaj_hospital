<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('queue_managements', function (Blueprint $table) {
            $table->foreignId('appointment_id')
                  ->nullable()
                  ->constrained('appointments')
                  ->onDelete('set null');
                  
            // Add index for better performance
            $table->index(['appointment_id', 'status']);
        });
    }

    public function down()
    {
        Schema::table('queue_managements', function (Blueprint $table) {
            $table->dropForeign(['appointment_id']);
            $table->dropColumn('appointment_id');
        });
    }
};