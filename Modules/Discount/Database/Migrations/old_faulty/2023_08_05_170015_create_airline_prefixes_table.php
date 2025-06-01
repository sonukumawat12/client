<?php

use Modules\Airline\Entities\AirlinePrefix;
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
        Schema::create('airline_prefixes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('prefix_starting_no');
            $table->timestamps();
        });

        // add dummy data

        for ($i=0; $i < 5; $i++) { 
            AirlinePrefix::create([
                'user_id' => 3,
                'prefix_starting_no' => 'Prefix Starting No ' . $i
            ]);
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airline_prefixes');
    }
};
