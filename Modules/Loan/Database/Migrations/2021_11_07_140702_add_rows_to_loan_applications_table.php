<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRowsToLoanApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('loan_applications', [
            'loan_term',
            'repayment_frequency',
            'repayment_frequency_type',
            'is_top_up'
        ])) {
            Schema::table('loan_applications', function (Blueprint $table) {
                $table->integer('loan_term')->nullable();
                $table->integer('repayment_frequency')->nullable();
                $table->string('repayment_frequency_type')->nullable();
                $table->boolean('is_top_up');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn('loan_term');
            $table->dropColumn('repayment_frequency');
            $table->dropColumn('repayment_frequency_type');
            $table->dropColumn('is_top_up');
        });
    }
}
