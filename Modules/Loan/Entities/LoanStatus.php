<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanStatus extends Model
{
    protected $fillable = [
        'name',
        'parent_status',
        'business_id',
        'active'
    ];

    public function scopeForBusiness($query)
    {
        $query->where('loan_statuses.business_id', session('business.id'));
    }

    public function getParentStatusLabelAttribute()
    {
        return trans('loan::general.'.$this->parent_status);
    }
}
