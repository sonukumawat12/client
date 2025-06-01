<?php

namespace App;

use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Notifications\Notifiable;
use Modules\Member\Entities\Electrorate;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Notifications\MemberResetPasswordToken;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Member extends Authenticatable 
{
    use LogsActivity;

    use Notifiable;
    use HasRoles;

    protected $guard = 'member';

    protected $hidden = [
        'password', 'remember_token',
    ];
    protected static $logAttributes = ['*'];

    protected static $logFillable = true;
    
    protected $table = "members";
    
    protected static $logName = 'Member'; 

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = ['give_away_gifts' => 'array'];

    /**
     *  Column Feild Name 
     */

    const FIELDS =  [
        'name'=> 'business.name',
        'address'=>'business.address',
        'mobile_number_1'=> 'business.mobile_number_1',
        'mobile_number_2'=> 'business.mobile_number_2',
        'mobile_number_3'=> 'business.mobile_number_3',
        'gender'=> 'business.gender',
        'date_of_birth'=> 'business.date_of_birth',
        'electrorate_id'=> 'member::lang.electrorate',
        'bala_mandalaya_area'=> 'business.bala_mandalaya_area',
        'member_group'=> 'business.member_group',
        'password' =>  'business.password',
        'relation_name' =>  'business.password',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fillable', 'some_other_attribute']);
    }

    public function permitted_locations()
    {
        $user = $this;
        return 'all';
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MemberResetPasswordToken($token));
    }

    /**
     * Get all of the contacts's notes & documents.
     */
    public function documentsAndnote()
    {
        return $this->morphMany('App\DocumentAndNote', 'notable');
    }

    /**
     * Get the Electrorate associated with the Member
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function electrorate()
    {
        return $this->hasOne(Electrorate::class, 'id', 'electrorate_id');
    }

    /**
     * Get all of the family memebers for the Member
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function members()
    {
        return $this->hasMany(Self::class, 'parent_id','id');
    }

    static function storeActivity($record){
        $activity = Activity::create(array(
            'log_name' => 'Member',	
            'description' => $record['type'],	
            'subject_id' => $record['id'],	
            'subject_type' => "App\Member",	
            'causer_id' => auth()->user()->id,	
            'causer_type' => 'App\User',	
            'properties' => $record['attributes'],	
            'created_at' => date('Y-m-d H:i'),	
            'updated_at' => date('Y-m-d H:i'),	
        ));
    } 
}
