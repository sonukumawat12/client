<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRepaymentAndLoanTermAndTopUpFieldsToLoanApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->integer('loan_term')->after('loan_product_id');
            $table->integer('repayment_frequency')->after('loan_term');
            $table->string('repayment_frequency_type', 11)->after('repayment_frequency');
            $table->boolean('is_top_up')->default(false)->after('repayment_frequency_type');
        });
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