<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusChangeDateToLoanCollateralHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_collateral_history', function (Blueprint $table) {
            $table->date('status_change_date')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_collateral_history', function (Blueprint $table) {
            $table->dropColumn('status_change_date');
        });
    }
}
