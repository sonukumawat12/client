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
            $table->boolean('is_next_visit')->default(false);
            $table->dateTime('next_visit_start_date')->nullable()->after('is_next_visit');
            $table->dateTime('next_visit_end_date')->nullable()->after('next_visit_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hms_coupons', function (Blueprint $table) {
            $table->dropColumn(['is_next_visit', 'next_visit_start_date', 'next_visit_end_date']);
        });
    }
};
