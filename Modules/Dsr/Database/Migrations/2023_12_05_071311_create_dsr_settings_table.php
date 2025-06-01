<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDsrSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dsr_settings', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_time')->default(now());
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('province_id');
            $table->unsignedBigInteger('district_id');
            $table->json('areas');
            $table->unsignedBigInteger('fuel_provider_id');
            $table->string('dealer_number');
            $table->string('dealer_name');
            $table->integer('dsr_starting_number');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('business_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dsr_settings');
    }
}
