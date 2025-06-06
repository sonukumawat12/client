<?php

namespace Modules\DocManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DocManagementLogo extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;
    
    
    protected static $logName = 'doc_management_logos'; 

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Return list of customer group for a business
     *
     * @param $business_id int
     * @param $prepend_none = true (boolean)
     * @param $prepend_all = false (boolean)
     *
     * @return array
     */
    public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false, $type = null)
    {
        if(empty($type)){
            $type = 'customer';
        }
        $all_cg = ContactGroup::where('business_id', $business_id)->where('type', $type);
        $all_cg = $all_cg->pluck('name', 'id');

        //Prepend none
        if ($prepend_none) {
            $all_cg = $all_cg->prepend(__("lang_v1.none"), '');
        }

        //Prepend none
        if ($prepend_all) {
            $all_cg = $all_cg->prepend(__("report.all"), '');
        }
        
        return $all_cg;
    }
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fillable', 'some_other_attribute']);
    }
}
