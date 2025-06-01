<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddLocationIdToLoanTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `loan_transactions` CHANGE `branch_id` `location_id` INT(10) UNSIGNED NULL DEFAULT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `loan_transactions` CHANGE `location_id` `branch_id` INT(10) UNSIGNED NULL DEFAULT NULL;');
    }
}