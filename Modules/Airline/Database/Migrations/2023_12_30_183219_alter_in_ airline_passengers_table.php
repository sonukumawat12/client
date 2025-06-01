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
        Schema::table('airline_passengers', function (Blueprint $table) {
            $table->enum('passenger_type',['child', 'infant', 'adult'])->nullable();
            $table->date('expiry_date')->nullable();
            $table->longText('additional_services')->nullable();
            $table->float('amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('airline_passengers', function (Blueprint $table) {
            $table->dropColumn(['passenger_type', 'expiry_date', 'additioinal_services', 'amount']);
        });
    }
};
