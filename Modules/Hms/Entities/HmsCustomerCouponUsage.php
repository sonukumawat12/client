<?php

namespace Modules\Hms\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Customer;
use Modules\Hms\Entities\HmsCoupon;

class HmsCustomerCouponUsage extends Model
{
    use HasFactory;

    // Specify the table name if it's not following the default Laravel convention
    protected $table = 'hms_customer_coupon_usage';

    // Define the fillable columns (to allow mass assignment)
    protected $fillable = [
        'customer_id',
        'coupon_id',
    ];

    // Define the relationships

    /**
     * Get the customer that owns the coupon usage.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the coupon associated with the usage.
     */
    public function coupon()
    {
        return $this->belongsTo(HmsCoupon::class, 'coupon_id');
    }
}
