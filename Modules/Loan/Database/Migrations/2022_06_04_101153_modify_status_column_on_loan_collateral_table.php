<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ModifyStatusColumnOnLoanCollateralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `loan_collateral` CHANGE `status` `status` ENUM('deposited_into_branch', 'collateral_with_borrower', 'returned_to_borrower', 'repossession_initiated', 'repossessed', 'under_auction', 'sold', 'lost') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'collateral_with_borrower';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `loan_collateral` CHANGE `status` `status` ENUM('active','repossessed','sold','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active';");
    }
}
