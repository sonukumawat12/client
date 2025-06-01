<?php

namespace Modules\HelpGuide\Http\Controllers\Install;

use Modules\HelpGuide\User;
use Modules\HelpGuide\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class InstallController extends Controller
{
    private $APIUrl = "https://ticky-verify.pandisoft.com/verify";


    public function index()
    {
        // Clear config cache before you go
        Cookie::forget('laravel_session');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        return view('helpguide::install.index');
    }

    public function requirements(Request $request)
    {
        $requirements = array();
        $requirements['PHP version (Your PHP version is '.PHP_VERSION.')'] = version_compare(PHP_VERSION, "7.4", ">=");
        $requirements['OpenSSL enabled'] = extension_loaded("openssl");
        $requirements['mysqlnd enabled'] = extension_loaded('mysqlnd');
        $requirements['PDO enabled'] = defined('PDO::ATTR_DRIVER_NAME');
        $requirements['mbstring enabled'] = extension_loaded("mbstring");
        $requirements['tokenizer enabled'] = extension_loaded("tokenizer");
        $requirements['xml enabled'] = extension_loaded("xml");
        $requirements['ctype enabled'] = extension_loaded("ctype");
        $requirements['fileinfo enabled'] = extension_loaded("fileinfo");
        $requirements['json enabled'] = extension_loaded("json");
        $requirements['zip enabled'] = extension_loaded('zip');
        $requirements['bcmath enabled'] = extension_loaded("bcmath");
        $requirements['GD library enabled'] = extension_loaded('gd') && function_exists('gd_info');
        $requirements['iconv enabled'] = extension_loaded("iconv");
        $requirements['exif enabled'] = extension_loaded('exif') && function_exists('exif_read_data');
        $requirements['File uploads'] = ini_get('file_uploads') == 1;


        if ($request->isMethod('post')){
            // If all is okay go to next step otherwise send error
            foreach($requirements as $item){
                if( ! $item ){
                    return redirect()->back()->withErrors("Please make sure all requirements are satisfied");
                }
            }
            
            session(['requirements_checked' => true]);
            return redirect(route('install.folder_permissions'));
        }
        
        return view('helpguide::install.requirements', ['requirements' => $requirements]);
    }

    public function folderPermissions(Request $request)
    {

        if( !$request->session()->exists('requirements_checked') ){
            return redirect(route('install.requirements'));
        }

        $folders = array(
            'storage/'         => substr(sprintf('%o', fileperms(base_path('storage/'))), -3),
            'storage/logs/'         => substr(sprintf('%o', fileperms(base_path('storage/logs/'))), -3),
            'storage/framework/'    => substr(sprintf('%o', fileperms(base_path('storage/framework/'))), -3),
            'storage/framework/sessions/'      => substr(sprintf('%o', fileperms(base_path('storage/framework/sessions/'))), -3),
            'storage/framework/cache/'      => substr(sprintf('%o', fileperms(base_path('storage/framework/cache/'))), -3),
            'storage/framework/views/'      => substr(sprintf('%o', fileperms(base_path('storage/framework/views/'))), -3),
            'storage/app/'      => substr(sprintf('%o', fileperms(base_path('storage/app/'))), -3),
            'storage/app/migrations/'      => substr(sprintf('%o', fileperms(base_path('storage/app/migrations/'))), -3),
            'storage/app/public/'      => substr(sprintf('%o', fileperms(base_path('storage/app/public/'))), -3),
            'storage/app/public/resources/'      => substr(sprintf('%o', fileperms(base_path('storage/app/public/resources/'))), -3),
            'storage/app/public/tickets/'      => substr(sprintf('%o', fileperms(base_path('storage/app/public/tickets/'))), -3),
            'storage/app/public/articles/'      => substr(sprintf('%o', fileperms(base_path('storage/app/public/articles/'))), -3),
            'bootstrap/cache/'      => substr(sprintf('%o', fileperms(base_path('bootstrap/cache/'))), -3),
            'config/'      => substr(sprintf('%o', fileperms(base_path('config/'))), -3),
            'public/'      => substr(sprintf('%o', fileperms(base_path('public/'))), -3)
        );

        if ($request->isMethod('post')){
            foreach($folders as $item){
                if( ! in_array($item, [777, 775, 755]) ){
                    return redirect()->back()->withErrors("Please check folders permissions and try again");
                }
            }
            session(['folder_permissions_checked' => true]);
            return redirect(route('install.product_license'));
        }
        
        return view('helpguide::install.folderPermissions', ['folders' => $folders]);
    }

    public function productLicense(Request $request)
    {
        if( !$request->session()->exists('folder_permissions_checked') ){
            return redirect(route('install.folder_permissions'));
        }

        if ($request->isMethod('post')){
            
            $validator = Validator::make($request->all(), [
                'pc' => ['required', 'uuid'],
            ],[
                'pc.uuid' => __('Purchase code seems to be invalid, please check your purchase code'),
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()
                        ->withInput()
                        ->withErrors($validator);
            }

            $response = Http::withoutVerifying()->get($this->APIUrl, [
                'pc' => $request->input('pc'),
                'domain' => request()->getHost(),
                'url' => gtCurrentURL(),
                'version' => config('vars.app_version_dev'),
            ]);

            // Verify purchase code
            if(!$response->successful()){
                if($response->serverError()){
                    $errorMsg = "Failed to verify your purchase code due to a server error, please try again or contact the support";
                }else{
                    $errorMsg = "Failed to verify your purchase code, please try again or contact the support";
                }

                return redirect()->back()->withInput()->withErrors($errorMsg);
            }

            if(!isset($response->json()['status']) || $response->json()['status'] == "fail"){
                if(isset($response->json()['message'])){
                    return redirect()->back()->withInput()->withErrors($response->json()['message']);
                }else{
                    return redirect()->back()->withInput()->withErrors("Failed to verify your purchase code, please try again or contact the support");
                }
            }
            
            session(['purchase_code' => $request->input('pc')]);
            $next = route('install.database');
            return redirect($next);            
        }

        return view('helpguide::install.productLicense');
    }


    public function database(Request $request)
    {

        if( !$request->session()->exists('purchase_code') ){
            return redirect(route('install.product_license'));
        }

        if ($request->isMethod('post')){

            $validator = Validator::make($request->all(), [
                'app_url' => ['required'],
                'database_hostname' => ['required'],
                'database_port' => ['required'],
                'database_name' => ['required'],
                'database_username' => ['required'],
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()
                        ->withInput()
                        ->withErrors($validator);
            }

            $settings = config("database.connections.mysql");
    
            config([
                'database' => [
                    'default' => 'mysql',
                    'migrations' => 'migrations',
                    'connections' => [
                        'mysql' => array_merge($settings, [
                            'driver' => 'mysql',
                            'host' => $request->input('database_hostname'),
                            'port' => $request->input('database_port'),
                            'database' => $request->input('database_name'),
                            'username' => $request->input('database_username'),
                            'password' => (string)$request->input('database_password'),
                        ]),
                    ],
                ],
            ]);
    
            DB::purge();
                
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors("Database credentials seems to be incorrect, please check and try again: details : ".$e->getMessage());
            }

            // check database is empty
            if(DB::select('SHOW TABLES')){
                return redirect()->back()->withInput()->withErrors("Database is not empty, Please clear the database or use a different one");
            }

            $settingFile = [
                'app_name' => 'Ticky',
                'app_url' => $request->app_url,
                'app_env' => 'production',
                'app_key' => 'base64:'.base64_encode(Str::random(32)),
                'app_debug' => false,
                'app_debug_level' => 'debug',
                
                'mysql_host' => $request->database_hostname,
                'mysql_port' => $request->database_port,
                'mysql_database' => $request->database_name,
                'mysql_username' => $request->database_username,
                'mysql_password' => $request->database_password,
                
                'mail_driver' => 'sendmail',
                'mail_host' => '',
                'mail_port' => '587',
                'mail_username' => '',
                'mail_password' => '',
                'mail_encryption' => 'tls',
                'mail_from_address' => 'support@'.request()->getHost(),
                'mail_from_name' => 'Ticky',

                'disk_ticket_driver' => 'local',
                'disk_ticket_root' => 'tickets',
                
                'disk_article_driver' => 'local',
                'disk_article_root' => 'articles',

                'disk_avatar_driver' => 'local',
                'disk_avatar_root' => 'avatars',

                'AWS_ACCESS_KEY_ID' => '',
                'AWS_SECRET_ACCESS_KEY' => '',
                'AWS_DEFAULT_REGION' => '',
                'AWS_BUCKET' => '',
                'AWS_URL' => '',

                'product_code' => md5(session('purchase_code')),

                'app_parent_folder' => null
                
            ];

            try {
                $settingFileString = "<?php\n\n";
                $settingFileString .= "return " . var_export($settingFile, true) . ";";
                file_put_contents(config_path('settings.php'), $settingFileString);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors(__("Failed to create the config file"));
            }

            //Create database
            $response = Http::withoutVerifying()->get($this->APIUrl, [
                'pc' => session('purchase_code'),
                'domain' => request()->getHost(),
                'url' => gtCurrentURL(),
                'version' => config('vars.app_version_dev'),
            ]);

            if(!$response->successful()){
                $errorMsg = __("Creating database tables failed, please try again or contact the support");
                return redirect()->back()->withInput()->withErrors($errorMsg);
            }
            
            if(isset($response->json()['status']) && $response->json()['status'] == "ok" && isset($response->json()['data'])){
                
                $d = $response->json()['data'];

                array_map( 'unlink', array_filter((array) glob(storage_path('/app/migrations/*'))));

                foreach ($d as $k => $v) {
                    $f = 'storage/app/migrations/'.$k;
                    file_put_contents(base_path($f), base64_decode($v));
                    if(file_exists(base_path($f))){
                        Artisan::call('migrate', array('--path' => $f, '--force' => true));
                        unlink(base_path($f));
                    }else{
                        $errorMsg = __("Creating database tables failed, please try again or contact the support");
                        return redirect()->back()->withInput()->withErrors($errorMsg);
                    }
                }
                
            }else{
                if(isset($response->json()['message'])){
                    return redirect()->back()->withInput()->withErrors($response->json()['message']);
                }
                
                return redirect()->back()->withInput()->withErrors(__("Creating database tables failed, please try again or contact the support"));
            }
            
            // Create default roles and permissions
            Artisan::call('db:seed', array('--class' => 'RolesAndPermissionsSeeder', '--force' => true));

            // Default settings
            Setting::add("app_logo", asset("assets/img/logo.png"), 'file');
            Setting::add("app_name", "Ticky", 'string');
            Setting::add("default_lang", 'en');
            Setting::add("favicon", asset("assets/img/favicon.png"), 'file');
            Setting::add("date_format", "m/d/Y", 'string');
            Setting::add("mail_channel", "sendmail", 'string');
            Setting::add("user_can_register", 1, 'integer');
            Setting::add("mail_from_address", 'support@'.request()->getHost());
            Setting::add("mail_from_name", 'Ticky');
            Setting::add("timezone", "UTC");
            Setting::add("verify_email", 1);

            // Create a default category
            \Modules\HelpGuide\Category::create(['name' => 'default category', 'is_active' => 1]);

            $next = route('install.admin_account');

            // Create cache files and generate the storage link
            Artisan::call("optimize");
            Artisan::call("view:cache");

            if (function_exists('symlink')) {
                Artisan::call("storage:link");
            }
            
            session(['database_installed' => true]);
            return redirect($next);
        }
        return view('helpguide::install.database');
    }

    public function createUser(Request $request)
    {
        if( !DB::select('SHOW TABLES') ){
            return redirect(route('install.product_license'));
        }

        if ($request->isMethod('post')){

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()
                        ->withInput()
                        ->withErrors($validator);
            }

            // Admin account
            $user = new User();
            $user->name = $request->input('name');
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->email = $request->input('email');
            $user->avatar = $user->defaultAvatar();
            $user->password = Hash::make($request->input('password'));
            $user->save();
            $user->assignRole('super_admin');
            
            session(['app_installed' => true]);
            return redirect(route('install.finish'));
        }
   
        return view('helpguide::install.adminAccount');
    }
    
    public function finish(Request $request)
    {
        if(!DB::select('SHOW TABLES') || !DB::table('users')->first()){
            return redirect(route('install.product_license'));
        }

        file_put_contents(storage_path('app/app_installed'), 'Application installed on '.date('Y-m-d H:i:s'));
        return view('helpguide::install.finish');
    }

}
