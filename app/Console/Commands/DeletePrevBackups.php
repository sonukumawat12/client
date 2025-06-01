<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DeletePrevBackups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backups:delete-old {days=300}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes Google Drive backup files older than the specified number of days';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            // $days = (int) $this->argument('days');
            $days = (int) env('BACKUP_RETENTION_DAYS', 300); // Default to 300 if not set
            $files = Storage::disk('google')->files(); // Use 'google' disk
    
            foreach ($files as $file) {
                // Fetch the last modified date of the file
                $lastModified = Carbon::createFromTimestamp(Storage::disk('google')->lastModified($file));
                Log::info("Checking file: $file - Last Modified: $lastModified");
                
                // If the file is older than specified days, delete it
                if ($lastModified->lt(Carbon::now()->subDays($days))) {
                    Storage::disk('google')->delete($file);
                    Log::info("Deleted file: $file");
                    $this->info("Deleted: $file");
                }
            }
            Log::info("Old Google Drive backups deleted successfully.");
            $this->info("Old Google Drive backups deleted successfully.");
        }catch(\Exception $e) {
            Log::error("DeletePrevBackups",[
                $e
            ]);
        }
    }
}
