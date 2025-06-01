<?php

namespace Modules\Translation\Providers;

use Modules\Translation\Scanner;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Translation\TranslationManager;
use Modules\Translation\Drivers\Translation;
use Illuminate\Support\Facades\Config;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Translation';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'translation';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->publishAssets();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->registerHelpers();
        $this->registerMenu();

       
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
        $this->app->register(RouteServiceProvider::class);
        $this->registerContainerBindings();
    }

    /** 
     * Add item to dashboard menu
     */
    public function registerMenu()
    {

        $menu = config('menu');

        $menu['sidebar']['tanslation'] = [
            'name' => 'Translation',
            'target' => '',
            'icon' => 'bi bi-translate',
            'route' => 'languages.index',
            'url' => null,
            'sub_items' => [],
            'permissions' => [],
            'order' => 8
        ];

        Config::set('menu', $menu);
    }

    /**
     * Publish package assets.
     *
     * @return void
     */
    private function publishAssets()
    {
        $this->publishes([
            __DIR__.'/../public/assets' => public_path('vendor/translation'),
        ], 'assets');
    }
    
    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            // app(Factory::class)->load(module_path($this->moduleName, 'Database/factories'));
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

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/Modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/Modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }

    /**
     * Register package helper functions.
     *
     * @return void
     */
    private function registerHelpers()
    {
        require __DIR__.'/../Resources/helpers.php';
    }

    /**
     * Register package bindings in the container.
     *
     * @return void
     */
    private function registerContainerBindings()
    {
        $this->app->singleton(Scanner::class, function () {
            $config = $this->app['config']['translation'];

            return new Scanner(new Filesystem, $config['scan_paths'], $config['translation_methods']);
        });

        $this->app->singleton(Translation::class, function ($app) {
            return (new TranslationManager($app, $app['config']['translation'], $app->make(Scanner::class)))->resolve();
        });
    }
}
