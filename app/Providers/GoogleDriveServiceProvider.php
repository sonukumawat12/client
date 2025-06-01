<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Log;

class GoogleDriveServiceProvider extends ServiceProvider
{
    public function boot()
    {
        try{
            \Storage::extend('google', function ($app, $config) {
                $client = new GoogleClient();
                $client->setClientId($config['client_id']);
                $client->setClientSecret($config['client_secret']);
                $client->refreshToken($config['refresh_token']);
    
                $service = new \Google\Service\Drive($client);
    
                $adapter = new GoogleDriveAdapter($service, $config['folder_id']);
    
                return new Filesystem($adapter);
            });
        } catch(\Exception $e) {
            Log::error("GoogleDriveServiceProvider", [
                $e
            ]);
        }
    }
}
