<?php

namespace Modules\Airline\Entities;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirlineAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent',
        'user_id',
        'business_id',
        'contact_id',
        'address',
        'mobile_1',
        'mobile_2',
        'land_no',
        'joined_date',
        'opening_balance'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the flights for the AirlineAgent
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function flights()
    {
        return $this->hasMany(AirlineTicketInvoices::class, 'airline_agent');
    }
}
