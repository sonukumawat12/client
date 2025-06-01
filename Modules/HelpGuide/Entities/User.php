<?php

namespace Modules\HelpGuide\Entities;

use Modules\HelpGuide\Traits\HasMeta;
use Modules\HelpGuide\Entities\PushNotificationToken;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasFactory, SoftDeletes,HasMeta;

    protected $fillable = [
        'name', 'email', 'password','last_login_at','last_login_ip',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function pushNotificationTokens()
    {
        return $this->hasMany(PushNotificationToken::class);
    }

    public function tickets()
    {
        return $this->hasMany('Modules\HelpGuide\Entities\Ticket');
    }

    public function getTotalTicketsAttribute()
    {
       return $this->hasMany('Modules\HelpGuide\Entities\Ticket')->withoutGlobalScope('own_ticket')->whereUserId($this->id)->count();
    }

    public function socialAccounts()
    {
        return $this->hasMany('Modules\HelpGuide\Entities\SocialAccount');
    }

    public function avatar()
    {
        return $this->avatar;
    }

    public function getAvatarAttribute($avatar){
        if($avatar) return asset($avatar);
        return 'https://s.gravatar.com/avatar/'.md5($this->email).'?s=64&d=mp';
    }

    public function defaultAvatar()
    {
        return "https://s.gravatar.com/avatar/".md5($this->email)."?s=64&d=mp";
    }

    public function isEmployee()
    {
        return true;
        // return $this->hasAnyRole('super_admin','admin','agent','non-restricted_agent');
    }

    public function isAdmin()
    {
        return true;
        // return $this->hasAnyRole('super_admin','admin');
    }

    public function userRole(){
        return 'super_admin';
        // return $this->getRoleNames()->toString();
    }

    public function envatoCustomer()
    {
        return $this->hasOne('Modules\HelpGuide\Entities\SocialAccount')
            ->where('provider', 'envato')->
            select(['provider_username']);
    }

    public function purchaseCount()
    {
        return CustomerPurchase::where('user_id', $this->id)->count();
    }

    public function format(){
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'total_tickets' => $this->total_tickets,
            'locale' => $this->locale,
        ];
    }

    /**
	 * Send the email verification notification.
	 *
	 * @return void
	 */
	public function sendEmailVerificationNotification()
	{
		if((boolean)setting('verify_email', true) === true){
			$this->notify(new VerifyEmail);
		}
	}

    public function getLocale(){
        return $this->locale;
    }
}
