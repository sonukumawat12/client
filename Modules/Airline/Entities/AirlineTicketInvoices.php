<?php

namespace Modules\Airline\Entities;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirlineTicketInvoices extends Model
{
    use HasFactory;
    
    protected $table = 'air_ticket_invoices';

    protected $fillable = [
        'business_id',
        'transaction_id',
        'airticket_no',
        'customer_group',
        'customer',
        'supplier',
        'airline',
        'airline_invoice_no',
        'airline_agent',
        'travel_mode',
        'departure_country',
        'departure_airport',
        'departure_date',
        'departure_time',
        'transit',
        'transit_airport',
        'arrival_country',
        'arrival_airport',
        'arrival_date',
        'arrival_time',
        'total_time',
        'transit_time',
        'note'
    ];


}
