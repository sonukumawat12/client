<?php

namespace Modules\Member\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Suggestion extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Suggestion';

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

    protected static function getStateOfUrgenciesArray()
    {
        return [
            'normal' => 'Normal',
            'medium' => 'Medium',
            'high' => 'High',
        ];
    }
    protected static function getSolutionGivenArray()
    {
        return [
            'solved' => 'Solved',
            'pending' => 'Pending',
            'rejected' => 'Rejected',
        ];
    }

    protected static function getStatusArray()
    {
        return [
            'pending' => 'Pending',
            'On action' => 'On action',
            'completed' => 'Completed',
        ];
    }

    protected static function checkMemberorNot()
    {
        if (Auth::guard('web')->check()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the staff associated with the Suggestion
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function staff()
    {
        return $this->hasOne(MemberStaff::class, 'id','member_staff_id');
    }
}
