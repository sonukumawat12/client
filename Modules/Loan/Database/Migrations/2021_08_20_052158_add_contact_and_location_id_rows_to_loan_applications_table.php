<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddContactAndLocationIdRowsToLoanApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `loan_applications` CHANGE `client_id` `contact_id` INT(10) UNSIGNED NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE `loan_applications` CHANGE `branch_id` `location_id` INT(10) UNSIGNED NULL DEFAULT NULL;');
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->index('contact_id');
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `loan_applications` CHANGE `contact_id` `client_id` INT(10) UNSIGNED NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE `loan_applications` CHANGE `location_id` `branch_id` INT(10) UNSIGNED NULL DEFAULT NULL;');
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropIndex('loan_applications_contact_id_index');
            $table->dropIndex('loan_applications_location_id_index');
        });
    }
}