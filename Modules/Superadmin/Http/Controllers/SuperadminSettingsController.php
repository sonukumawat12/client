<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Account;
use App\AccountGroup;
use App\AccountType;
use App\Category;
use App\Currency;
use App\ExpenseCategory;
use App\ExpenseCategoryCode;
use App\Product;
use App\Contact;
use App\Transaction;
use DateTimeZone;
use App\Ad;
use App\AdPage;
use App\AdPageSlot;
use App\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use Modules\Petro\Entities\FuelTank;
use App\Business;
use App\BusinessLocation;
use App\DefaultAccount;
use App\DefaultProductCategory;
use App\DefaultExpenseCategory;
use App\DefaultAccountType;
use App\DefaultAccountGroup;
use App\Scopes\HrSettingScope;
use App\System;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Modules\Visitor\Entities\VisitorSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Modules\HR\Entities\HrPrefix;
use Modules\HR\Entities\HrSetting;
use Modules\HR\Entities\Tax;
use Modules\HR\Entities\WorkingDay;
use Intervention\Image\Facades\Image;
use Modules\Superadmin\Entities\TankDipChart;
use Yajra\DataTables\Facades\DataTables;
use Modules\Superadmin\Entities\HelpExplanation;
use App\Vehicle;
use App\FuelType;
use App\VehicleCategory;
use App\VehicleClassification;
use App\VehicleFuelQuota;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Spatie\ImageOptimizer\OptimizerChainFactory;



class SuperadminSettingsController extends BaseController
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $commonUtil;
    protected $mailDrivers;
    protected $backupDisk;

    public function __construct(BusinessUtil $businessUtil, Util $commonUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;

        $this->mailDrivers = [
            'smtp' => 'SMTP',
            'sendmail' => 'Sendmail',
            'mailgun' => 'Mailgun',
            'mandrill' => 'Mandrill',
            'ses' => 'SES',
            'sparkpost' => 'Sparkpost'
        ];

        $this->backupDisk = ['local' => 'Local', 'dropbox' => 'Dropbox','google' => 'Google'];
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
     private function fuelReFillingCycle (){
        return [
            '0'=>'No Limit' ,
            '1' => '1 Hrs'
            ,'2' => '2 Hrs'
            ,'3' => '3 Hrs'
            ,'4' => '4 Hrs'
            ,'5' => '5 Hrs'
            ,'6' => '6 Hrs'
            ,'7' => '7 Hrs'
            ,'8' => '8 Hrs'
            ,'9' => '9 Hrs'
            ,'10' => '10 Hrs'
            ,'11' => '11 Hrs'
            ,'12' => '12 Hrs'
        ];
    }
    
    
    public function pages()
    {
        $pages = DB::table('pages')->get()->groupBy('page_name');
        $settings = Setting::first();
        $config = DB::table('config')->get();
        $allPages = array_keys($pages->toArray());
        return view('superadmin::superadmin_settings.landing_page.pages', compact('allPages', 'settings', 'config'));
    }
    
    public function landing_languages()
    {
        if (request()->ajax()) {
            $languages = DB::table('languages')->get();
            
            $table =  Datatables::of($languages)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                       
                        $html .= '<li><a href="#" data-href="' . action('SellController@editShipping', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-truck" aria-hidden="true"></i>' . __("lang_v1.edit_shipping") . '</a></li>';
                        $html .= '</ul></div>';
                        return $html;
                    }
                )
                ->editColumn('active', function ($row) {
                    if($row->active == 1){
                        $html = '<input class="input-icheck lang-check" data-id="'.$row->id.'" checked="checked" type="checkbox" value="1" style="position: absolute; ">';
                    }else{
                        $html = '<input class="input-icheck lang-check" data-id="'.$row->id.'" type="checkbox" value="1" style="position: absolute;">';
                    }
                    
                    return $html;
                    
                });
            
            return $table->rawColumns(['active','action'])->make(true);
            
        }
        
        return view('superadmin::superadmin_settings.landing_page.languages');
    }
    
    public function landingSettings(){
        $data = DB::table('site_settings')->first()->landingPage_settings;
        $data = json_decode($data,true);
        return view('superadmin::superadmin_settings.landing_page.settings', compact('data'));
    }
    
    public function landAdminSettings(){
        $timezonelist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $currencies = Currency::get();
        if (tenant()) {
            // Tenant context: default connection assumed to be tenant DB
            $settings = Setting::first();
            $config = DB::table('config')->get();
        } else {
            // Base system: use central connection explicitly
            $settings = Setting::on('mysql')->first();
            $config = DB::connection('mysql')->table('config')->get();
        }

        $app_name = System::getProperty('app_name');

        $email_configuration = [
            'driver' => env('MAIL_MAILER', 'smtp'),
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'address' => env('MAIL_FROM_ADDRESS'),
            'name' => env('MAIL_FROM_NAME', $settings->site_name),
        ];

        $google_configuration = [
            'GOOGLE_ENABLE' => env('GOOGLE_ENABLE', ''),
            'GOOGLE_CLIENT_ID' => env('GOOGLE_CLIENT_ID', ''),
            'GOOGLE_CLIENT_SECRET' => env('GOOGLE_CLIENT_SECRET', ''),
            'GOOGLE_REDIRECT' => env('GOOGLE_REDIRECT', ''),
            'GOOGLE_ADSENSE_CODE' => env('GOOGLE_ADSENSE_CODE')
        ];

        $image_limit = [
            'SIZE_LIMIT' => env('SIZE_LIMIT', '')
        ];

        $recaptcha_configuration = [
            'RECAPTCHA_ENABLE' => env('RECAPTCHA_ENABLE', ''),
            'RECAPTCHA_SITE_KEY' => env('RECAPTCHA_SITE_KEY', ''),
            'RECAPTCHA_SECRET_KEY' => env('RECAPTCHA_SECRET_KEY', '')
        ];
        
        $settings['email_configuration'] = $email_configuration;
        $settings['google_configuration'] = $google_configuration;
        $settings['recaptcha_configuration'] = $recaptcha_configuration;
        $settings['image_limit'] = $image_limit;

        return view('superadmin::landing_page.settings', compact('app_name','settings', 'timezonelist', 'currencies', 'config'));
    }
    
    protected function changeEnv($data = array())
    {
        if (count($data) > 0) {

            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');

            // Split string on every " " and write into array
            $env = preg_split('/\r?\n/', $env);

            // Loop through given data
            foreach ((array) $data as $key => $value) {

                // Loop through .env-data
                foreach ($env as $env_key => $env_value) {

                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if ($entry[0] == $key) {
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }

            // Turn the array back to an String
            $env = implode("\n", $env);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);

            return true;
        } else {
            return false;
        }
    }
    
    public function changeSettings(Request $request)
    {
        
        if ($request->site_logo == null && $request->favi_icon == null && $request->primary_image == null && $request->secondary_image == null && $request->register_image == null) {
            if (tenant()) {
                Setting::where('id', '1')->update([
                    'google_key' => $request->google_key, 'google_analytics_id' => $request->google_analytics_id,
                    'site_name' => $request->site_name, 'seo_meta_description' => $request->seo_meta_desc, 'seo_keywords' => $request->meta_keywords,
                    'tawk_chat_bot_key' => $request->tawk_chat_bot_key
                ]);
            }else{
                Setting::on('mysql')->where('id', '1')->update([
                    'google_key' => $request->google_key, 'google_analytics_id' => $request->google_analytics_id,
                    'site_name' => $request->site_name, 'seo_meta_description' => $request->seo_meta_desc, 'seo_keywords' => $request->meta_keywords,
                    'tawk_chat_bot_key' => $request->tawk_chat_bot_key
                ]);
            }

            $double_site_name = str_replace('"', '', trim($request->site_name, '"'));
            $space_name = str_replace("'", '', trim($double_site_name, "'"));
            $site_name = str_replace(" ", '', trim($space_name, " "));

            DB::table('config')->where('config_key', 'site_name')->update([
                'config_value' => $site_name,
            ]);

            DB::table('config')->where('config_key', 'timezone')->update([
                'config_value' => $request->timezone,
            ]);

            DB::table('config')->where('config_key', 'currency')->update([
                'config_value' => $request->currency,
            ]);

            DB::table('config')->where('config_key', 'paypal_mode')->update([
                'config_value' => $request->paypal_mode,
            ]);

            DB::table('config')->where('config_key', 'paypal_client_id')->update([
                'config_value' => $request->paypal_client_key,
            ]);

            DB::table('config')->where('config_key', 'paypal_secret')->update([
                'config_value' => $request->paypal_secret,
            ]);

            DB::table('config')->where('config_key', 'razorpay_key')->update([
                'config_value' => $request->razorpay_client_key,
            ]);

            DB::table('config')->where('config_key', 'razorpay_secret')->update([
                'config_value' => $request->razorpay_secret,
            ]);

            DB::table('config')->where('config_key', 'term')->update([
                'config_value' => $request->term,
            ]);

            DB::table('config')->where('config_key', 'stripe_publishable_key')->update([
                'config_value' => $request->stripe_publishable_key,
            ]);

            DB::table('config')->where('config_key', 'stripe_secret')->update([
                'config_value' => $request->stripe_secret,
            ]);

            DB::table('config')->where('config_key', 'app_theme')->update([
                'config_value' => $request->app_theme,
            ]);

            $data = [
                'config_value' => $request->share_content,
            ];
            
            if (tenant()) {
                // Runs on tenant DB (default connection assumed to be tenant DB)
                DB::table('config')->where('config_key', 'share_content')->update($data);
            } else {
                // Runs on base system DB (explicit connection: 'central')
                DB::connection('mysql')->table('config')->where('config_key', 'share_content')->update($data);
            }

            DB::table('config')->where('config_key', 'bank_transfer')->update([
                'config_value' => $request->bank_transfer,
            ]);

            DB::table('config')->where('config_key', 'payhere_merchant_secret')->update([
                'config_value' => $request->payhere_merchant_secret,
            ]);

            DB::table('config')->where('config_key', 'payhere_merchant_id')->update([
                'config_value' => $request->payhere_merchant_id,
            ]);

            $app_name = str_replace('"', '', $request->app_name);
            $app_name = str_replace(' ', '', $app_name);
            
            if (tenant()) {
                // Setting::updateOrCreate(
                //     ['id' => '1'],
                //     ['app_name' => $app_name]
                // );
                System::updateOrCreate(
                    ['key' => 'app_name'],
                    ['value' => $app_name]
                );
            }
            
            $image_limit = str_replace('"', '', $request->image_limit);
            $recaptcha_enable = str_replace('"', '', $request->recaptcha_enable);
            $recaptcha_site_key = str_replace('"', '', $request->recaptcha_site_key);
            $recaptcha_secret_key = str_replace('"', '', $request->recaptcha_secret_key);
            $google_adsense_code = str_replace('"', '', $request->google_adsense_code);
            //dd("'".$app_name."'");
            $envData = [
                'TIMEZONE' => $request->timezone,
                'GOOGLE_ENABLE' => $request->google_auth_enable,
                'GOOGLE_CLIENT_ID' => $request->google_client_id,
                'GOOGLE_CLIENT_SECRET' => $request->google_client_secret,
                'GOOGLE_REDIRECT' => $request->google_redirect,
                'SIZE_LIMIT' => $image_limit,
                'RECAPTCHA_ENABLE' => $recaptcha_enable,
                'RECAPTCHA_SITE_KEY' => $recaptcha_site_key,
                'RECAPTCHA_SECRET_KEY' => $recaptcha_secret_key,
                'GOOGLE_ADSENSE_CODE' => $google_adsense_code,
            ];
            
            if (!tenant()) {
                $envData['APP_NAME'] = '"' . $app_name . '"';
            }
            
            $this->changeEnv($envData);
            

            
            return redirect()->back()->with('toast_success', __('Success')) ;;
        }

        if ($request->favi_icon == null && $request->site_logo == null && $request->secondary_image == null && $request->register_image == null) {
            
            // primary image
            
            logger("starting image upload");
            
            $validator = Validator::make($request->all(), [
                'primary_image' => 'mimes:jpeg,png,jpg,gif,svg|max:'.env("SIZE_LIMIT").'',
            ]);
            if ($validator->fails()) {
                return back()->with('errors', $validator->messages()->all()[0])->withInput();
            }
            
            logger("validator passed");

            $primary_image = '/frontend/assets/elements/' . 'IMG-' . time() . '.' . $request->primary_image->extension();
            
            logger("moving image to $primary_image");
            
            $move = $request->primary_image->move(public_path('/public/frontend/assets/elements'), $primary_image);
            
            logger("moved status is $move, now updating DB");
            
            $result = DB::table('config')->where('config_key', 'primary_image')->update([
                'config_value' => $primary_image,
            ]);
            
            logger("DB update status is $result");

            
            return redirect()->back()->with('toast_success', __('Success')) ;;
        } else if ($request->favi_icon == null && $request->site_logo == null && $request->primary_image == null && $request->register_image == null) {
            
            // sec image
            
            $validator = Validator::make($request->all(), [
                'secondary_image' => 'mimes:jpeg,png,jpg,gif,svg|max:'.env("SIZE_LIMIT").'',
            ]);
            if ($validator->fails()) {
                return back()->with('errors', $validator->messages()->all()[0])->withInput();
            }

            $secondary_image = '/frontend/assets/' . 'IMG-' . time() . '.' . $request->secondary_image->extension();
            $request->secondary_image->move(public_path('/public/frontend/assets'), $secondary_image);

            DB::table('config')->where('config_key', 'secondary_image')->update([
                'config_value' => $secondary_image,
            ]);

            
            return redirect()->back()->with('toast_success', __('Success')) ;;
        } else if ($request->primary_image == null && $request->secondary_image == null && $request->site_logo == null && $request->register_image == null) {
            
            // favicon
            
            $validator = Validator::make($request->all(), [
                'favi_icon' => 'mimes:jpeg,png,jpg,gif,svg|max:'.env("SIZE_LIMIT").'',
            ]);
            if ($validator->fails()) {
                return back()->with('errors', $validator->messages()->all()[0])->withInput();
            }

            $favi_icon = '/backend/img/' . 'IMG-' . time() . '.' . $request->favi_icon->extension();
            $request->favi_icon->move(public_path('/public/backend/img'), $favi_icon);

            Setting::where('id', '1')->update([
                'google_key' => $request->google_key, 'google_analytics_id' => $request->google_analytics_id,
                'site_name' => $request->site_name, 'favicon' => $favi_icon, 'seo_meta_description' => $request->seo_meta_desc, 'seo_keywords' => $request->meta_keywords,
                'tawk_chat_bot_key' => $request->tawk_chat_bot_key,
            ]);

            
            return redirect()->back()->with('toast_success', __('Success')) ;;
        } else if ($request->primary_image == null && $request->secondary_image == null && $request->favicon == null && $request->register_image == null) {
            
            // site logo
            
            
            $validator = Validator::make($request->all(), [
                'site_logo' => 'mimes:jpeg,png,jpg,gif,svg|max:'.env("SIZE_LIMIT").'',
            ]);
            if ($validator->fails()) {
                return back()->with('errors', $validator->messages()->all()[0])->withInput();
            }

            $site_logo = '/backend/img/' . 'IMG-' . time() . '.' . $request->site_logo->extension();
            $request->site_logo->move(public_path('/public/backend/img'), $site_logo);

            Setting::where('id', '1')->update([
                'google_key' => $request->google_key, 'google_analytics_id' => $request->google_analytics_id,
                'site_name' => $request->site_name, 'site_logo' => $site_logo, 'seo_meta_description' => $request->seo_meta_desc, 'seo_keywords' => $request->meta_keywords,
                'tawk_chat_bot_key' => $request->tawk_chat_bot_key,
            ]);

            
            return redirect()->back()->with('toast_success', __('Success')) ;;
        } else if ($request->primary_image == null && $request->secondary_image == null && $request->favicon == null && $request->site_logo == null) {
            
            // register image
            
            $validator = Validator::make($request->all(), [
                'register_image' => 'mimes:jpeg,png,jpg,gif,svg|max:'.env("SIZE_LIMIT").'',
            ]);
            if ($validator->fails()) {
                return back()->with('errors', $validator->messages()->all()[0])->withInput();
            }

            $register_image = '/frontend/assets/' . 'IMG-' . time() . '.' . $request->register_image->extension();
            $request->register_image->move(public_path('/public/frontend/assets'), $register_image);

            DB::table('config')->where('config_key', 'register_image')->update([
                'config_value' => $register_image,
            ]);

            
            return redirect()->back()->with('toast_success', __('Success')) ;;
        } else if ($request->primary_image != null && $request->secondary_image != null && $request->favi_icon != null  && $request->site_logo != null && $request->register_image != null) {

            // all images
            
            $site_logo = '/backend/img/' . 'IMG-' . time() . '.' . $request->site_logo->extension();
            
            $favi_icon = '/backend/img/' . 'IMG-' . time() . '.' . $request->favi_icon->extension();
            $secondary_image = '/frontend/assets/' . 'IMG-' . time() . '.' . $request->secondary_image->extension();
            $primary_image = '/frontend/assets/elements/' . 'IMG-' . time() . '.' . $request->primary_image->extension();
            $register_image = '/frontend/assets/elements/' . 'IMG-' . time() . '.' . $request->register_image->extension();
            $request->primary_image->move(public_path('/public/frontend/assets/elements'), $primary_image);
            $request->secondary_image->move(public_path('/public/frontend/assets'), $secondary_image);
            $request->favi_icon->move(public_path('/public/backend/img'), $favi_icon);
            $request->site_logo->move(public_path('/public/backend/img'), $site_logo);
            $request->register_image->move(public_path('/public/frontend/assets'), $register_image);

            Setting::where('id', '1')->update([
                'site_logo' => $site_logo, 'favicon' => $favi_icon, 'seo_image' => $site_logo
            ]);

            DB::table('config')->where('config_key', 'primary_image')->update([
                'config_value' => $primary_image,
            ]);

            DB::table('config')->where('config_key', 'secondary_image')->update([
                'config_value' => $secondary_image,
            ]);
            
            DB::table('config')->where('config_key', 'register_image')->update([
                'config_value' => $register_image,
            ]);

            
            return redirect()->back()->with('toast_success', __('Success')) ;;
        }
    }
    
    public function savelandingSettings(Request $request){
        $data = array();
        $data['about'] = $request->has('about') ? 1 : 0;
        $data['how_it_works'] = $request->has('how_it_works') ? 1 : 0;
        $data['features'] = $request->has('features') ? 1 : 0;
        $data['pricing'] = $request->has('pricing') ? 1 : 0;
        $data['contact'] = $request->has('contact') ? 1 : 0;
        $data['language'] = $request->has('language') ? 1 : 0;
        $data['faq'] = $request->has('faq') ? 1 : 0;
        $data['login'] = $request->has('login') ? 1 : 0;
        $data['signup'] = $request->has('signup') ? 1 : 0;
        
        $update = DB::table('site_settings')
                ->update(['landingPage_settings' => json_encode($data)]);
        
        if($update > 0 ){
            return back()->with('toast_success', __('Success')) ;
        }else{
            return back()->with('toast_error', __('Something went wrong'));
        }  
    }
    
    public function editPage(Request $request, $id)
    {
        $sections = DB::table('pages')->where('page_name', $id)->get();
        $settings = Setting::first();
        $config = DB::table('config')->get();
        return view('superadmin::superadmin_settings.landing_page.edit-page', compact('sections', 'settings', 'config'));
    }
    
    
    public function edit()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $settings = System::pluck('value', 'key');
        $currencies = $this->businessUtil->allCurrencies();
        $business_id = request()->session()->get('business.id');
        $superadmin_version = System::getProperty('superadmin_version');
        $is_demo = env('APP_ENV') == 'demo' ? true : false;

        $default_values = [
            'APP_NAME' => env('APP_NAME'),
            'APP_TITLE' => env('APP_TITLE'),
            'APP_LOCALE' => env('APP_LOCALE'),
            'MAIL_DRIVER' => $is_demo ? null : env('MAIL_DRIVER'),
            'MAIL_HOST' => $is_demo ? null : env('MAIL_HOST'),
            'MAIL_PORT' => $is_demo ? null : env('MAIL_PORT'),
            'MAIL_USERNAME' => $is_demo ? null : env('MAIL_USERNAME'),
            'MAIL_PASSWORD' => $is_demo ? null : env('MAIL_PASSWORD'),
            'MAIL_ENCRYPTION' => $is_demo ? null : env('MAIL_ENCRYPTION'),
            'MAIL_FROM_ADDRESS' => $is_demo ? null : env('MAIL_FROM_ADDRESS'),
            'MAIL_FROM_NAME' => $is_demo ? null : env('MAIL_FROM_NAME'),
            'STRIPE_PUB_KEY' => $is_demo ? null : env('STRIPE_PUB_KEY'),
            'STRIPE_SECRET_KEY' => $is_demo ? null : env('STRIPE_SECRET_KEY'),
            'PAYPAL_MODE' => env('PAYPAL_MODE'),
            'PAYPAL_SANDBOX_API_USERNAME' => $is_demo ? null : env('PAYPAL_SANDBOX_API_USERNAME'),
            'PAYPAL_SANDBOX_API_PASSWORD' => $is_demo ? null : env('PAYPAL_SANDBOX_API_PASSWORD'),
            'PAYPAL_SANDBOX_API_SECRET' => $is_demo ? null : env('PAYPAL_SANDBOX_API_SECRET'),
            'PAYPAL_LIVE_API_USERNAME' => $is_demo ? null : env('PAYPAL_LIVE_API_USERNAME'),
            'PAYPAL_LIVE_API_PASSWORD' => $is_demo ? null : env('PAYPAL_LIVE_API_PASSWORD'),
            'PAYPAL_LIVE_API_SECRET' => $is_demo ? null : env('PAYPAL_LIVE_API_SECRET'),
            'BACKUP_DISK' => env('BACKUP_DISK'),
            'DROPBOX_ACCESS_TOKEN' => $is_demo ? null : env('DROPBOX_ACCESS_TOKEN'),
            'RAZORPAY_KEY_ID' => $is_demo ? null : env('RAZORPAY_KEY_ID'),
            'RAZORPAY_KEY_SECRET'  => $is_demo ? null : env('RAZORPAY_KEY_SECRET'),
            
            'GOOGLE_DRIVE_CLIENT_ID'  => $is_demo ? null : env('GOOGLE_DRIVE_CLIENT_ID'),
            'GOOGLE_DRIVE_CLIENT_SECRET'  => $is_demo ? null : env('GOOGLE_DRIVE_CLIENT_SECRET'),
            'GOOGLE_DRIVE_REFRESH_TOKEN'  => $is_demo ? null : env('GOOGLE_DRIVE_REFRESH_TOKEN'),
            'GOOGLE_FOLDER_NAME'  => $is_demo ? null : env('GOOGLE_FOLDER_NAME'),
            'BACKUP_RETENTION_DAYS'  => $is_demo ? null : env('BACKUP_RETENTION_DAYS'),

            'PESAPAL_CONSUMER_KEY'  => $is_demo ? null : env('PESAPAL_CONSUMER_KEY'),
            'PESAPAL_CONSUMER_SECRET'  => $is_demo ? null : env('PESAPAL_CONSUMER_SECRET'),
            'PESAPAL_LIVE'  => $is_demo ? null : env('PESAPAL_LIVE'),

            'PAYHERE_MERCHANT_ID'  => $is_demo ? null : env('PAYHERE_MERCHANT_ID'),
            'PAYHERE_MERCHANT_SECRET'  => $is_demo ? null : env('PAYHERE_MERCHANT_SECRET'),
            'PAYHERE_LIVE'  => $is_demo ? null : env('PAYHERE_LIVE'),
            'PAY_ONLINE_LIVE'  => $is_demo ? null : env('PAY_ONLINE_LIVE'),
            'PAY_ONLINE_STARTING_NO'  => $is_demo ? null : env('PAY_ONLINE_STARTING_NO'),
            'PAY_ONLINE_STARTING_NO'  => $is_demo ? null : env('PAY_ONLINE_STARTING_NO'),
            'PAY_ONLINE_BANK_NAME'  => $is_demo ? null : env('PAY_ONLINE_BANK_NAME'),
            'PAY_ONLINE_BRANCH_NAME'  => $is_demo ? null : env('PAY_ONLINE_BRANCH_NAME'),
            'PAY_ONLINE_ACCOUNT_NO'  => $is_demo ? null : env('PAY_ONLINE_ACCOUNT_NO'),
            'PAY_ONLINE_ACCOUNT_NAME'  => $is_demo ? null : env('PAY_ONLINE_ACCOUNT_NAME'),
            'PAY_ONLINE_SWIFT_CODE'  => $is_demo ? null : env('PAY_ONLINE_SWIFT_CODE')
        ];
        $mail_drivers = $this->mailDrivers;

        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }
        $backup_disk = $this->backupDisk;

        $cron_job_command = $this->businessUtil->getCronJobCommand();

        $default_account_types = DefaultAccountType::where('business_id', $business_id)
            ->whereNull('parent_account_type_id')
            ->with(['sub_types'])
            ->get();

        $asset_type_ids = json_encode(DefaultAccountType::getAccountTypeIdOfType('Assets', $business_id));

        $default_accounts = DefaultAccount::pluck('name', 'id');
        $payment_types = $this->commonUtil->payment_types();

        $prefixes = HrPrefix::withoutGlobalScope(HrSettingScope::class)->where('business_id', $business_id)->first();
        $taxes = Tax::withoutGlobalScope(HrSettingScope::class)->where('business_id', $business_id)->get();
        $working_days = WorkingDay::withoutGlobalScope(HrSettingScope::class)->where('business_id', $business_id)->where('is_superadmin_default', 1)->get();
        $businesses = Business::where('is_active', 1)->pluck('name', 'id');

        if ($working_days->count() == 0) {
            $days = array(
                'Saturday',
                'Sunday',
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
            );
            foreach ($days as $day) {
                WorkingDay::insert(['business_id' => $business_id, 'days' => $day, 'flag' => 0, 'is_superadmin_default' => 1]);
            }
        }
        $working_days =  WorkingDay::withoutGlobalScope(HrSettingScope::class)->where('business_id', $business_id)->where('is_superadmin_default', 1)->get();

        $permissions['visitors_registration_setting'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'visitors_registration_setting');
        $permissions['visitors_district'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'visitors_district');
        $permissions['visitors_town'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'visitors_town');

        $visitor_settings = VisitorSettings::where('business_id', $business_id)->first();

        $sheet_names = TankDipChart::pluck('sheet_name', 'sheet_name');
        $tank_manufacturers = FuelTank::pluck('tank_manufacturer', 'tank_manufacturer');
        $tank_capacitys = FuelTank::pluck('storage_volume', 'storage_volume');
        
        $mbt_sheet_names = TankDipChart::pluck('sheet_name', 'id');
        $mbt_tank_manufacturers = FuelTank::pluck('tank_manufacturer', 'id');
        $mbt_tank_capacitys = FuelTank::pluck('storage_volume', 'id');

        $ads = Ad::join('ad_pages', 'ad_pages.id','ads.ad_page_id')
        ->join('ad_page_slots', 'ad_page_slots.id','ads.ad_page_slot_id')
        ->select(['ads.*', 'ad_pages.name as ad_page_name','ad_page_slots.slot as ad_page_slot_name'])
        ->orderBy('ads.id', 'ASC')->get();

        $adPageSlot = AdPageSlot::join('ad_pages', 'ad_page_slots.ad_page_id','ad_pages.id')
            ->select(['ad_page_slots.*', 'ad_pages.name as ad_page_name','ad_page_slots.id as ad_page__slot_id'])
            ->orderBy('ad_pages.id', 'ASC')->get();

        $config = DB::table('config')->get();
        $ad_pages = AdPage::get();
        
        
        
        $vehicleCategories = VehicleCategory::pluck('category', 'id');
        $vehicleClassification = VehicleClassification::pluck('classification', 'id');
        $fuelReFillingCycle =  $this->fuelReFillingCycle();
        

        return view('superadmin::superadmin_settings.edit')
            ->with(compact(
                'vehicleCategories','vehicleClassification','fuelReFillingCycle',
                'sheet_names',
                'tank_manufacturers',
                'tank_capacitys',
                
                'mbt_sheet_names',
                'mbt_tank_manufacturers',
                'mbt_tank_capacitys',
                
                'working_days',
                'businesses',
                'prefixes',
                'taxes',
                'settings',
                'visitor_settings',
                'currencies',
                'superadmin_version',
                'mail_drivers',
                'languages',
                'default_values',
                'backup_disk',
                'cron_job_command',
                'default_account_types',
                'asset_type_ids',
                'default_accounts',
                'payment_types',
                'ad_pages',
                'ads',
                'permissions',
                'adPageSlot',
                'default_accounts'
            ));
    }


    public function savePage(Request $request, $id)
    {
        $sections = DB::table('pages')->where('page_name', $id)->get();
        for ($i = 0; $i < count($sections); $i++) {
            $safe_section_content = $request->input('section' . $i);
            DB::table('pages')->where('page_name', $id)->where('id', $sections[$i]->id)->update(['section_content' => $safe_section_content]);
        }
        return back()->with('toast_success', __('Success')) ;
    }
    

     // Edit Ad
     public function editAd(Request $request, $id)
     {
         $ad_id = $request->id;
         $ad_detail = Ad::where('ad_id', $ad_id)->first();
         $ad_pages = DB::table('ad_pages')->get();
        //  $settings = Setting::where('status', 1)->first();
         $ad_page_slots = AdPageSlot::where('ad_page_id', $ad_detail->ad_page_id)->select('slot','id')->get();
         
         if ($ad_detail == null) {
             return view('errors.404');
         } else {
             return view('superadmin::superadmin_settings.edit-ad', compact('ad_detail', 'ad_pages', 'ad_page_slots'));
         }
     }
 
     // Update Ad
     public function updateAd(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'ad_id' => 'required',
             'ad_page_id' => 'required',
             'ad_page_slot_id' => 'required',
             'client_name' => 'required',
             'start_date' => 'required|before_or_equal:end_date',
             'end_date' => 'required',
             'amount' => 'required',
         ]);
 
         $uploadedFile = $request->file('new_content');
         
         if($validator && $uploadedFile != null){
             $validator = Validator::make($request->all(), [               
                 'new_content' =>  'required|image|mimes:jpeg,png,jpg,gif,svg|max:' . env("SIZE_LIMIT") . '',
             ]);
         }
         
         if ($validator->fails()) {
             return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
         }
         
         $ad = Ad::where('ad_id', $request->ad_id)->first();
         
         if ($ad == null) {
             return back()->with('toast_error', __('Advertisement no found'))->withInput();
         }
         
         $updateData = [
             'ad_page_id' => $request->ad_page_id,
             'ad_page_slot_id' => $request->ad_page_slot_id,
             'client_name' => $request->client_name,
             'start_date' => $request->start_date,
             'end_date' => $request->end_date,
             'amount' => $request->amount,
             'status' => $request->status == "on"? 1: 0,
             'link' => $request->link
         ];
         
         if($uploadedFile != null){
             $adPageSlot = AdPageSlot::where('id', $request->ad_page_slot_id)->first();
             
             $content = $this->saveFile($uploadedFile, $ad->code, $adPageSlot->width,$adPageSlot->height);
             
             $updateData["content"] = $content;
         }
         
         Ad::where('ad_id', $request->ad_id)->update($updateData);
         
         //flash()->success('Ad Updated Successfully!');
         return redirect()->route('settings', $request->ad_id)->with('Ad Updated Successfully!');
     }
 
     // Delete Ad
     public function deleteAd(Request $request)
     {
        
         Ad::where('ad_id', $request->query('ad_id'))->delete();
         //flash()->success('Ad Delete Successfully!');
         return redirect()->route('settings')->with('Ad Delete Successfully!');
     } 

    // Save Ad
    public function saveAd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'create_date'=>'required',
            'ad_page_id' => 'required',
            'ad_page_slot_id' => 'required',            
            'client_name' => 'required',
            'start_date' => 'required|before_or_equal:end_date',
            'end_date' => 'required',
            'amount' => 'required',
            'content' =>  'required|image|mimes:jpeg,png,jpg,gif,svg|max:' . env("SIZE_LIMIT") . '',
            'storage_disk' => ['required', Rule::in(['s3', 'local_server']) ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => $validator->messages()->all()[0]
            ]);
        }
        
        $uploadedFile = $request->file('content');
        
        $adPageId = $request->ad_page_id;
        $adPageSlotId = $request->ad_page_slot_id;
        $adPageSlot = AdPageSlot::where('id', $adPageSlotId)->first();
        
        $imageCode = $this->generateCode();
        $content = $this->saveFile($uploadedFile, $imageCode,$adPageSlot->width,$adPageSlot->height, $request->input('storage_type'));
      
        
        $ad = new Ad();
        $ad->timestamps = false;
        $ad->ad_id = uniqid();
        $ad->ad_page_id = $adPageId;
        $ad->ad_page_slot_id = $adPageSlotId;
        $ad->code = $imageCode;
        $ad->client_name = $request->client_name;
        $ad->start_date = $request->start_date;
        $ad->end_date = $request->end_date;
        $ad->amount = $request->amount;
        $ad->created_at = $request->create_date;
        $ad->content = $content;
        $ad->status = $request->status == "on"? 1: 0;
        $ad->link = $request->link;
        $ad->save();
        
        return response()->json([
            'success' => 1,
            'message' => __('Ad added successfully')
        ]);
    }
    private function saveFile($uploadedFile, $code, $maxWidth, $maxHeight, $storageDisk) {
        if (env('AWS_SUPERADMIN_AD_STORAGE_ENABLED') && $storageDisk == 's3') {
            $storage = Storage::disk('s3');
        } else {
            $storage = Storage::disk('local');
        }
        $filePublicUrl = "";
        
        $filename = trim($code.".".$uploadedFile->getClientOriginalExtension());
        
        $resizedFile = self::resizeImage($uploadedFile, $maxWidth, $maxHeight);
        
        $storage->put(
            'ads/'.$filename,
            $resizedFile->stream()
            );
        
        $filePublicUrl = $storage->url('ads/'.$filename);
        
        return $filePublicUrl;
        
    }
        
    private function generateCode(){
        $latestAd = Ad::orderBy('code','desc')->first();
        
        $code = "";
        $currentYear = \Carbon::now()->format('Y');
        $count = 100;
        
        if($latestAd != null){
            $currentCodeData = explode('-', $latestAd->code);
            
            if( count($currentCodeData) > 0){
                $year = $currentCodeData[0];
                
                if($currentYear == $year){
                    $count = ++$currentCodeData[1];
                }
            }
        }
    
        $code = "$currentYear-$count";
        
        return $code;
    }
    
    private function resizeImage($uploadedFile, $maxWidth, $maxHeight){
        $image = Image::make($uploadedFile);
    
        $width  = $image->width();
        $height = $image->height();
        
        if ( $image->width() > $maxWidth) {
            $image->resize($maxWidth, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } 
        
        if ($image->height() > $maxHeight) {
            $image->resize(null, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        
        return $image;
    }

    public function getAdPageSlot(Request $request)
    {
       
        $ad_page_id = $request->ad_page_id;
        $ad_page_slot_id = $request->ad_page_slot_id;
        $result = null;
        
        if(!empty($ad_page_id)) {
            $result = AdPageSlot::where('ad_page_id', $ad_page_id)->select('slot','id','width','height')->get();
        } else if(!empty($ad_page_slot_id)){
            $result = AdPageSlot::where('id', $ad_page_slot_id)->select('slot','id','width','height')->first();
        }
        
        return response()->json([
            'success' => 1,
            'message' => 'Success',
            'data' => $result
        ]);
    }
    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {       
        // dd("adfsafasdfdsf");
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            //Disable .ENV settings in demo
            if (config('app.env') == 'demo') {
                $output = [
                    'success' => 0,
                    'msg' => 'Feature disabled in demo!!'
                ];
                return back()->with('status', $output);
            }

            $system_settings = $request->only([
                'APP_NAME',
                'APP_TITLE',
                'customer_supplier_security_deposit_current_liability_font_size',
                'customer_supplier_security_deposit_current_liability_color',
                'customer_supplier_security_deposit_current_liability_message',
                'not_enalbed_module_user_font_size',
                'not_enalbed_module_user_color',
                'not_enalbed_module_user_message',
                'visitor_welcome_email_subject',
                'visitor_welcome_email_body',
                'customer_welcome_email_subject',
                'customer_welcome_email_body',
                'agent_welcome_email_subject',
                'agent_welcome_email_body',
                'new_subscription_email_subject',
                'new_subscription_email_subject_offline',
                'new_subscription_email_body_offline',
                'new_subscription_email_body',
                'company_starting_number',
                'upload_image_quality',
                'helpdesk_system_url',
                'create_individual_company_package',
                'business_or_entity',
                'company_number_prefix',
                'sms_on_password_change',
                'footer_top_margin',
                'admin_invoice_footer',
                'default_number_of_customers',
                'upload_image_width',
                'upload_image_height',
                'laboratory_prefix',
                'laboratory_code_start_from',
                'pharmacy_prefix',
                'pharmacy_code_start_from',
                'hospital_prefix',
                'hospital_code_start_from',
                'patient_prefix',
                'patient_code_start_from',
                'app_currency_id',
                'invoice_business_name',
                'email',
                'invoice_business_landmark',
                'invoice_business_zip',
                'invoice_business_state',
                'invoice_business_city',
                'invoice_business_country',
                'package_expiry_alert_days',
                'superadmin_register_tc',
                'welcome_email_subject',
                'welcome_email_body',
                'patient_register_success_title',
                'patient_register_success_msg',
                'company_register_success_title',
                'company_register_success_msg',
                'subscription_message_online_success_title',
                'subscription_message_online_success_msg',
                'subscription_message_offline_success_title',
                'subscription_message_offline_success_msg',
                'visitor_register_success_title',
                'visitor_register_success_msg',
                'customer_register_success_title',
                'customer_register_success_msg',
                'member_register_success_title',
                'member_register_success_msg',
                'agent_register_success_title',
                'agent_register_success_msg',
                'login_banner_html',
                'main_page_refresh_interval_minute',
                'app_footer',
                'admin_reports_footer',
                'tax_label_1',
                'tax_number_1',
                'tax_label_2',
                'tax_number_2',
            ]);

            $system_settings['show_give_away_gift_in_register_page'] = !empty($request->show_give_away_gift_in_register_page) ?  json_encode($request->show_give_away_gift_in_register_page) : '[]';
            $system_settings['show_referrals_in_register_page'] = !empty($request->show_referrals_in_register_page) ? json_encode($request->show_referrals_in_register_page) : '[]';
            $system_settings['PAY_ONLINE_CURRENCY_TYPE'] = !empty($request->PAY_ONLINE_CURRENCY_TYPE) ? json_encode($request->PAY_ONLINE_CURRENCY_TYPE) : '[]';

            //Checkboxes
            $checkboxes = [
                'enable_visitor_register_btn_login_page',
                'enable_individual_register_btn_login_page',
                'enable_visitor_welcome_email',
                'enable_customer_welcome_email',
                'enable_admin_login',
                'enable_landing_page',
                'enable_member_login',
                'enable_visitor_login',
                'enable_customer_login',
                'enable_agent_login',
                'enable_employee_login',
                'enable_pricing_btn_login_page',
                'enable_member_register_btn_login_page',
                'enable_patient_register_btn_login_page',
                'enable_register_btn_login_page',
                'enable_agent_register_btn_login_page',
                'enable_lang_btn_login_page',
                'enable_business_based_username',
                'enable_repair_btn_login_page',
                'superadmin_enable_register_tc',
                'allow_email_settings_to_businesses',
                'enable_new_business_registration_notification',
                'enable_new_subscription_notification',
                'enable_welcome_email',
                'customer_secrity_deposit_current_liability_checkbox',
                'supplier_secrity_deposit_current_liability_checkbox',
                'general_message_pump_operator_dashbaord_checkbox',
                'general_message_petro_dashboard_checkbox',
                'general_message_tank_management_checkbox',
                'general_message_pump_management_checkbox',
                'general_message_pumper_management_checkbox',
                'general_message_daily_collection_checkbox',
                'general_message_settlement_checkbox',
                'general_message_list_settlement_checkbox',
                'general_message_dip_management_checkbox',
                'enable_login_banner_image',
                'enable_login_banner_html',
                'enable_inline_tax'
            ];
            $input = $request->input();
            foreach ($checkboxes as $checkbox) {
                $system_settings[$checkbox] = !empty($input[$checkbox]) ? 1 : 0;
            }
            if ($request->enable_customer_login) {
                User::where('is_customer', 1)->update(['status' => 'active']);
            } else {
                User::where('is_customer', 1)->update(['status' => 'inactive']);
            }
            if (!file_exists('./public/img/banners')) {
                mkdir('./public/img/banners', 0777, true);
            }

            //upload banner image
            if ($request->hasfile('login_banner_image')) {
                $file = $request->file('login_banner_image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                Image::make($file->getRealPath())->resize(468, 60)->save('public/img/banners/' . $filename);
                $uploadFileFicon = 'public/img/banners/' . $filename;
                $system_settings['login_banner_image'] = $uploadFileFicon;
            } else {
                $system_settings['login_banner_image'] = null;
            }

            $system_settings['default_payment_accounts'] = !empty($input['default_payment_accounts']) ? json_encode($input['default_payment_accounts']) : null;
            foreach ($system_settings as $key => $setting) {
                System::updateOrCreate(
                    ['key' => $key],
                    ['value' => $setting]
                );
            }

            //change defuat account mapping to all business
            $businesses = Business::select('id')->get();
            foreach ($businesses as $business) {
                $busines_map_account = array();
                foreach ($input['default_payment_accounts'] as $key => $map) {
                    $account_id = Account::where('business_id', $business->id)->where('default_account_id', $map['account'])->first();
                    $busines_map_account[$key]['is_enabled'] = !empty($map['is_enabled']) ? $map['is_enabled'] : 0;
                    $busines_map_account[$key]['account'] = !empty($account_id) ? $account_id->id : 0;
                }
                BusinessLocation::where('business_id', $business->id)
                    ->update(['default_payment_accounts' => json_encode($busines_map_account)]);
            }

            $env_settings =  $request->only([
                'APP_NAME', 'APP_TITLE',
                'APP_LOCALE', 'MAIL_DRIVER', 'MAIL_HOST', 'MAIL_PORT',
                'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_ENCRYPTION',
                'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME', 'STRIPE_PUB_KEY',
                'STRIPE_SECRET_KEY', 'PAYPAL_MODE',
                'PAYPAL_SANDBOX_API_USERNAME',
                'PAYPAL_SANDBOX_API_PASSWORD',
                'PAYPAL_SANDBOX_API_SECRET', 'PAYPAL_LIVE_API_USERNAME',
                'PAYPAL_LIVE_API_PASSWORD', 'PAYPAL_LIVE_API_SECRET',
                'BACKUP_DISK', 'DROPBOX_ACCESS_TOKEN','GOOGLE_DRIVE_CLIENT_ID','GOOGLE_DRIVE_CLIENT_SECRET','GOOGLE_DRIVE_REFRESH_TOKEN','GOOGLE_FOLDER_NAME','BACKUP_RETENTION_DAYS',
                'RAZORPAY_KEY_ID', 'RAZORPAY_KEY_SECRET',
                'PESAPAL_CONSUMER_KEY', 'PESAPAL_CONSUMER_SECRET', 'PESAPAL_LIVE',
                'PAYHERE_MERCHANT_ID', 'PAYHERE_MERCHANT_SECRET', 'PAYHERE_LIVE',
                'PAY_ONLINE_LIVE', 'PAY_ONLINE_STARTING_NO', 'PAY_ONLINE_BANK_NAME',
                'PAY_ONLINE_BRANCH_NAME', 'PAY_ONLINE_ACCOUNT_NO', 'PAY_ONLINE_ACCOUNT_NAME', 'PAY_ONLINE_SWIFT_CODE'
            ]);

            $found_envs = [];
            $env_path = base_path('.env');
            $env_lines = file($env_path);
            foreach ($env_settings as $index => $value) {
                foreach ($env_lines as $key => $line) {
                    //Check if present then replace it.
                    if (strpos($line, $index) !== false) {
                        $env_lines[$key] = $index . '="' . $value . '"' . PHP_EOL;

                        $found_envs[] = $index;
                    }
                }
            }

            //Add the missing env settings
            $missing_envs = array_diff(array_keys($env_settings), $found_envs);
            if (!empty($missing_envs)) {
                $missing_envs = array_values($missing_envs);
                foreach ($missing_envs as $k => $key) {
                    if ($k == 0) {
                        $env_lines[] = PHP_EOL . $key . '="' . $env_settings[$key] . '"' . PHP_EOL;
                    } else {
                        $env_lines[] = $key . '="' . $env_settings[$key] . '"' . PHP_EOL;
                    }
                }
            }

            $env_content = implode('', $env_lines);
            $output = ['success' => 0, 'msg' => 'Some setting could not be saved, make sure .env file has 644 permission & owned by www-data user'];
            if (is_writable($env_path) && file_put_contents($env_path, $env_content)) {
                $output = [
                    'success' => 1,
                    'msg' => __('lang_v1.success')
                ];
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()
            ->action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@edit')
            ->with('status', $output);
    }

    public function saveAdSlot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slot'=>'required',
            'slot_no' => 'required',
            'ad_page_id' => 'required',            
            'width' => 'required',
            'height' => 'required|before_or_equal:end_date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => $validator->messages()->all()[0]
            ]);
        }

        $ad_page_slot = new AdPageSlot();
        $ad_page_slot->slot = $request->input('slot');
        $ad_page_slot->slot_no = $request->input('slot_no');
        $ad_page_slot->ad_page_id = $request->input('ad_page_id');
        $ad_page_slot->width = $request->input('width');
        $ad_page_slot->height = $request->input('height');
        $ad_page_slot->save();
        
        return response()->json([
            'success' => 1,
            'message' => __('Ad added successfully')
        ]);
    }
    
    public function storeProductCategory(Request $request)
    {
        if (!auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id = session()->get('user.business_id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');

        if ($account_access) {
            if ($request->add_related_account == 'category_level') {
                $validator = Validator::make($request->all(), [
                    'cogs_account_id' => 'required',
                    'sales_income_account_id' => 'required',
                    'add_related_account' => 'required'
                ]);

                if ($validator->fails()) {
                    $output = [
                        'success' => 0,
                        'msg' => $validator->errors()->all()[0]
                    ];
                    return $output;
                }
            }
        }

        try {
            $input = $request->only(['price_reduction_acc','price_increment_acc','remaining_stock_adjusts','name', 'short_code', 'add_related_account', 'cogs_account_id', 'sales_income_account_id']);
            if (!empty($request->input('add_as_sub_cat')) &&  $request->input('add_as_sub_cat') == 1 && !empty($request->input('parent_id'))) {
                $input['parent_id'] = $request->input('parent_id');
            } else {
                $input['parent_id'] = 0;
            }
            $business_id = $request->session()->get('user.business_id');
            $input['weight_excess_loss_applicable'] = !empty($request->weight_excess_loss_applicable) ? 1 : 0;
            $input['weight_loss_expense_account_id'] = $request->weight_loss_expense_account_id;
            $input['weight_excess_income_account_id'] = $request->weight_excess_income_account_id;
            $input['created_by'] = $request->session()->get('user.id');
            $input['business_id'] = $business_id;
            $businesses = Business::get();
            $defaultCategory = DefaultProductCategory::create($input);
            foreach($businesses as $business) {
                $input['business_id'] = $business->id;
                $input['default_product_category_id'] = $defaultCategory->id;
                $category_exist = Category::where('business_id', $business->id)->where('name',  $defaultCategory->name)->first();
                if (empty($category_exist)) {
                    $cogs_acc = Account::where('business_id', $business->id)->where('default_account_id', $defaultCategory->cogs_account_id)->first();
                    $income_acc = Account::where('business_id', $business->id)->where('default_account_id', $defaultCategory->sales_income_account_id)->first();
                    
                    $input['sales_income_account_id'] = !empty($income_acc) ? $income_acc->id : 0;
                    $input['cogs_account_id'] = !empty($cogs_acc) ? $cogs_acc->id : 0;
                    
                    Category::create($input);
                }
            }
            
            $output = [
                'success' => true,
                'data' => $defaultCategory,
                'msg' => __("category.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }
    
    public function addProductCategory()
    {
        $business_id = session()->get('user.business_id');
        $businesses = Business::where('id', '!=', $business_id)->pluck('name', 'id');
        $cogs_group_id = DefaultAccountGroup::where('name','COGS Account Group')->where('business_id',$business_id)->first();
        $sale_income_group_id = DefaultAccountGroup::where('name','Sales Income Group')->where('business_id',$business_id)->first();
      
        $expense = DefaultProductCategory::where('business_id',$business_id)->get()->last();
        if(!empty($expense)){
            $code = explode('-',$expense->code);
            
            $expcode = str_pad((((int) $code[sizeof($code)-1])+1), 4, '0', STR_PAD_LEFT);
        }else{
            $expcode = $expcode = str_pad(1, 4, '0', STR_PAD_LEFT);
        }
        
        $expense_account_type_id = DefaultAccountType::where('name','Expenses')->where('business_id',$business_id)->first()->id ?? null;
        $income_type_id = DefaultAccountType::where('name','Income')->where('business_id',$business_id)->first()->id ?? null;
        $cogs_accounts = [];
        if (!empty($cogs_group_id)) {
            $cogs_accounts = DefaultAccount::where('asset_type',$cogs_group_id->id)->where('business_id',$business_id)->pluck('name', 'id');;
        }
        $sale_income_accounts = [];
        if (!empty($sale_income_group_id)) {
            $sale_income_accounts = DefaultAccount::where('asset_type',$sale_income_group_id->id)->where('business_id',$business_id)->pluck('name', 'id');;
        }
        $expense_accounts = [];
        if (!empty($expense_account_type_id)) {
            $expense_accounts = DefaultAccount::where('account_type_id', $expense_account_type_id)->where('business_id',$business_id)->pluck('name', 'id');
        }
        $income_accounts = [];
        if (!empty($income_type_id)) {
            $income_accounts = DefaultAccount::where('account_type_id', $income_type_id)->where('business_id',$business_id)->pluck('name', 'id');
        }

        $categories = Category::where('parent_id', 0)
            ->select(['name', 'short_code', 'id'])
            ->get();
        $parent_categories = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $parent_categories[$category->id] = $category->name;
            }
        }

        $help_explanations = HelpExplanation::pluck('value', 'help_key');

        return view('superadmin::superadmin_settings.product_expense.create_product')
            ->with(compact('parent_categories', 'cogs_accounts', 'sale_income_accounts', 'expense_accounts', 'income_accounts', 'help_explanations', 'businesses','expcode'));
    }
    
    public function getProductCategories()
    {
        if (request()->ajax()) {
            $category = DefaultProductCategory::leftjoin('accounts as cogs_account', 'default_product_categories.cogs_account_id', 'cogs_account.id')
                ->leftjoin('accounts as sale_account', 'default_product_categories.sales_income_account_id', 'sale_account.id')
                
                ->leftjoin('accounts as decr_account', 'default_product_categories.price_reduction_acc', 'decr_account.id')
                ->leftjoin('accounts as incr_account', 'default_product_categories.price_increment_acc', 'incr_account.id')
                ->select('default_product_categories.name', 'short_code', 'default_product_categories.id', 'parent_id', 'cogs_account.name as cogs', 'sale_account.name as sales_accounts','decr_account.name as decr_accounts','incr_account.name as incr_accounts','default_product_categories.remaining_stock_adjusts');
            $category = $category->get()->sortBy('parent_id');
            return Datatables::of($category)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '';
                        if ($row->name != "Fuel") {
                            $html .= '<button data-href="' . action("CategoryController@edit", [$row->id]) . '" class="btn btn-xs btn-primary edit_category_button"><i class="glyphicon glyphicon-edit"></i>  ' . __("messages.edit") . '</button> &nbsp;';

                            $html .= '<button data-href="' . action("CategoryController@destroy", [$row->id]) . '" class="btn btn-xs btn-danger delete_category_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</button>';

                        }
                        return $html;
                    }

                )
                ->addColumn('category_name', function ($row) {
                    if ($row->parent_id == 0) {
                        return $row->name;
                    } else {
                         // @eng START 11/2 1335
                        $parent = Category::where('id', $row->parent_id)->first();
                        if($parent) {
                            return $parent->name;
                            
                        }
                        return ''; 
                        // return Category::where('id', $row->parent_id)->first()->name;
                        // @eng END 11/2 1335
                    }
                })
                ->addColumn('category_short_code', function ($row) {
                    if ($row->parent_id == 0) {
                        return $row->short_code;
                    } else {
                        // @eng START 11/2 1335
                        $parent = Category::where('id', $row->parent_id)->first(); 
                        if($parent) return $parent->short_code;
                        return '';
                        // return Category::where('id', $row->parent_id)->first()->short_code;
                        // @eng END 11/2 1335
                    }
                })
                ->addColumn('sub_category_name', function ($row) {
                    if ($row->parent_id != 0) {
                        return $row->name;
                    } else {
                        return '';
                    }
                })
                ->addColumn('sub_category_short_code', function ($row) {
                    if ($row->parent_id != 0) {
                        return $row->short_code;
                    } else {
                        return '';
                    }
                })
                ->removeColumn('id')
                ->removeColumn('parent_id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    
    public function addExpenseCategory()
    {
        $business_id = session()->get('user.business_id');
        $businesses = Business::where('id', '!=', $business_id)->pluck('name', 'id');
        $expense = DefaultExpenseCategory::where('business_id',$business_id)->get()->last();
        if(!empty($expense)){
            $code = explode('-',$expense->code);
            
            $expcode = str_pad((((int) $code[sizeof($code)-1])+1), 4, '0', STR_PAD_LEFT);
        }else{
            $expcode = $expcode = str_pad(1, 4, '0', STR_PAD_LEFT);
        }
        
        
        $expense_account_type_id = DefaultAccountType::where('business_id', $business_id)->where('name', 'Expenses')->first();
        $expense_accounts = [];
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        $expense_account_id = null;
        if ($account_access) {
            if (!empty($expense_account_type_id)) {
                $expense_accounts = DefaultAccount::where('business_id', $business_id)->where('account_type_id', $expense_account_type_id->id)->pluck('name', 'id');
            }
        } else {
            $expense_account_id = DefaultAccount::where('name', 'Expenses')->where('business_id', $business_id)->first()->id;
            $expense_accounts = DefaultAccount::where('name', 'Expenses')->where('business_id', $business_id)->pluck('name', 'id');
        }

        $expense_categories = DefaultExpenseCategory::where('business_id', $business_id)->pluck('name', 'id');
        $quick_add = request()->quick_add ? 1 : 0;

        $payees = Contact::where('business_id', $business_id)->pluck('name', 'id');
        $payees[''] = 'No Payee';
        //dd($payees);

        return view('superadmin::superadmin_settings.product_expense.create_expense')
            ->with(compact('expense_accounts', 'account_access', 'expense_account_id', 'quick_add', 'expense_categories', 'payees', 'businesses','expcode'));
    }
    
     public function storeExpenseCategory(Request $request)
    {
        try {
//            $validator = Validator::make($request->all(), [
//                'name' => 'required|string',
//                'code' => 'required|string',
//                'expense_account' => 'required|string',
//                'payee_id' => 'required|string',
//                'parent_id' => 'nullable|string',
//            ]);
//
//            if ($validator->fails()) {
//                return [
//                    'success' => false,
//                    'msg' => __("messages.something_went_wrong")
//                ];
//            }
            $input = $request->only(['name', 'code', 'expense_account', 'payee_id', 'is_sub_category', 'parent_id']);

                
            if (!$request->payee_id) {
                $input['payee_id'] = '0';
            }else{
                $input['payee_id'] = $request->payee_id;
            }
            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;
            $businesses = Business::get();
            $defaultCategory = DefaultExpenseCategory::create($input);
            foreach($businesses as $business) {
                $input['business_id'] = $business->id;
                $input['default_expense_category_id'] = $defaultCategory->id;
              
                $category_exist = ExpenseCategory::where('business_id', $business->id)->where('name',  $defaultCategory->name)->first();
                if (empty($category_exist)) {
                    $exp_acc = Account::where('business_id', $business->id)->where('default_account_id', $defaultCategory->expense_account)->first();
                    $input['expense_account'] = !empty($exp_acc) ? $exp_acc->id : 0;
                    ExpenseCategory::create($input);
                }
            }
            
            $output = [
                'success' => true,
                'expense_category' => $defaultCategory,
                'msg' => __("expense.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }
    
    public function getExpenseCategories() {
        if (request()->ajax()) {
            $expense_category = DefaultExpenseCategory::leftjoin('accounts', 'default_expense_categories.expense_account', 'accounts.id')
                ->leftjoin('contacts', 'contacts.id', '=', 'default_expense_categories.payee_id')
                ->select(['default_expense_categories.name', 'code', 'accounts.name as account_name', 'contacts.name as payee_name', 'default_expense_categories.id']);
            return Datatables::of($expense_category->get())
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '';
                        $html .= '<button data-href="' . action("CategoryController@edit", [$row->id]) . '" class="btn btn-xs btn-primary edit_category_button"><i class="glyphicon glyphicon-edit"></i>  ' . __("messages.edit") . '</button> &nbsp;';

                        $html .= '<button data-href="' . action("CategoryController@destroy", [$row->id]) . '" class="btn btn-xs btn-danger delete_category_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</button>';
                        return $html;
                    }

                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    public function locationsSettings()
    {
        return view('superadmin::superadmin_settings.locations.index');
        
    }
}
