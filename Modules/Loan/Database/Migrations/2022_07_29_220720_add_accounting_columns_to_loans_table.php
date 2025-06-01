<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountingColumnsToLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            // total_outstanding_derived
            $table->enum('accounting_rule', ['none', 'cash', 'accrual_periodic', 'accrual_upfront'])->default('none')->nullable()->after('total_outstanding_derived');
            $table->bigInteger('fund_source_chart_of_account_id')->unsigned()->nullable()->after('accounting_rule');
            $table->bigInteger('loan_portfolio_chart_of_account_id')->unsigned()->nullable()->after('fund_source_chart_of_account_id');
            $table->bigInteger('suspended_income_chart_of_account_id')->unsigned()->nullable()->after('loan_portfolio_chart_of_account_id');
            $table->bigInteger('interest_receivable_chart_of_account_id')->unsigned()->nullable()->after('suspended_income_chart_of_account_id');
            $table->bigInteger('fees_receivable_chart_of_account_id')->unsigned()->nullable()->after('interest_receivable_chart_of_account_id');
            $table->bigInteger('penalties_receivable_chart_of_account_id')->unsigned()->nullable()->after('fees_receivable_chart_of_account_id');
            $table->bigInteger('transfer_in_suspense_chart_of_account_id')->unsigned()->nullable()->after('penalties_receivable_chart_of_account_id');
            $table->bigInteger('income_from_interest_chart_of_account_id')->unsigned()->nullable()->after('transfer_in_suspense_chart_of_account_id');
            $table->bigInteger('income_from_penalties_chart_of_account_id')->unsigned()->nullable()->after('income_from_interest_chart_of_account_id');
            $table->bigInteger('income_from_fees_chart_of_account_id')->unsigned()->nullable()->after('income_from_penalties_chart_of_account_id');
            $table->bigInteger('income_from_recovery_chart_of_account_id')->unsigned()->nullable()->after('income_from_fees_chart_of_account_id');
            $table->bigInteger('losses_written_off_chart_of_account_id')->unsigned()->nullable()->after('income_from_recovery_chart_of_account_id');
            $table->bigInteger('interest_written_off_chart_of_account_id')->unsigned()->nullable()->after('losses_written_off_chart_of_account_id');
            $table->bigInteger('overpayments_chart_of_account_id')->unsigned()->nullable()->after('interest_written_off_chart_of_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('accounting_rule');
            $table->dropColumn('fund_source_chart_of_account_id');
            $table->dropColumn('loan_portfolio_chart_of_account_id');
            $table->dropColumn('suspended_income_chart_of_account_id');
            $table->dropColumn('interest_receivable_chart_of_account_id');
            $table->dropColumn('fees_receivable_chart_of_account_id');
            $table->dropColumn('penalties_receivable_chart_of_account_id');
            $table->dropColumn('transfer_in_suspense_chart_of_account_id');
            $table->dropColumn('income_from_interest_chart_of_account_id');
            $table->dropColumn('income_from_penalties_chart_of_account_id');
            $table->dropColumn('income_from_fees_chart_of_account_id');
            $table->dropColumn('income_from_recovery_chart_of_account_id');

            $table->dropColumn('losses_written_off_chart_of_account_id');
            $table->dropColumn('interest_written_off_chart_of_account_id');
            $table->dropColumn('overpayments_chart_of_account_id');
        });
    }
}
