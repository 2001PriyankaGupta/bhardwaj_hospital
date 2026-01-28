<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('chat_messages')) {
            // Use raw SQL to avoid requiring doctrine/dbal for column changes
            DB::statement('ALTER TABLE `chat_messages` MODIFY `sender_id` BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chat_messages')) {
            // Revert to NOT NULL (may fail if NULL values exist)
            DB::statement('ALTER TABLE `chat_messages` MODIFY `sender_id` BIGINT UNSIGNED NOT NULL');
        }
    }
};
