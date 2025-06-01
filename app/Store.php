<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\User;

class Store extends Model
{
    use LogsActivity;
    
    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    protected static $logName = 'Store'; 

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fillable', 'some_other_attribute']);
    }

    /**
    * Get the Store dropdown.
    *
    * @return string
    */
    public static function forDropdown($business_id, $enable_petro_module = 0,$check_permissions = 0, $permission = null)
    {
        $user_id = request()->session()->get('user.id');
        $user = User::findOrFail($user_id);
        
        
        if($check_permissions == 0 || (!empty($user->roles->first()) && $user->roles->first()->name == "Admin#$business_id" )){
            $stores = Store::where('business_id', $business_id)->get();
            $dropdown =  $stores->pluck('name', 'id');
        }else{
            $dropdown = UserStorePermission::join('stores', 'stores.id', 'user_store_permissions.store_id')
                        ->where('stores.business_id', $business_id)
                        ->where('user_store_permissions.user_id',$user_id)
                        ->where($permission,1)
                        ->pluck('stores.name','stores.id');
        }
        

        return $dropdown;
    }
    
    public static function getStores($business_id, $check_store_not = 0,$location_id,$permission = null)
    {
        $user_id = request()->session()->get('user.id');
        $user = User::findOrFail($user_id);
        
        
        if((!empty($user->roles->first()) && $user->roles->first()->name == "Admin#$business_id" )){
            if (!empty($check_store_not)) {
                $store = Store::where('business_id', $business_id)->where('status', 1)->where('location_id', $location_id)->where('id', '!=', $check_store_not)->select('id', 'name')->get();
            } else {
                $store = Store::where('business_id', $business_id)->where('status', 1)->where('location_id', $location_id)->select('id', 'name')->get();
            }
            return $store;
        }else{
            if (!empty($check_store_not)) {
                $store = UserStorePermission::join('stores', 'stores.id', 'user_store_permissions.store_id')
                        ->where('stores.business_id', $business_id)
                        ->where('stores.status',1)
                        ->where('stores.location_id', $location_id)
                        ->where('stores.id', '!=', $check_store_not)
                        ->where('user_store_permissions.user_id',$user_id)
                        ->select('stores.name','stores.id');
                if(!empty($permission)){
                    $store->where($permission,1);
                }
            } else {
                $store = UserStorePermission::join('stores', 'stores.id', 'user_store_permissions.store_id')
                        ->where('stores.business_id', $business_id)
                        ->where('stores.status',1)
                        ->where('stores.location_id', $location_id)
                        ->where('user_store_permissions.user_id',$user_id)
                        ->select('stores.name','stores.id');
                        
                        if(!empty($permission)){
                            $store->where($permission,1);
                        }
            }
            return $store->get();

        }
        

        return $dropdown;
    }

    public function business_locations()
    {
        return $this->belongsTo(\App\BusinessLocation::class);
    }
    
    public function variation_qty()
    {
        return $this->belongsToMany(\App\Variation::class, 'variation_store_details', 'store_id', 'product_variation_id');
    }
}
