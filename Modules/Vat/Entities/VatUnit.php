<?php

namespace Modules\Vat\Entities;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VatUnit extends Model
{
    use LogsActivity;
    
    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    protected static $logName = 'Unit'; 

    use SoftDeletes;
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
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fillable', 'some_other_attribute']);
    }
    
    public static function forDropdown($business_id)
    {
        $query = VatUnit::where('business_id', $business_id);
        
        $units = $query->select('actual_name as name', 'id')->get();
        $dropdown = $units->pluck('name', 'id');
        $dropdown->prepend(__('messages.please_select'), '');
        return $dropdown;
    }

 
}
