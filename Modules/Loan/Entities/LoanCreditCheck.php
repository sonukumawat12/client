<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanCreditCheck extends Model
{
    protected $fillable = [];
    public $table = "loan_credit_checks";
    
    public function scopeForDropdown($query)
    {
        return $query->select('loan_credit_checks.id', 'loan_credit_checks.translated_name');
    }
}
