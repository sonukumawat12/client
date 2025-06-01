<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddContactAndLocationIdRowsToLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            DB::statement('ALTER TABLE `loans` CHANGE `client_id` `contact_id` INT(10) UNSIGNED NULL DEFAULT NULL;');
            DB::statement('ALTER TABLE `loans` CHANGE `branch_id` `location_id` INT(10) UNSIGNED NULL DEFAULT NULL;');
            $table->index('contact_id');
            $table->index('location_id');
            $table->dropIndex('loans_client_id_index');
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
            DB::statement('ALTER TABLE `loans` CHANGE `contact_id` `client_id` INT(10) UNSIGNED NULL DEFAULT NULL;');
            DB::statement('ALTER TABLE `loans` CHANGE `location_id` `branch_id` INT(10) UNSIGNED NULL DEFAULT NULL;');
            $table->dropIndex('loans_contact_id_index');
            $table->dropIndex('loans_location_id_index');
            $table->index('client_id');
        });
    }
}