<?php

namespace Modules\Airline\Entities;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirlinePassengers extends Model
{
    use HasFactory;
     protected $casts = [
         'additional_services' => 'array'
         ];
    protected $fillable = [
        'name',
        'passport_number',
        'airticket_no',
        'frequent_flyer_no',
        'passport_image',
        'airline_itinerary',
        'child',
        'price',
        'invoice_id',
        'passenger_type',
        'expiry_date',
        'additional_services',
        'amount'
    ];

}
