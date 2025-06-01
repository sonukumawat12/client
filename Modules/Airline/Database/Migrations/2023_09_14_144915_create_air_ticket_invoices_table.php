<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAirTicketInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('air_ticket_invoices', function (Blueprint $table) {
            $table->id();

            $table->timestamps();
            
            $table->string('airticket_no');
            
            $table->string('customer_group');
            
            $table->string('customer');
            
            $table->string('airline');
            
            $table->string('airline_invoice_no');
            
            $table->string('airline_agent');
            
            $table->string('travel_mode');
            
            $table->string('departure_country');
            
            $table->string('departure_airport');
            
            $table->date('departure_date');
            
            $table->string('transit');
            
            $table->string('transit_airport');
            
            $table->string('arrival_country');
            
            $table->string('arrival_airport');
            
            $table->date('arrival_date');
            
            $table->time('arrival_time');
            
 
            
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('air_ticket_invoices');
    }
}

