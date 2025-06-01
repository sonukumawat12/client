<?php

namespace Modules\HelpGuide\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
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

            // Set mail config
            $configMail = Config::get('mail');

            if(setting('mail_channel') == "smtp"){
                $configMail['driver'] = 'smtp';
                $configMail['host'] = setting('smtp_host');
                $configMail['port'] = setting('smtp_port');
                $configMail['encryption'] = setting('smtp_encryption');
                $configMail['username'] = setting('smtp_username');
                $configMail['password'] = setting('smtp_password');
            }
            
            $configMail['from'] = [
                'address' => setting('mail_from_address', defaultSetting('mail_from_address')),
                'name' => setting('mail_from_name', "Ticky app")
            ];
            
            Config::set('mail', $configMail);
        }
    }
}   