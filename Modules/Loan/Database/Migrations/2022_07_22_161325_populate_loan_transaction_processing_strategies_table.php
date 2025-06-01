<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Loan\Entities\LoanTransactionProcessingStrategy;

class PopulateLoanTransactionProcessingStrategiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $ltps = [
            ['id' => '1', 'name' => 'Penalties, Fees, Interest, Principal order', 'translated_name' => 'Penalties, Fees, Interest, Principal order', 'active' => '1'],
            ['id' => '2', 'name' => 'Principal, Interest, Penalties, Fees Order', 'translated_name' => 'Principal, Interest, Penalties, Fees Order', 'active' => '1'],
            ['id' => '3', 'name' => 'Interest, Principal, Penalties, Fees Order', 'translated_name' => 'Interest, Principal, Penalties, Fees Order', 'active' => '1']
        ];

        if(!LoanTransactionProcessingStrategy::where('name', 'Penalties, Fees, Interest, Principal order')->first()){
            LoanTransactionProcessingStrategy::insert($ltps);
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
