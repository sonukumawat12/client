<?php

use Modules\Airline\Entities\AirlineAgent;
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
        Schema::create('airline_agents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('agent');
            $table->timestamps();
        });

        for ($i=0; $i < 5; $i++) { 
            AirlineAgent::create([
                'user_id' => 3,
                'agent' => 'Agent ' . ($i + 1)
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airline_agents');
    }
};
