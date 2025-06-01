<?php

namespace Modules\HelpGuide\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Nwidart\Modules\Facades\Module as BaseModule;

class Module extends BaseModule
{
    private static $APIURL = "https://pandisoft.com/ticky/modules/";

    public static function list()
    {
        // Local modules
        $lmodules = [];
        
        // Remote Modules 
        $rmodules = [];

        foreach (self::all() as $module) {

            // ignore hidden modules
            if( $module->get('is_hidden') ) continue;

            $lmodules[$module->getLowerName()] = [
                'name' => $module->get('alias'),
                'id' => $module->getLowerName(),
                'description' => $module->get('description'),
                'keywords' => $module->get('keywords'),
                'version' => $module->get('version'),
                'version_dev' => $module->get('version_dev'),
                'app_min_version' => $module->get('app_min_version'),
                'price' => $module->get('price'),
                'new_price' => $module->get('new_price'),
                'order' => $module->get('order'),
                'is_enabled' => $module->isEnabled(),
                'is_installed' => true,
                'is_changing' => false,
                'author' => $module->get('author'),
                'author_url' => $module->get('author_url'),
                'download_link' => '#',
                'has_update' => false,
                'thumbnail' => route('dashboard.modules_manager.thumbnail', ["module" => $module->getLowerName() ])
            ];
        }

        $rmodules = self::getRemoteModuleList();

        foreach ($rmodules as $mk => $mv) {
          $rmodules[$mk]['is_installing'] = false;
            if( isset($lmodules[$mk])){
                if( $lmodules[$mk]['version_dev'] < $mv['version_dev'] ){
                    $lmodules[$mk]['has_update'] = true;
                }
                unset($rmodules[$mk]);
            }
        }

        // Merge list modules
        $allmodules = array_merge($lmodules, $rmodules);

        // Sort modules
        usort($allmodules, function ($m1, $m2) {
            return $m1['order'] <=> $m2['order'];
        });

        return $allmodules;
    }

    public static function getRemoteModuleList()
    {
        return Cache::remember('remote_modules_list', 3600, function() {
            $response = Http::withoutVerifying()->get(self::$APIURL);
            if($response->successful()){
                if(isset($response->json()['status']) || $response->json()['status'] == "ok"){
                    return $response->json()['modules'];
                }
            }
            return [];
        });
    }

    public static function getModuleThumbnail($module)
    {
      return Cache::remember('remote_module_thumbnail_'.$module, 86400, function() use ($module){

          $m = Module::find($module);

          $path = public_path('assets/img/module-placeholder.svg');

          if( $m ){
              if ( File::exists( Module::getModulePath($module).'thumbnail.jpg') ) {
                  $path = Module::getModulePath($module).'thumbnail.jpg';
              }
          }

          if (!File::exists($path)) {
              abort(404);
          }
      
          $file = File::get($path);
          $type = File::mimeType($path);
          $response = Response::make($file, 200);
          $response->header("Content-Type", $type);
          return $response;
      });
    }

    public static function permissions()
    {
        $permissions = [];

        foreach (self::allEnabled() as $module) {
            
            $mconfig = Module::getModulePath($module->getName()).'/Config/config.php';

            if( ! file_exists($mconfig) ){
                continue;
            }

            $config = require $mconfig;

            if(!isset($config['permissions'])) continue; 

            $permissions[$module->getName()] = $config['permissions'];
        }

        return $permissions;
    }
}
