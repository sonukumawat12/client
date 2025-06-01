<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeAppliedAmountToFourDecimalPlacesInLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `loans` CHANGE `applied_amount` `applied_amount` DECIMAL(65,4) NULL DEFAULT NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `loans` CHANGE `applied_amount` `applied_amount` DECIMAL(65,6) NULL DEFAULT NULL;");
    }
}
