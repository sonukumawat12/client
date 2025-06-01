<?php
namespace Modules\Member\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MemberGroupTransferHistory extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'MemberGroupTransferHistory';
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
    // Member relationship
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id','id');
    }

    // User who transferred
    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by', 'id');
    }
    
    public function transferredFromGroup()
    {
        return $this->belongsTo(MemberGroup::class, 'transferred_from', 'id');
    }

    public function transferredToGroup()
    {
        return $this->belongsTo(MemberGroup::class, 'transferred_to', 'id');
    }

}
