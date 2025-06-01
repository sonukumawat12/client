<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductToDsrSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dsr_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->after('fuel_provider_id')->nullable();
            $table->string('accumulative_sale')->after('product_id')->nullable();
            $table->string('accumulative_purchase')->after('accumulative_sale')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dsr_settings', function (Blueprint $table) {
            $table->dropColumn(['product_id', 'accumulative_sale', 'accumulative_purchase']);
        });
    }
}
