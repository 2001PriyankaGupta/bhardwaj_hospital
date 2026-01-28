<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->enum('user_type', ['admin', 'doctor', 'staff', 'patient'])->default('patient');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('locality')->nullable();
            $table->integer('age')->nullable()->after('gender');
            $table->text('address')->nullable()->after('age');
            $table->string('emergency_contact_number')->nullable()->after('address');
            $table->string('alternate_contact_number')->nullable()->after('emergency_contact_number');
            $table->text('basic_medical_history')->nullable()->after('alternate_contact_number');
            $table->string('otp')->nullable()->after('password');
            $table->timestamp('otp_expires_at')->nullable()->after('otp');
            $table->string('otp_type')->nullable()->after('otp_expires_at');
            $table->boolean('is_verified')->default(false)->after('otp_type');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_type', 'status', 'locality', 'age', 'address', 'emergency_contact_number', 'alternate_contact_number', 'basic_medical_history', 'otp', 'otp_expires_at', 'otp_type', 'is_verified']);
        });
    }
};
