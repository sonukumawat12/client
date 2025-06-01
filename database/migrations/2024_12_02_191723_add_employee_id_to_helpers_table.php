<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        Schema::table('helpers', function (Blueprint $table) {
            // Adding the foreign key column
            $table->unsignedBigInteger('employee_id')->nullable(); // Nullable in case some helpers don't have employees associated

            // Setting up the foreign key relationship
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('helpers', function (Blueprint $table) {
            // Dropping the foreign key and column if rolling back the migration
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
        });
    }
};
