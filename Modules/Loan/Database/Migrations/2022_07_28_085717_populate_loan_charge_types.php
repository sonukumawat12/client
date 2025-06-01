<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Loan\Entities\LoanChargeType;

class PopulateLoanChargeTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $loan_charge_types = [
            ['id' => '1', 'name' => 'Disbursement', 'translated_name' => 'Disbursement', 'active' => '1'],
            ['id' => '2', 'name' => 'Specified Due Date', 'translated_name' => 'Specified Due Date', 'active' => '1'],
            ['id' => '3', 'name' => 'Installment Fees', 'translated_name' => 'Installment Fees', 'active' => '1'],
            ['id' => '4', 'name' => 'Overdue Installment Fee', 'translated_name' => 'Overdue Installment Fee', 'active' => '1'],
            ['id' => '5', 'name' => 'Disbursement - Paid With Repayment', 'translated_name' => 'Disbursement - Paid With Repayment', 'active' => '1'],
            ['id' => '6', 'name' => 'Loan Rescheduling Fee', 'translated_name' => 'Loan Rescheduling Fee', 'active' => '1'],
            ['id' => '7', 'name' => 'Overdue On Loan Maturity', 'translated_name' => 'Overdue On Loan Maturity', 'active' => '1'],
            ['id' => '8', 'name' => 'Last installment fee', 'translated_name' => 'Last installment fee', 'active' => '1']
        ];

        if(!LoanChargeType::where('name', 'Disbursement')->first()){
            LoanChargeType::insert($loan_charge_types);
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
