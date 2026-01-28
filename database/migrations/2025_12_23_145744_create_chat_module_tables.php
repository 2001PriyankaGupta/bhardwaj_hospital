<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {

        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('conversation_id')->unique();
            $table->unsignedBigInteger('appointment_id')->nullable();

            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable()->comment('Assigned doctor');
            $table->enum('status', ['active', 'closed', 'pending', 'resolved'])->default('active');
            $table->enum('priority', ['low', 'medium', 'high', 'emergency'])->default('medium');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->nullOnDelete();
            $table->foreign('doctor_id')->references('id')->on('doctors')->nullOnDelete();
            $table->foreign('appointment_id')->references('id')->on('appointments')->nullOnDelete();
        });

        /**
         * 2. Chat Messages
         */
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('conversation_id');
            $table->enum('sender_type', ['patient', 'admin', 'system', 'doctor', 'staff']);
            $table->unsignedBigInteger('sender_id');
            $table->enum('message_type', [
                'text',
                'image',
                'file',
                'appointment',
                'prescription',
                'location',
                'quick_reply'
            ]);
            $table->text('message')->nullable();
            $table->json('attachments')->nullable();
            $table->json('metadata')->nullable()->comment('Appointment links, prescription IDs etc.');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->index('conversation_id');
            $table->index(['sender_type', 'sender_id']);

            $table->foreign('conversation_id')
                ->references('conversation_id')
                ->on('chat_conversations')
                ->cascadeOnDelete();
        });

        /**
         * 3. Chat Quick Replies
         */
        Schema::create('chat_quick_replies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('reply_type', ['text', 'action'])->default('text');
            $table->enum('action_type', [
                'book_appointment',
                'view_reports',
                'ask_doctor',
                'emergency',
                'billing',
                'general'
            ])->nullable();
            $table->string('icon')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });

        /**
         * 4. Chat Assignments
         */
        Schema::create('chat_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('conversation_id');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('unassigned_at')->nullable();
            $table->text('notes')->nullable();

            $table->foreign('conversation_id')
                ->references('conversation_id')
                ->on('chat_conversations')
                ->cascadeOnDelete();

            $table->foreign('assigned_to')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        /**
         * 5. Chat Ratings
         */
        Schema::create('chat_ratings', function (Blueprint $table) {
            $table->id();
            $table->string('conversation_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->tinyInteger('rating')->checkBetween(1, 5);
            $table->text('feedback')->nullable();
            $table->timestamp('rated_at')->useCurrent();

            $table->foreign('conversation_id')
                ->references('conversation_id')
                ->on('chat_conversations')
                ->nullOnDelete();

            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_ratings');
        Schema::dropIfExists('chat_assignments');
        Schema::dropIfExists('chat_quick_replies');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');
    }
};
