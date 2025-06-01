<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EssentialsEmployeeAdvance extends Model
{
    use HasFactory;

    const PAYMENT_STATUS_NEW = 1;
	const PAYMENT_STATUS_PARTIAL = 2;
	const PAYMENT_STATUS_FULLY_PAID = 3;
	const PAYMENT_STATUS_PENDING = 4;
	const PAYMENT_STATUSES = [
		self::PAYMENT_STATUS_NEW => "New",
		self::PAYMENT_STATUS_PARTIAL => "PatiallyPaid",
		self::PAYMENT_STATUS_FULLY_PAID => "FullyPaid",
		self::PAYMENT_STATUS_PENDING => "Pending",
	];
	
	protected $fillable = ['id','employee_id','payment_type_id','account_id','amount','amount_paid','payment_status','check_no','datetime_entered','reference_no','remarks','salary_period_start','salary_period_end'];
	
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
	
    protected static function newFactory()
    {
        return \Modules\Essentials\Database\factories\EssentialsEmployeeAdvanceFactory::new();
    }
}
