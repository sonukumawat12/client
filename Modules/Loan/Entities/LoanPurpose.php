<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanPurpose extends Model
{
    protected $fillable = [];
    public $table = "loan_purposes";
    public $timestamps = false;

    public function scopeForBusiness($query)
    {
        return $query->where('loan_purposes.business_id', session('business.id'));
    }
}
