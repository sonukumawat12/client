<?php

namespace App;

use DB;
use App\Chequer\CancelCheque;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Hash;
use Modules\Airline\Entities\Airline;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Modules\Airline\Entities\AirlineAgent;

use Modules\Airline\Entities\AirlinePrefix;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements JWTSubject
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'User';

    use Notifiable;
    use SoftDeletes;
    use HasRoles;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fillable', 'some_other_attribute']);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = ['give_away_gifts' => 'array'];

    /**
     * Get the business that owns the user.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }
    
    /**
     * The contact the user has access to.
     * Applied only when selected_contacts is true for a user in
     * users table
     */
    public function contactAccess()
    {
        return $this->belongsToMany(\App\Contact::class, 'user_contact_access');
    }

    /**
     * Creates a new user based on the input provided.
     *
     * @return object
     */
    public static function create_user($details)
    {
        $user = User::create([
                    'surname' => $details['surname'],
                    'first_name' => $details['first_name'],
                    'last_name' => $details['last_name'],
                    'username' => $details['username'],
                    'contact_number' => $details['contact_number'] ?? null,
                    'email' => $details['email'] ?? null,
                    'password' => Hash::make($details['password'] ?? 'Not Set'),
                    'language' => !empty($details['language']) ? $details['language'] : 'en',
                ]);
        
        return $user;
    }
    
    public function contact()
    {
        return $this->hasOne(Contact::class);
    }

    /**
     * Gives locations permitted for the logged in user
     *
     * @return string or array
     */
    public function permitted_locations()
    {
        $user = $this;

        if ($user->can('access_all_locations')) {
            return 'all';
        } else {
            $business_id = request()->session()->get('user.business_id');
            $permitted_locations = [];
            $all_locations = BusinessLocation::where('business_id', $business_id)->get();
            foreach ($all_locations as $location) {
                if ($user->can('location.' . $location->id)) {
                    $permitted_locations[] = $location->id;
                }
            }

            return $permitted_locations;
        }
    }

    /**
     * Returns if a user can access the input location
     *
     * @param: int $location_id
     * @return boolean
     */
    public static function can_access_this_location($location_id)
    {
        $permitted_locations = auth()->user()->permitted_locations();

        if ($permitted_locations == 'all' || in_array($location_id, $permitted_locations)) {
            return true;
        }

        return false;
    }

    /**
     * Return list of users dropdown for a business
     *
     * @param $business_id int
     * @param $prepend_none = true (boolean)
     * @param $include_commission_agents = false (boolean)
     *
     * @return array users
     */
    public static function forDropdown($business_id, $prepend_none = true, $include_commission_agents = false, $prepend_all = false)
    {
        $query = User::where('business_id', $business_id);
        if (!$include_commission_agents) {
            $query->where('is_cmmsn_agnt', 0);
        }

        $all_users = $query->select('id', DB::raw("CONCAT(COALESCE(surname, ''),' ',COALESCE(first_name, ''),' ',COALESCE(last_name,'')) as full_name"));

        $users = $all_users->pluck('full_name', 'id');

        //Prepend none
        if ($prepend_none) {
            $users = $users->prepend(__('lang_v1.none'), '');
        }

        //Prepend all
        if ($prepend_all) {
            $users = $users->prepend(__('lang_v1.all'), '');
        }

        return $users;
    }

    /**
    * Return list of sales commission agents dropdown for a business
    *
    * @param $business_id int
    * @param $prepend_none = true (boolean)
    *
    * @return array users
    */
    public static function saleCommissionAgentsDropdown($business_id, $prepend_none = true)
    {
        $all_cmmsn_agnts = User::where('business_id', $business_id)
                        ->where('is_cmmsn_agnt', 1)
                        ->select('id', DB::raw("CONCAT(COALESCE(surname, ''),' ',COALESCE(first_name, ''),' ',COALESCE(last_name,'')) as full_name"));

        $users = $all_cmmsn_agnts->pluck('full_name', 'id');

        //Prepend none
        if ($prepend_none) {
            $users = $users->prepend(__('lang_v1.none'), '');
        }

        return $users;
    }

    /**
     * Return list of users dropdown for a business
     *
     * @param $business_id int
     * @param $prepend_none = true (boolean)
     * @param $prepend_all = false (boolean)
     *
     * @return array users
     */
    public static function allUsersDropdown($business_id, $prepend_none = true, $prepend_all = false)
    {
        $all_users = User::where('business_id', $business_id)
                        ->select('id', DB::raw("CONCAT(COALESCE(surname, ''),' ',COALESCE(first_name, ''),' ',COALESCE(last_name,'')) as full_name"));

        $users = $all_users->pluck('full_name', 'id');

        //Prepend none
        if ($prepend_none) {
            $users = $users->prepend(__('lang_v1.none'), '');
        }

        //Prepend all
        if ($prepend_all) {
            $users = $users->prepend(__('lang_v1.all'), '');
        }

        return $users;
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getUserFullNameAttribute()
    {
        return "{$this->surname} {$this->first_name} {$this->last_name}";
    }

    /**
     * Return true/false based on selected_contact access
     *
     * @return boolean
     */
    public static function isSelectedContacts($user_id)
    {
        $user = User::findOrFail($user_id);

        return (boolean)$user->selected_contacts;
    }

    public function getRoleNameAttribute()
    {
        return explode('#', $this->getRoleNames()[0])[0];
    }

    public function media()
    {
        return $this->morphOne(\App\Media::class, 'model');
    }

    public function getMaxFileUpload()
    {
        $subscription = \Modules\Superadmin\Entities\Subscription::active_subscription($this->business->id);
        $maxFileUpload = 0;
        if ($subscription) {
            $maxFileUpload = $subscription->package->max_disk_size;
        }
        // convert from MB to KB
        return (int)($maxFileUpload * 1024);
    }

    public function getMaxFileUploadSize()
    {
        $userName = $this->username;
        $prescription = $this->folderSize($this->getFolderName('prescription', $userName));
        $pharmacy = $this->folderSize($this->getFolderName('pharmacy', $userName));
        $laboratoryTests = $this->folderSize($this->getFolderName('laboratory-tests', $userName));
        $laboratoryBills = $this->folderSize($this->getFolderName('laboratory-bills', $userName));

        $totalUpload = $prescription + $pharmacy + $laboratoryTests + $laboratoryBills;
        $remainingSize = $this->getMaxFileUpload() - $totalUpload;
        return $remainingSize > 0 ? $remainingSize : 0;
    }

    private function getFolderName($name, $username)
    {
        $folder = "./public/img/{$name}/{$username}";
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        return $folder;
    }

    private function folderSize($dir)
    {
        $size = 0;
        foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : folderSize($each);
        }

        // Convert from Byte to KB
        return (int)($size / 1024);
    }
    public function userAccess()
    {
        return $this->belongsToMany(\App\UserContactAccess::class);
    }

    public function airline()
    {
        return $this->hasMany(Airline::class);
    }
    public function airline_refixes()
    {
        return $this->hasMany(AirlinePrefix::class);
    }
    public function airline_agents()
    {
        return $this->hasMany(AirlineAgent::class);
    }
    
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'first_name' => $this->first_name,
            'customer_group' => $this->customer_group
        ];
    }

    /**
     * Get the setting associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function setting()
    {
        return $this->hasOne(UserSetting::class, 'user_id');
    }

    
    /**
     * Get the user that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cancelCheque()
    {
        return $this->hasMany(CancelCheque::class,'user_id');
    }
}
