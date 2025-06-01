<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\User;

class LoanCollateralHistory extends Model
{
    public $table = "loan_collateral_history";
    
    protected $fillable = [
        'loan_collateral_id',
        'updated_by_user_id',
        'status',
        'status_change_date'
    ];

    public function updated_by()
    {
        return $this->hasOne(User::class, 'id', 'updated_by_user_id')->withDefault();
    }
}
