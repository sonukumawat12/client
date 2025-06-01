<?php

namespace Modules\Loan\Entities;

use App\Product as AppProduct;
use App\User;

class Product extends AppProduct
{
    protected $fillable = [];

    public function approval_officers()
    {
        return $this->belongsToMany(User::class, 'loan_product_approval_officers', 'product_id', 'user_id');
    }

    public function charges()
    {
        return $this->hasMany(LoanProductLinkedCharge::class, 'loan_product_id', 'id');
    }

    public function scopeForBusiness($query)
    {
        return $query->where('products.business_id', session('business.id'));
    }
}
