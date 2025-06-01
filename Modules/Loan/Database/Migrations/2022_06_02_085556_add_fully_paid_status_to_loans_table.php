<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFullyPaidStatusToLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `loans` CHANGE `status` `status` ENUM('pending','approved','active','withdrawn','rejected','closed','rescheduled','written_off','overpaid','submitted', 'fully_paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `loans` CHANGE `status` `status` ENUM('pending','approved','active','withdrawn','rejected','closed','rescheduled','written_off','overpaid','submitted') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted';");
    }
}
