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
        Schema::table('air_ticket_invoices', function (Blueprint $table) {
            $table->time('total_time')->nullable();
            $table->time('transit_time')->nullable();
            $table->text('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('air_ticket_invoices', function (Blueprint $table) {
            $table->dropColumn(['total_time', 'transit_time', 'note']);
        });
    }
};
