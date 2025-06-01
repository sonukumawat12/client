<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Modules\HelpGuide\Entities\Setting;
use Illuminate\Http\Request;

use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{

    public function index()
    {
        // $this->authorize('view', Setting::class);
        return view("helpguide::dashboard.settings.index");
    }

    public function migration()
    {
        Artisan::call('migrate', ['--force' => true ]);
        return Artisan::output();
    }

    // Save settings using recieved from ajax
    public function save(Request $request)
    {
        // $this->authorize('update', Setting::class);

        $rules = Setting::getValidationRules();

        if( ! Setting::isFieldExists($request->input('key')) ){
            Log::debug('SettingsController save: ', [
                $request->input('key')
            ]);
            return ['status' => 'warning', "message" => __('Setting not defined')];
        }
        
        $key = $request->input('key');
        $value = $request->input('val');

        $validator = Validator::make([$key => $value], [$key => $rules[$key]]);

        if ($validator->fails()) {
            return ['status' => 'fail', 'errors' => $validator->errors() ];
        }

        Setting::add($key, $value, Setting::getDataType($key));

        return ['status' => 'ok', 'message' => __('Settings has been saved.')];
    }  

    public function advancedSettings()
    {
        // $this->authorize('view', Setting::class);
        return view("helpguide::dashboard.settings.advanced_settings");
    }

    public function fetch()
    {   
        // $this->authorize('view', Setting::class);
        $settings = Setting::getAllSettings()->pluck('val','name');
        
        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $languages = availableLanguages();

        $isDownForMaintenance = app()->isDownForMaintenance();
        
        return ['settings' => $settings, 'timezones' => $timezones, 'languages' => $languages, 'isDownForMaintenance' => $isDownForMaintenance];
    }

    public function clearCache()
    {
        // $this->authorize('update', Setting::class);
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');

        if(config('app.env') === 'production'){
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            Artisan::call('config:cache');
            Artisan::call('optimize');
            Artisan::call("storage:link");
        }

        Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder', '--force' => true]);

        // Update application assets version
        $varsFile = config_path('vars.php');
        $vars = require $varsFile;
        $vars['asset_version'] = uniqid();
        $varsFileString = "<?php\n\n";
        $varsFileString .= "return " . var_export($vars, true) . ";";
        file_put_contents($varsFile, $varsFileString);

        return ['status' => 'ok', 'message' => __("Application cached has been cleared and recached")];
    }

    public function toggleMaintenanceMode(Request $request)
    {
        // $this->authorize('update', Setting::class);
        $debugMode = $request->input('maintenance_mode');

        if($debugMode){
            Artisan::call('down', ['--message' => __("The site is down for maintenance")]);
            return ['status' => 'ok', 'message' => __("Debug mode has been enabled")];
        }
        
        Artisan::call('up');
        return ['status' => 'ok', 'message' => __("Debug mode has been disabled")];
    }

    public function toggleEnableCache(Request $request)
    {
        // $this->authorize('update', Setting::class);
        $cacheEnabled = $request->input('enable_cache');

        if($cacheEnabled){
            Artisan::call("optimize");
            Artisan::call("view:cache");
            return ['status' => 'ok', 'message' => __("Cache has been enabled")];
        }
        
        Artisan::call("cache:clear");
        Artisan::call("route:clear");
        Artisan::call("view:clear");
        Artisan::call("config:clear");

        return ['status' => 'ok', 'message' => __("Cache has been disabled")];
    }

    public function serverStatus()
    {
        // $this->authorize('view', Setting::class);
        return [
            "max_execution_time" => ini_get("max_execution_time"),
            "max_input_time" => ini_get("max_input_time"),
            "max_input_vars" => ini_get("max_input_vars"),
            "memory_limit" => ini_get("memory_limit"),
            "post_max_size" => ini_get("post_max_size"),
            "upload_max_filesize" => ini_get("upload_max_filesize"),
            "zip" => extension_loaded('zip'),
            "php_version" => phpversion()
        ];
    }
}