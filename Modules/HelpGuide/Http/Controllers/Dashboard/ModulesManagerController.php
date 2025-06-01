<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Modules\HelpGuide\Models\Module;
use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Modules\HelpGuide\Http\Requests\Admin\InstallModuleRequest;
use Illuminate\Filesystem\Filesystem;

class ModulesManagerController extends Controller
{

  public function list()
  {
    abort_unless(Auth::User()->can('list_modules'), 403);
    return Module::list();
  }

  /**
   * enable the specified resource in storage.
   * @param Request $request
   * @param int $id
   * @return Renderable
   */
  public function toggleModuleStatus(Request $request, $module)
  {
    abort_unless(Auth::User()->can('manage_modules'), 403, __("You don't have permission to perform this action"));

    $m = Module::find($module);

    if (!$m) {
      abort(404, __('Module :module_name not found', ['module_name' => $module]));
    }

    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');

    if ($m->isEnabled()) {
      $m->disable();
      $isEnabled = false;
    } else {
      $m->enable();

      if (file_exists(base_path("Modules/" . $m->getName() . "/Entities/Activate.php"))) {
        $ModuleActivate = "\Modules\\" . $m->getName() . "\Entities\Activate";
        $ModuleActivateInstance = new $ModuleActivate();
        $ModuleActivateInstance->run();
      }

      $isEnabled = true;
    }

    if (config('app.env') === 'production') {
      Artisan::call('route:cache');
      Artisan::call('view:cache');
      Artisan::call('config:cache');
      Artisan::call('optimize');
      Artisan::call("storage:link");
    }

    return [
      'status' => 'success',
      'module' => $module,
      'is_enabled' => $isEnabled,
      'message' => __('Module :module_name has been :new_status', ['module_name' => $m, 'new_status' => $isEnabled ? __('Enabled') : __('Disabled')])
    ];
  }

  /**
   * Install the specified resource in storage.
   * @param Request $request
   * @param int $id
   * @return Renderable
   */
  public function install(InstallModuleRequest $request)
  {
    $m = Module::find($request->module);

    if ($m) {
      abort(403, __('Module :module_name already installed', ['module_name' => $request->module]));
    }

    $awailableModules = Module::getRemoteModuleList();

    if (
      !isset($awailableModules[$request->module]) ||
      !isset($awailableModules[$request->module]['download_link']) ||
      !$awailableModules[$request->module]['download_link']
    ) {
      return ApiResponse('Module not found', 403);
    }
    
    $zipFile = file_get_contents( $awailableModules[$request->module]['download_link'] );

    if( ! $zipFile ) return ApiResponse('Install module failed, could not fetch the module');

    $filePath = Storage::disk('local')
    ->path('modules/'.pathinfo($awailableModules[$request->module]['download_link'], PATHINFO_BASENAME));

    file_put_contents($filePath, $zipFile);
    
    $zip = new \ZipArchive;

    $res = $zip->open($filePath);

    if ( ! $res ) return ApiResponse('Invalide module file', 422);

    // Tmp folder
    $tmpf = '/app/modules/extracted/';

    // Clear module folder
    $filesystem = new Filesystem;
    $filesystem->cleanDirectory( storage_path($tmpf) );

    $zip->extractTo( storage_path($tmpf) );

    // get module folder name
    $moduleFolder = array_slice(scandir(storage_path($tmpf)), 2);

    // check if valid module
    if(count($moduleFolder) == 0 || ! file_exists( storage_path($tmpf.$moduleFolder[0].'/module.json') )){
      return ApiResponse('Invalide module file', 422);
    }

    $zip->extractTo( base_path('Modules') );
    $zip->close();

    unlink($filePath);

    // Clean tmp files
    $filesystem->cleanDirectory( storage_path($tmpf) );

    return ApiResponse('Module has been installed');
  }

  public function moduleThumbnail($module)
  {
    return Module::getModuleThumbnail($module);
  }
}
