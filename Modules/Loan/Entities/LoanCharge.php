<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanCharge extends Model
{
    protected $fillable = [];

    public $table = "loan_charges";

    protected $appends = ['name_with_charge_type'];

    public function charge_type()
    {
        return $this->hasOne(LoanChargeType::class, 'id', 'loan_charge_type_id')->withDefault();
    }

    public function charge_option()
    {
        return $this->hasOne(LoanChargeOption::class, 'id', 'loan_charge_option_id')->withDefault();
    }

    public function getNameWithChargeTypeAttribute()
    {
        return !empty($this->charge_type->name) ?
            $this->name . ' ('.$this->charge_type->name.')' :
            $this->name;
    }

    public function scopeForDropdown($query)
    {
        return $query->select('loan_charges.id', 'loan_charges.name');
    }

    public function scopeNotOverdueChargeType($query)
    {
        return $query->whereHas('charge_type', function ($query) {
            return $query->whereNotIn('loan_charge_types.id', [2, 4, 7]);
        });
    }

    public function scopeIsOverdueChargeType($query)
    {
        return $query->whereHas('charge_type', function ($query) {
            return $query->whereIn('loan_charge_types.id', [2, 4, 7]);
        });
    }
}
