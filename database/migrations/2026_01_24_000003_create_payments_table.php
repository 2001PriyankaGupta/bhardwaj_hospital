<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('appointment_id')->nullable();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->decimal('amount', 10, 2)->default(0.00);
                $table->string('currency', 10)->default('INR');
                $table->string('payment_method')->nullable();
                $table->string('status')->default('pending'); // pending, completed, failed
                $table->string('transaction_id')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
                $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};