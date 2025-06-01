<?php

namespace Modules\Translation\Entities;

use App\Models\Module;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class Activate
{
    public function run()
    {
        //$this->runSeed();
        //$this->runMigration();
        $this->copyAssets();
    }

    public function runSeed()
    {
        $seeder = new \Modules\Translation\Database\Seeders\TranslationDatabaseSeeder();
        $seeder->run();
    }
    
    public function runMigration()
    {
        Artisan::call('module:migrate', ['Translation', '--force' => true ]);
    }

    public function copyAssets()
    {
        File::copyDirectory(Module::getModulePath('Translation').'public/assets', public_path('assets/modules/translation'));
    }
}
