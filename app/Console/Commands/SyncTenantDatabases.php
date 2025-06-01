<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncTenantDatabases extends Command
{
    protected $signature = 'tenants:sync';
    protected $description = 'Sync tenant databases with changes from the base database';

    public function handle()
    {
        $baseDb = env('DB_DATABASE');
        
        config(['database.connections.mysql.database' => $baseDb]);
        DB::reconnect('mysql'); 
        $changes = DB::table('base_change_log')->get();
    
        if ($changes->isEmpty()) {
            $this->info('No changes to sync.');
            return;
        }
    
        $tenantIds = DB::table('tenants')->pluck('id')->toArray();
    
        foreach ($changes as $change) {
            foreach ($tenantIds as $tenantId) {
                
                $tenantDb = 'vimi50_' . $tenantId;
    
                DB::purge('mysql');
                config(['database.connections.mysql.database' => $tenantDb]);
                DB::reconnect('mysql');
    
                switch ($change->change_type) {
                    case 'create':
                        DB::purge('mysql');
                        config(['database.connections.mysql.database' => $baseDb]);
                        DB::reconnect('mysql');
                        $data = DB::table($change->table_name)->where('id', $change->row_id)->first();
    
                        config(['database.connections.mysql.database' => $tenantDb]);
                        DB::reconnect('mysql');
                        DB::table($change->table_name)->insert((array) $data);
                        
                        break;
    
                    case 'update':

                        DB::purge('mysql');
                        config(['database.connections.mysql.database' => $baseDb]);
                        DB::reconnect('mysql');
                        $data = DB::table($change->table_name)->where('id', $change->row_id)->first();
    
                        config(['database.connections.mysql.database' => $tenantDb]);
                        DB::reconnect('mysql');
                        DB::table($change->table_name)->where('id', $change->row_id)->update((array) $data);
                        
                        break;
    
                    case 'delete':
                       
                        DB::table($change->table_name)->where('id', $change->row_id)->delete();
                        
                        break;
                }
            }
        }
    
       
        DB::purge('mysql');
        config(['database.connections.mysql.database' => $baseDb]);
        DB::reconnect('mysql');
        DB::table('base_change_log')->truncate();
    
    }

}
