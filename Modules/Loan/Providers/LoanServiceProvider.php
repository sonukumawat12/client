<?php

namespace Modules\Loan\Providers;

use App\Utils\ModuleUtil;
use Illuminate\Database\Eloquent\Factory;

use Illuminate\Support\Facades\View;

use Illuminate\Support\ServiceProvider;

class LoanServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        //TODO:Remove sidebar
        view::composer(['loan::layouts.partials.sidebar',
            'loan::layouts.partials.invoice_layout_settings',
            'loan::layouts.partials.pos_header',
            'loan::layouts.partials.header'
            ], function ($view) {
                if (auth()->user()->can('superadmin')) {
                    $__is_loan_enabled = true;
                } else {
                    $business_id = session()->get('user.business_id');
                    $module_util = new ModuleUtil();
                    $__is_loan_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'loan_module');
                }

                $view->with(compact('__is_loan_enabled'));
            });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('loan.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php',
            'loan'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/loan');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/loan';
        }, \Config::get('view.paths')), [$sourcePath]), 'loan');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/loan');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'loan');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'loan');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
