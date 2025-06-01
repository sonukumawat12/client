<?php

namespace App\Http\Middleware;

use Closure;

class CheckIsAppInstalled
{
    protected $except = [
        'install*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( ! isAppInstalled() ){

            $folders = array(
                'storage/'         => substr(sprintf('%o', fileperms(storage_path(''))), -3),
                'storage/logs/'         => substr(sprintf('%o', fileperms(storage_path('logs/'))), -3),
                'storage/framework/'    => substr(sprintf('%o', fileperms(storage_path('framework/'))), -3),
                'storage/framework/sessions/'      => substr(sprintf('%o', fileperms(storage_path('framework/sessions/'))), -3),
                'storage/framework/cache/'      => substr(sprintf('%o', fileperms(storage_path('framework/cache/'))), -3),
                'storage/framework/views/'      => substr(sprintf('%o', fileperms(storage_path('framework/views/'))), -3),
                'storage/app/'      => substr(sprintf('%o', fileperms(storage_path('app/'))), -3),
                'storage/app/migrations/'      => substr(sprintf('%o', fileperms(storage_path('app/migrations/'))), -3),
                'storage/app/public/'      => substr(sprintf('%o', fileperms(storage_path('app/public/'))), -3),
                'storage/app/public/resources/'      => substr(sprintf('%o', fileperms(storage_path('app/public/resources/'))), -3),
                'storage/app/public/tickets/'      => substr(sprintf('%o', fileperms(storage_path('app/public/tickets/'))), -3),
                'storage/app/public/articles/'      => substr(sprintf('%o', fileperms(storage_path('app/public/articles/'))), -3),
                'bootstrap/cache/'      => substr(sprintf('%o', fileperms(storage_path('framework/'))), -3),
                'config/'      => substr(sprintf('%o', fileperms(base_path('config/'))), -3),
            );

            $folderNeedToBeChecked = false;

            foreach ($folders as $fp) {
                if( ! in_array($fp, [777, 775, 755]) ){
                    $folderNeedToBeChecked = true;
                }
            }

            if( $folderNeedToBeChecked ){
                echo "<div style='border: 2px solid #a44842; padding: 15px'>";
                echo "<div>";
                echo "<p>It seems that some folders are not writable, Please make sure all folders below are writable</p>";
                echo "<p>Before you start installing the application below folders must have 755 or 775 or 777 permissions</p>";
                echo "<p>Please check the documentation on how to change your folder permissions <br /><a target='_blank' href='https://support.pandisoft.com/articles/15'>https://support.pandisoft.com/articles/15</a></p>";
                echo "</div>";
                echo "<table>";
                foreach ($folders as $fk => $fv) {
                    echo "<tr><td>".$fk."</td>";
                    if(in_array($fv, [777, 775, 755])){
                        echo "<td><span style='padding: 0 5px; background-color: green; color: white'>".$fv."</span></td>";
                    } else {
                        echo "<td><span style='padding: 0 5px; background-color: red; color: white'>".$fv."</span></td>";
                    }
                    echo "</tr>";
                }
                echo "</table></div>";
                exit();
            }
        }

        if (!isAppInstalled() && !$request->is('install*')) {
            return redirect('install');
        } else if ( isAppInstalled() && $request->is('install*')){
            \abort(404);
        }

        return $next($request);
    }
}
