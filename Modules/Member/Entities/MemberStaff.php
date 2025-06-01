<?php

namespace Modules\Member\Entities;

use App\User;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\Entities\JobTitle;
use Spatie\Activitylog\Traits\LogsActivity;

class MemberStaff extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'MemberStaff';
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fillable', 'some_other_attribute']);
    }

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the User that owns the MemberStaff
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the Job as Designation associated with the MemberStaff
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function designation()
    {
        return $this->hasOne(JobTitle::class,'id','job_id');
    }
}
