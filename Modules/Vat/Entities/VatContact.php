<?php

namespace Modules\Vat\Entities;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class VatContact extends Model 
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;
    
    protected $table = "vat_contacts";
    
    protected static $logName = 'Contact'; 

    use Notifiable;

    use SoftDeletes;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fillable', 'some_other_attribute']);
    }
    
    public function scopeActive($query)
    {
        return $query->where('active', '1');
    }

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    

    public function scopeOnlySuppliers($query)
    {
        return $query->whereIn('vat_contacts.type', ['supplier', 'both']);
    }
    
    public function scopeOnlyCustomers($query)
    {
        return $query->whereIn('vat_contacts.type', ['customer', 'both']);
    }
    public function scopeOnlyActive($query)
    {
        return $query->where('vat_contacts.active', 1);
    }

    
       /**
     * Get the business that owns the user.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }
    

    public static function contactDropdown($business_id, $prepend_none = true)
    {
       $query = VatContact::where('business_id', $business_id);
       $query->select('id','name as supplier');
        
        $contacts = $query->pluck('supplier', 'id');

        if ($prepend_none) {
            $contacts = $contacts->prepend(__('lang_v1.none'), '');
        }

        return $contacts;
    }
   
  
    public static function suppliersDropdown($business_id, $prepend_none = true, $append_id = true)
    {
        $all_contacts = VatContact::where('business_id', $business_id)
                        ->whereIn('type', ['supplier', 'both']);

        $all_contacts->select('id','name as supplier');

        $suppliers = $all_contacts->pluck('supplier', 'id');

        //Prepend none
        if ($prepend_none) {
            $suppliers = $suppliers->prepend(__('lang_v1.none'), '');
        }

        return $suppliers;
    }
    
  
    public static function customersDropdown($business_id, $prepend_none = true, $append_id = true)
    {
        $all_contacts = VatContact::where('business_id', $business_id)
                        ->whereIn('type', ['customer', 'both'])->onlyActive();

        $all_contacts->select('id', DB::raw("name as customer"));

        $customers = $all_contacts->pluck('customer', 'id');

        //Prepend none
        if ($prepend_none) {
            $customers = $customers->prepend(__('lang_v1.none'), '');
        }

        return $customers;
    }

   
}
