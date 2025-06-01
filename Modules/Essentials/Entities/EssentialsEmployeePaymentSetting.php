<?php

namespace Modules\Essentials\Entities;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EssentialsEmployeePaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = ['id','liability_account_id','name','status','employee_ledger','datetime_entered','user_id','remarks','business_id','created_at','updated_at'];
    
	public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
	
    protected static function newFactory()
    {
        return \Modules\Essentials\Database\factories\EssentialsEmployeePaymentSettingFactory::new();
    }
}
