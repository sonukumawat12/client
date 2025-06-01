<?php

namespace Modules\HelpGuide\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if( isAppInstalled() ){
            $config = Config::get('services');


            if(setting('envato_oauth_enabled', false)) {
                $config['envato'] = [];
                $config['envato']['client_id'] = setting('envato_oauth_app_id');
                $config['envato']['client_secret'] = setting('envato_oauth_app_secret');
                $config['envato']['redirect'] = url('login/envato/callback');
            };

            if(setting('facebook_oauth_enabled', false)) {
                $config['facebook'] = [];
                $config['facebook']['client_id'] = setting('facebook_oauth_app_id');
                $config['facebook']['client_secret'] = setting('facebook_oauth_app_secret');
                $config['facebook']['redirect'] = url('login/facebook/callback');
            };

            if(setting('google_oauth_enabled', false)) {
                $config['google'] = [];
                $config['google']['client_id'] = setting('google_oauth_app_id');
                $config['google']['client_secret'] = setting('google_oauth_app_secret');
                $config['google']['redirect'] = url('login/google/callback');
            };

            Config::set('services', $config);

            
            // update config
            $config = Config::get('app');
            $config['debug'] = (boolean)setting('app_debug', defaultSetting('app_debug', false));
            Config::set('app', $config);

        }
    }
}
