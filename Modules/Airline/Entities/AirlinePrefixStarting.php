<?php

namespace Modules\Airline\Entities;

use App\User;
// use \Module\Airline\Entities\AirlinePrefixStartingMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirlinePrefixStarting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mode_id',
        'value',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mode()
    {
        return $this->belongsTo(AirlinePrefixStartingMode::class, 'mode_id');
    }
}
