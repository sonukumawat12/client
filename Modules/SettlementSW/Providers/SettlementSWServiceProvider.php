<?php
namespace Modules\SettlementSW\Providers;

use Illuminate\Support\ServiceProvider;

class SettlementSWServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load the module’s routes:
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        // Load its views under the "settlementsw::" namespace:
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'settlementsw');

        //load translation
        $this->registerTranslations();
    }

    public function register()
    {
        //
    }

    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/SettlementSW');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'SettlementSW');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'SettlementSW');
        }
    }
}
