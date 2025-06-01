<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Loan\Entities\LoanTransactionType;

class AddLoanWithdrawalTransactionType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        LoanTransactionType::insert([
            ['id' => 14, 'name' => 'Withdrawal', 'translated_name' => 'Withdrawal', 'active' => 1]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        LoanTransactionType::destroy(14);
    }
}
