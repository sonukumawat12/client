<?php

namespace Modules\Customizer\Entities;

use App\Models\Module;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class Activate
{
    public function run()
    {
        $this->runSeed();
        $this->runMigration();
        $this->copyAssets();
    }

    public function runSeed()
    {
        $seeder = new \Modules\Customizer\Database\Seeders\CustomizerDatabaseSeeder();
        $seeder->run();
    }
    
    public function runMigration()
    {
        Artisan::call('module:migrate', ['Customizer', '--force' => true ]);
    }

    public function copyAssets()
    {
        File::copyDirectory(Module::getModulePath('Customizer').'public/assets', public_path('assets/modules/customizer'));
    }
}
