<?php

namespace Modules\HelpGuide\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;


class RouteServiceProvider extends ServiceProvider
{

  /**
     * @var string $moduleName
     */
    protected $moduleName = 'HelpGuide';

    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\HelpGuide\Http\Controllers';

  /**
   * Define your route model bindings, pattern filters, etc.
   *
   * @return void
   */
  public function boot()
  {
    $this->configureRateLimiting();
    // Include the helpers file
    if (file_exists($file = module_path($this->moduleName, 'Helpers/helpers.php'))) {
        require $file;
    }
  }

  /**
   * Define the routes for the application.
   *
   * @return void
   */
  public function map()
  {
    $this->mapWebRoutes();
    $this->mapApiRoutes();
    $this->mapMyAccountRoutes();
    $this->mapDashboardRoutes();
    $this->mapPublicRoutes();
    $this->mapInstallRoutes();
  }

  /**
   * Define the "web" routes for the application.
   *
   * These routes all receive session state, CSRF protection, etc.
   *
   * @return void
   */
  protected function mapWebRoutes()
  {

    if (setting('is_frontend_public', true)) {
      Route::middleware(['web', 'tenant.context'])->namespace($this->moduleNamespace)->group(module_path('HelpGuide', '/Routes/localeWebRoutes.php'));
    } else {
      Route::middleware(['web', 'tenant.context'])->middleware('auth')->namespace($this->moduleNamespace)->group(module_path('HelpGuide', '/Routes/localeWebRoutes.php'));
    }
  }

  /**
   * Define the "install" routes for the application.
   *
   * These routes all receive session state, CSRF protection, etc.
   *
   * @return void
   */
  protected function mapInstallRoutes()
  {
    Route::prefix('install')
      ->middleware(['install', 'tenant.context'])
      ->namespace($this->moduleNamespace)
      ->group(module_path('HelpGuide', '/Routes/install.php'));
  }

  /**
   * Define the "public" routes for the application.
   *
   * These routes all receive session state, CSRF protection, etc.
   *
   * @return void
   */
  protected function mapPublicRoutes()
  {
    Route::middleware(['public', 'tenant.context'])
      ->namespace($this->moduleNamespace)
      ->group(module_path('HelpGuide', '/Routes/public.php'));
  }

  /**
   * Define the "api" routes for the application.
   *
   * These routes are typically stateless.
   *
   * @return void
   */
  protected function mapApiRoutes()
  {
    Route::prefix('api/v1')
      ->middleware(['dashboard', 'tenant.context'])
      ->namespace($this->moduleNamespace)
      ->group(module_path('HelpGuide', '/Routes/api/customer/v1.php'));
    
    $middleware = ['api','role:agent|non-restricted_agent|admin|super_admin'];

    // If Email verification is enabled require user to verify thier email before login 
    if ((bool)setting('verify_email', true) === true) {
      $middleware[] = 'verified';
    }

    Route::prefix(defaultSetting('dashboard_prefix', 'helpguide/dashboard') . '/api/v1')
      ->middleware( ['dashboard', 'tenant.context'] )
      ->namespace($this->moduleNamespace)
      ->group(module_path('HelpGuide', '/Routes/api/admin/v1.php'));
  }

  protected function mapDashboardRoutes()
  {
    Route::prefix(defaultSetting('dashboard_prefix', 'helpguide/dashboard'))
      ->middleware(['dashboard', 'tenant.context'])
      ->namespace($this->moduleNamespace)
      ->group(module_path('HelpGuide', '/Routes/dashboard.php'));
  }

  protected function mapMyAccountRoutes()
  {
    Route::prefix('my_account')
      ->middleware(['my_account', 'tenant.context'])
      ->namespace($this->moduleNamespace)
      ->group(module_path('HelpGuide', '/Routes/my_account.php'));
  }

  /**
   * Configure the rate limiters for the application.
   *
   * @return void
   */
  protected function configureRateLimiting()
  {
    RateLimiter::for('api', function (Request $request) {
      return Limit::perMinute(100)->by(optional($request->user())->id ?: $request->ip());
    });
  }
}
