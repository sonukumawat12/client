<?php

namespace Modules\HelpGuide\Entities;

use Illuminate\Database\Eloquent\Model;

class CustomerPurchase extends Model
{

    protected $fillable = ["purchase_code", "user_id", "buyer", "amount", "sold_at", "license", "support_amount", "supported_until", "item_id", "item_name", "item_icon"];
    
    public function user()
    {
        return $this->belongsTo('Modules\HelpGuide\Entities\User');
    }

}
