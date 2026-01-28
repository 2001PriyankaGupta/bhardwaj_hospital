<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'reserved' to the enum values for the status column
        DB::statement("ALTER TABLE `beds` MODIFY COLUMN `status` ENUM('available','occupied','maintenance','cleaning','reserved') NOT NULL DEFAULT 'available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Before removing 'reserved' ensure no rows have that value
        DB::statement("UPDATE `beds` SET `status` = 'available' WHERE `status` = 'reserved'");
        DB::statement("ALTER TABLE `beds` MODIFY COLUMN `status` ENUM('available','occupied','maintenance','cleaning') NOT NULL DEFAULT 'available'");
    }
};
