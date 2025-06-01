<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEssentialsEmployeesAdvancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('essentials_employee_advances', function (Blueprint $table) {
            $table->id();
			$table->foreignId('employee_id')->constrained(table: 'essentials_employees', indexName: 'essential_employee_advance_id');
            $table->double('amount')->default(0);
            $table->double('amount_paid')->default(0);
			$table->integer('payment_status')->default(1);
			$table->dateTime('payment_datetime')->nullable();
			$table->string('check_no')->nullable();
			$table->string('reference_no')->nullable();
			$table->string('remarks')->nullable();
			$table->date('salary_period_start')->nullable();
			$table->date('salary_period_end')->nullable();
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('essentials_employees_advances');
    }
}
