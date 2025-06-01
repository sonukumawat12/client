<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\DetectDatabaseChangesService;

class DetectDatabaseChanges extends Command
{
    protected $signature = 'db:detect-changes';
    protected $description = 'Detect changes in the database schema';

    protected $detectDatabaseChangesService;

    public function __construct(DetectDatabaseChangesService $detectDatabaseChangesService)
    {
        parent::__construct();
        $this->detectDatabaseChangesService = $detectDatabaseChangesService;
    }

    public function handle()
    {
        $this->info('Starting database updates...');
        $result = $this->detectDatabaseChangesService->runSqlUpdates();
        $this->info($result);
    }
}
