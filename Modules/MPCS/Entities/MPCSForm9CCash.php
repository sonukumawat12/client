<?php

namespace Modules\MPCS\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MPCSForm9CCash extends Model
{
    use HasFactory;

    protected $table = 'mpcs_form9c_cash';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'from_starting_number',
        'previous_sheet_amount',
    ];

    protected $casts = [
        'date_added' => 'datetime',
    ];
}
