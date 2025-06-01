<?php

namespace Modules\Airline\Entities;

use App\User;
use \Module\Airline\Entities\AirlinePrefixStarting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirlinePrefixStartingMode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //public function frefix_starting_no()
    public function prefix_starting_no()
    {
        return $this->hasMany(AirlinePrefixStarting::class);
    }
}
