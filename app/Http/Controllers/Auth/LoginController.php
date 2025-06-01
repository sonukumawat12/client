<?php
namespace App\Http\Controllers\Auth;
use App\User;
use App\UserSetting;
use App\Utils\BusinessUtil;
use Illuminate\Http\Request;
use Litespeed\LSCache\LSCache;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\UsersOPTController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Cache;
use App\Utils\LocationUtil;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil)
    {
        if(auth()->user()){
            $setting = UserSetting::where('user_id',auth()->user()->id)->first();
            if($setting)
            {
                $setting->verification_done = false;
                $setting->verification_attempt_count = 0;
                $setting->save();
            }
        }
        $this->middleware('guest')->except('logout');
        
        $this->businessUtil = $businessUtil;
    }
    /**
     * Change authentication from email to username
     *
     * @return void
     */
    public function username()
    {
        return 'username';
    }
    public function logout()
    {
        $setting = UserSetting::where('user_id',auth()->user()->id)->first();
        if($setting)
        {
            $setting->verification_done = false;
            $setting->verification_attempt_count = 0;
            $setting->save();
        }

        request()->session()->flush();
        LSCache::purge('*');
        \Auth::logout();
        $id = request()->id;
        if(!empty($id)){
            return redirect('/?id='.$id);
        }else{
            return redirect('/');
        }
    }
    /**
     * The user has been authenticated.
     * Check if the business is active or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if (!$user->business->is_active) {
            \Auth::logout();
            return redirect('/login')
              ->with(
                  'status',
                  ['success' => 0, 'msg' => __('lang_v1.business_inactive')]
              );
        } elseif ($user->status != 'active') {
            \Auth::logout();
            return redirect('/login')
              ->with(
                  'status',
                  ['success' => 0, 'msg' => __('lang_v1.user_inactive')]
              );
        }
    }
    protected function redirectTo()
    {
        $user = \Auth::user();
        if($user->is_pump_operator){
            return '/petro/pump-operators/dashboard';
        }
        if (!$user->can('dashboard.data') && $user->can('sell.create')) {
            return '/pos/create';
        }
        return '/home';
    }
	public function login(Request $request)
	{
		$rules = [
            'username'=>'required',
            'password'=>'required',
		];
        
        // Cache user setting and membership status for 5 minutes to reduce repeated lookups
        $user = Cache::remember("user_login_{$request->username}", 300, function () use ($request) {
            return User::with('setting')->where('username', $request->username)->select('id', 'member')->first();
        });
        
        if($user && isset($user->setting) && $user->setting->re_captcha_enabled)
        {
            $rules['g-recaptcha-response']= 'required';
       
        }

        $this->validate($request,$rules);
        $credentials = $request->only('username','password');
		$remember = $request->get('remember');
	
	
		if (Auth::attempt($credentials,$remember)) {
		    $location_resp = LocationUtil::getRequestLocation( $request );
		    if( $location_resp['success'] ){
                LocationUtil::storeUserLocation( $user->id, 'user_login', $location_resp['data'] );
            }
            
            $manage_user_controller = new UsersOPTController(new BusinessUtil());
            if($manage_user_controller->sendOpt())
            {
                return [
                    'status' => true,
                    'step' => 'verify_step', 
                ];
            }
            $previousUrl = Session::get('previousUrl');
            if($previousUrl && strpos($previousUrl, route('shipping.index')) !== false) {
                
                return [
                    'status' => true,
                    'redirect' => $previousUrl,
                    'step' => 'prve_page', 
                ];
            }else{
        //   $users = User::where('username', $request->username)
        //                 ->where('member', 1)
        //                 ->first();
 
                if ($user->member == 1) {
                      return [
                            'status' => true,
                            'redirect' => 'member/home', // Redirect to member home
                            'step' => 'home_page'
                        ];
                }
                else
                {
                  // Default redirect for other users
                    return [
                        'status' => true,
                        'redirect' => '/home', // Redirect to default home
                        'step' => 'home_page'
                    ];  
                    
                }
       
                
                            }			
        }
		else
		{
            return [
                'status' => false,
                'msg' => '“Incorrect Login”. Please check and try again',
            ];
		}
	}
    public function custom(){
        if(Auth::check()){
        }else{
            $this->showLoginForm();
        }
    }


   
}