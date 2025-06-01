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
        Schema::table('drivers', function (Blueprint $table) {
            $table->unsignedBigInteger('bata_expense_category')->nullable()->after('advance_expense_category');
        });
    }
    
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('bata_expense_category');
        });
    }

};
