<?php

namespace Modules\HelpGuide\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::DefaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(isAppInstalled()){
            date_default_timezone_set(setting('timezone', 'UTC'));
        }

        if(isSSLEnabled()) {
            \URL::forceScheme('https');
        }
    }
}
