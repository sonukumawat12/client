<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class DatabaseController extends Controller
{
    public function __construct()
    {
        //
    }

    public function getDatabases(){
        $base_database = config('database.connections.mysql.database');
        
        preg_match('/^(.*?_)base$/', $base_database, $matches);
        if (isset($matches[1])) {
            $prefix = $matches[1]; // This will hold 'vimi50_'
            // Get tenant IDs from the base database
            $baseConnection = DB::connection('mysql')->setDatabaseName($base_database);
            $tenantIds = $baseConnection->table('tenants')->pluck('id')->toArray();
            $tenantIds[] = 'base';
        } else {
            // Handle the case where the database name does not match the expected pattern
            throw new Exception("The base database name format is unexpected. expected prefix_base e.g. 'vimi50_base'");
        }

        return view('database.update_databases')->with(compact(
            'prefix',
            'tenantIds'
        ));
    }

    public function updateDatabases(Request $request)
    {
        $file = $request->file('sqlFile');
        // $sqlContent = file_get_contents($file->getRealPath());
        $sqlContent = file_get_contents($file->getPathname());
        \Log::info("sqlContent: {$sqlContent}");
        $sqlStatements = explode(';', $sqlContent); // split the Multiple ALTER TABLE statements in a single query

        $base_database = config('database.connections.mysql.database');
        preg_match('/^(.*?_)base$/', $base_database, $matches);
        if (isset($matches[1])) {
            $prefix = $matches[1]; // This will hold 'vimi50_'
             // Purge the DB connection and switch to the tenant's database
            DB::purge('mysql');
            config(['database.connections.mysql.database' => $base_database]);
            DB::reconnect('mysql');
            $baseConnection = DB::connection('mysql')->setDatabaseName($base_database);
            // Get tenant IDs from the database
            $tenantIds = $baseConnection->table('tenants')->pluck('id')->toArray();
            $tenantIds[] = 'base';
        } else {
            // Handle the case where the database name does not match the expected pattern
            throw new Exception("The base database name format is unexpected. expected prefix_base e.g. 'vimi50_base'");
        }
        $processLogs = [];
        foreach ($sqlStatements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                \Log::info("Executing: {$statement}");
                $processLogs[] = "Executing: {$statement}";
            }
        }
        foreach ($tenantIds as $tenantId) {
            $tenantDb = $prefix . $tenantId;
            try {
                // switch DB connection to the tenant's database temporarily
                 // Purge the DB connection and switch to the tenant's database
                DB::purge('mysql');
                config(['database.connections.mysql.database' => $tenantDb]);
                DB::reconnect('mysql');
                $tempConnection = DB::connection('mysql')->setDatabaseName($tenantDb);
                
                \Log::info("Updating database {$tenantDb}...");
                $processLogs[] = "Updating database {$tenantDb}...";
                // update tenant database
                foreach ($sqlStatements as $statement) {
                    $statement = trim($statement);
                    if (!empty($statement)) {
                        try {
                            \Log::info("Executing: {$statement}");
                            // $processLogs[] = "Executing: {$statement}";
                            $tempConnection->statement($statement);
                            $processLogs[] = "{$tenantDb} updated.";
                        } catch (\Exception $e) {
                            \Log::error("{$tenantDb} SQL Execution Failed: " . $e->getMessage());
                            $processLogs[] = "{$tenantDb} SQL Execution Failed: " . $e->getMessage();
                        }
                    }
                }
                \Log::info("Executed SQL updates on tenant database: {$tenantDb}");
            } catch (\Exception $e) {
                // $this->error("Error executing SQL on tenant database {$tenantDb}: " . $e->getMessage());
                \Log::error("Error executing SQL on tenant database {$tenantDb}: " . $e->getMessage());
                $processLogs[] = "Error executing SQL on tenant database {$tenantDb}: " . $e->getMessage();
            }
        }
        DB::purge('mysql');
        config(['database.connections.mysql.database' => $base_database]);
        DB::reconnect('mysql');

        return redirect()->route('database.getDatabases')->with('msg', "SQL update process completed successfully.")->with('processLogs', $processLogs);
    }
}
