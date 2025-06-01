<?php

namespace Modules\Fleet\Entities;

use Illuminate\Database\Eloquent\Model;
use App\OpeningBalance;

class Fleet extends Model
{
    protected $fillable = [];

    protected $guarded  = ['id'];
     public function balanceDetail()
    {
        return $this->hasMany(OpeningBalance::class, 'fleets_id', 'id');
    }
}
