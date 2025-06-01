<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsOverdueColumnToLoanProductLinkedChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_product_linked_charges', function (Blueprint $table) {
            $table->boolean('is_overdue')->after('loan_charge_id')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_product_linked_charges', function (Blueprint $table) {
            $table->dropColumn('is_overdue');
        });
    }
}
