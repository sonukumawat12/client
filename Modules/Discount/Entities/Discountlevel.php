<?php

namespace Modules\Discount\Entities;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discountlevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_time',
        'sub_category', 'user', 'user_id', 'max_discount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
