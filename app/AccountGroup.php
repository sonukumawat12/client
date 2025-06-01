<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AccountGroup extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Account Group';

    protected $guarded = ['id'];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fillable', 'some_other_attribute']);
    }


    static function getGroupByName($name, $get_only_id = false)
    {
        $business_id = request()->session()->get('user.business_id');
        $group = AccountGroup::where('business_id', $business_id)->where('name', $name)->first();
        if($get_only_id){
            if(!empty($group)){
                $group = $group->id;
            }else{
                $group = 0;
            }
        }
        return $group;
    }
    static function getAccountByGroupId($id, $include_main = false)
    {
        $business_id = request()->session()->get('user.business_id');
        $accounts = Account::where('business_id', $business_id)->where('asset_type', $id);
        if ($include_main) {
            $accounts = $accounts->get();
        } else {
            $accounts = $accounts->where('is_main_account', 0)->get();
        }

        return $accounts;
    }
    
    public static function createOwnerDrawings($business_id){
        $default_account_grp = DefaultAccountGroup::where('name','Owners Drawings')->where('business_id',$business_id)->first();
        
        $default_equity_id = DefaultAccountType::where('name','Equity')->where('business_id',$business_id)->first();
        $equity_id = AccountType::where('name','Equity')->where('business_id',$business_id)->first()->id ?? null;
        
        if(empty($default_account_grp)){
            $default_account_grp = DefaultAccountGroup::create(['business_id' => $business_id,'name' => 'Owners Drawings','account_type_id' => !empty($default_equity_id) ? $default_equity_id->id : 0]);
        }
        
        $account_grp = AccountGroup::where('name','Owners Drawings')->where('business_id',$business_id)->first();
        if(empty($account_grp)){
            $account_grp = AccountGroup::create(['business_id' => $business_id,'name' => 'Owners Drawings','account_type_id' => $equity_id,'default_account_group_id' => $default_account_grp->id]);
        }
    }

    static function getAccountGroupByAccountId($account_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $account_group = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)->where('accounts.id', $account_id)->select('account_groups.*')->first();

        return $account_group;
    }

}
