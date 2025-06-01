<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ContactGroup extends Model
{
    use LogsActivity;

    // Set log attributes to all columns or specific ones
    protected static $logAttributes = ['*']; 

    // Optional: Set the log name
    protected static $logName = 'Customer Group'; 

    // Set fillable attributes if necessary
    protected $fillable = ['name', 'business_id', 'type'];

    // Set attributes to be mutated to dates
    protected $dates = ['deleted_at'];

    // Guard attributes
    protected $guarded = ['id'];

    public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false, $type = null)
    {
        if(empty($type)){
            $type = 'customer';
        }
        $all_cg = ContactGroup::where('business_id', $business_id)->where('type', $type);
        $all_cg = $all_cg->pluck('name', 'id');

        // Prepend none
        if ($prepend_none) {
            $all_cg = $all_cg->prepend(__("lang_v1.none"), '');
        }

        // Prepend all
        if ($prepend_all) {
            $all_cg = $all_cg->prepend(__("report.all"), '');
        }
        
        return $all_cg;
    }

    // Set up activity log options
    public function getActivitylogOptions(): LogOptions
    {
        logger("Activitylog options called with: " . json_encode($this->getAttributes()));


        return LogOptions::defaults()
        ->logOnly(['*']);
    }
}
