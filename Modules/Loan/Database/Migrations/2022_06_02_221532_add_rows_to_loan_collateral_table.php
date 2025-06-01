<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRowsToLoanCollateralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_collateral', function (Blueprint $table) {
            $table->string('product_name')->after('status');
            $table->string('registration_date')->after('product_name');
            //optional fields
            $table->string('serial_number')->after('registration_date')->nullable();
            $table->string('model_name')->after('serial_number')->nullable();
            $table->string('model_number')->after('model_name')->nullable();
            $table->string('color')->after('model_number')->nullable();
            $table->string('manufacture_date')->after('color')->nullable();
            $table->string('condition')->after('manufacture_date')->nullable();
            $table->string('address')->after('condition')->nullable();
            //For vehicles only
            $table->string('registration_number')->after('address')->nullable();
            $table->string('mileage')->after('registration_number')->nullable();
            $table->string('engine_number')->after('mileage')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_collateral', function (Blueprint $table) {
            $table->dropColumn('product_name');
            $table->dropColumn('registration_date');
            $table->dropColumn('serial_number');
            $table->dropColumn('model_name');
            $table->dropColumn('model_number');
            $table->dropColumn('color');
            $table->dropColumn('manufacture_date');
            $table->dropColumn('condition');
            $table->dropColumn('address');
            $table->dropColumn('registration_number');
            $table->dropColumn('mileage');
            $table->dropColumn('engine_number');
        });
    }
}
