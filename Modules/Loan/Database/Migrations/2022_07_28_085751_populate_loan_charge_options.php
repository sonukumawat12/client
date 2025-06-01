<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Loan\Entities\LoanChargeOption;

class PopulateLoanChargeOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $loan_charge_options = [
            ['id' => '1', 'name' => 'Flat', 'translated_name' => 'Flat', 'active' => '1'],
            ['id' => '2', 'name' => 'Principal due on installment', 'translated_name' => 'Principal due on installment', 'active' => '1'],
            ['id' => '3', 'name' => 'Principal + Interest due on installment', 'translated_name' => 'Principal + Interest due on installment', 'active' => '1'],
            ['id' => '4', 'name' => 'Interest due on installment', 'translated_name' => 'Interest due on installment', 'active' => '1'],
            ['id' => '5', 'name' => 'Total Outstanding Loan Principal', 'translated_name' => 'Total Outstanding Loan Principal', 'active' => '1'],
            ['id' => '6', 'name' => 'Percentage of Original Loan Principal per Installment', 'translated_name' => 'Percentage of Original Loan Principal per Installment', 'active' => '1'],
            ['id' => '7', 'name' => 'Original Loan Principal', 'translated_name' => 'Original Loan Principal', 'active' => '1']
        ];

        if(!LoanChargeOption::where('name', 'Flat')->first()) {
            LoanChargeOption::insert($loan_charge_options);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
