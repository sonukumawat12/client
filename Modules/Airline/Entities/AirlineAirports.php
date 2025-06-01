<?php

namespace Modules\Airline\Entities;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirlineAirports extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_added',
        'country',
        'province',
        'airport_name',
        'status',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
