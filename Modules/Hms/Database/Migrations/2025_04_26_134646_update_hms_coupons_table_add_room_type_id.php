<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hms_coupons', function (Blueprint $table) {
            // Add the new foreign key column for room_type_id
            $table->json('room_type_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hms_coupons', function (Blueprint $table) {
            // Drop the new room_type_id column
            $table->dropColumn('room_type_id');
        });
    }
};
