<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Airline\Entities\AirlinePrefixStartingMode;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('airline_prefix_starting_modes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->timestamps();
        });

        $data = [
            [
                'user_id' => 3,
                'name' => 'Prefix'
            ],
            [
                'user_id' => 3,
                'name' => 'Starting No'
            ]
        ];

        AirlinePrefixStartingMode::upsert($data, ['user_id', 'name']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airline_prefix_starting_modes');
    }
};
