<?php
namespace App\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DetectDatabaseChangesService
{
    private $updatedTablesToBackup = [];
    private $processLogs = [];

    public function runSqlUpdates()
    {
        \Log::info('Initial memory usage: ' . memory_get_usage(true));
        set_time_limit(300); // Set to 5 minutes (300 seconds)
        ini_set('memory_limit', '7G');
        
        $base_database = config('database.connections.mysql.database');
        // Use specific DB
        $specific_db = $base_database;
        // $specific_db = "vimi50_cool";
        // $specific_db = "vimi50_hiru";
        if($specific_db != $base_database){
            DB::purge('mysql');
            config(['database.connections.mysql.database' => $specific_db]);
            DB::reconnect('mysql');
        }

        // $this->info('Checking changes...');
        \Log::info("Checking " . DB::connection()->getDatabaseName() . " changes...");
        $this->processLogs[] = "Checking " . DB::connection()->getDatabaseName() . " changes...";
        $database = config('database.connections.mysql.database');

        // Set the max execution time for the session
        $dbVersion = DB::selectOne('SELECT VERSION() as version')->version;
        if (stripos($dbVersion, 'mariadb') !== false) {
            // It's MariaDB, so use max_statement_time
            DB::statement("SET SESSION max_statement_time=300000"); // 300000 milliseconds (300 seconds)
        } else {
            // It's MySQL, so use max_execution_time
            DB::statement("SET SESSION max_execution_time=300000"); // Set to 300 seconds
        }

        // Fetch current schema metadata
        $currentSchema = collect(DB::select("
            SELECT 
                t.TABLE_NAME,
                t.UPDATE_TIME,
                c.COLUMN_NAME,
                c.COLUMN_TYPE,
                c.IS_NULLABLE,
                c.COLUMN_DEFAULT,
                c.COLUMN_KEY,
                s.INDEX_NAME,
                s.SEQ_IN_INDEX,
                s.COLUMN_NAME AS INDEX_COLUMN_NAME,
                s.INDEX_TYPE
            FROM information_schema.tables t
            LEFT JOIN information_schema.columns c 
                ON t.TABLE_NAME = c.TABLE_NAME
            LEFT JOIN information_schema.statistics s
                ON t.TABLE_NAME = s.TABLE_NAME
            WHERE t.TABLE_SCHEMA = ?
            ORDER BY t.TABLE_NAME, s.INDEX_NAME, s.SEQ_IN_INDEX
        ", [$database]));
        \Log::info('currentSchema memory usage: ' . memory_get_usage(true));

        $groupedSchema = collect($currentSchema->groupBy('TABLE_NAME')->map(function ($columns) {
            return [
                'TABLE_NAME' => $columns->first()->TABLE_NAME,
                'UPDATE_TIME' => $columns->first()->UPDATE_TIME,
                'COLUMNS' => $columns->map(function ($column) {
                    return [
                        'Field' => $column->COLUMN_NAME,
                        'Type' => $column->COLUMN_TYPE,
                        'Null' => $column->IS_NULLABLE,
                        'Default' => $column->COLUMN_DEFAULT,
                        'Key' => $column->COLUMN_KEY,
                    ];
                })->values(),
                'indexes' => $columns->whereNotNull('INDEX_NAME')->groupBy('INDEX_NAME')->map(function ($indexData) {
                    return $indexData->map(function ($index) {
                        return [
                            'INDEX_NAME' => $index->INDEX_NAME,
                            'INDEX_COLUMN_NAME' => $index->INDEX_COLUMN_NAME,
                            'SEQ_IN_INDEX' => $index->SEQ_IN_INDEX,
                            'INDEX_TYPE' => $index->INDEX_TYPE,
                        ];
                    });
                }),
            ];
        }));
        unset($currentSchema);
        \Log::info('groupedSchema memory usage: ' . memory_get_usage(true));

        // Define the schema file path using storage_path
        $schemaFile = storage_path('sql/' . $database . '_database_schema.json');

        // Log the file path
        \Log::info("Schema file: $schemaFile");

        // Ensure the directory exists
        if (!file_exists(dirname($schemaFile))) {
            mkdir(dirname($schemaFile), 0755, true);
        }

        if (file_exists($schemaFile)) {
            // Log that the previous schema was found
            \Log::info("Found previous schema...");
            $this->processLogs[] = "Found previous schema.";
            
            // Read the previous schema from the file
            $previousSchema = json_decode(file_get_contents($schemaFile), true);

            // Compare the two schemas
            $this->processLogs[] = "Comparing current schema from previous schema...";
            $this->compareSchemas($previousSchema, $groupedSchema);
            unset($groupedSchema);
            unset($previousSchema);
        } else {
            \Log::info("No previous schema found. Saving current schema...");
            $this->processLogs[] = "No previous schema found. Saving current schema...";
            file_put_contents($schemaFile, json_encode($groupedSchema));
            unset($groupedSchema);
        }

        // Reset back to the default database
        DB::purge('mysql');
        config(['database.connections.mysql.database' => $base_database]);
        DB::reconnect('mysql');

        $this->processLogs[] = "$specific_db updates check completed.";
        return [
            'message' => "$specific_db SQL updates check ran successfully.",
            'logs' => $this->processLogs
        ];
    }

    private function compareSchemas($previous, $current)
    {
        \Log::info('compareSchemas memory usage: ' . memory_get_usage(true));
        // $this->info('Comparing...');
        \Log::info("Comparing...");
        $previousTables = collect($previous)->keyBy('TABLE_NAME')->map(function ($item) {
            return (object) $item; // Convert each item to an object
        });
        unset($previous);
        $currentTables = collect($current)->keyBy('TABLE_NAME')->map(function ($item) {
            return (object) $item; // Convert each item to an object
        });
        $sqlUpdates = [];

        // Detect new tables
        $newTables = $currentTables->diffKeys($previousTables);
        if ($newTables->isNotEmpty()) {
            // $this->info('New tables detected:');
            \Log::info("New tables detected:");
            $this->processLogs[] = "New tables detected:";
            foreach ($newTables as $table) {
                // $this->line("- {$table->TABLE_NAME}");
                \Log::info("added - {$table->TABLE_NAME}");
                $this->processLogs[] = "added - {$table->TABLE_NAME}";
                if(empty(trim($table->TABLE_NAME))){
                    \Log::debug("Table Skipped: ", [$table]);
                    continue;
                }
                $sqlUpdates[] = $this->generateCreateTableSQL($table->TABLE_NAME);
            }
            \Log::info('newTables memory usage: ' . memory_get_usage(true));
        }

        // Detect removed tables
        $removedTables = $previousTables->diffKeys($currentTables);
        if ($removedTables->isNotEmpty()) {
            // $this->info('Removed tables detected:');
            \Log::info('Removed tables detected:');
            $this->processLogs[] = 'Removed tables detected:';
            foreach ($removedTables as $table) {
                // $this->line("- {$table->TABLE_NAME}");
                \Log::info("removed - {$table->TABLE_NAME}");
                $this->processLogs[] = "removed - {$table->TABLE_NAME}";
                if(empty(trim($table->TABLE_NAME))){
                    \Log::debug("Table Skipped: ", [$table]);
                    continue;
                }
                $sqlUpdates[] = "DROP TABLE IF EXISTS `{$table->TABLE_NAME}`;";
            }
            \Log::info('removedTables memory usage: ' . memory_get_usage(true));
        }

        // Detect altered tables (based on UPDATE_TIME)
        foreach ($currentTables as $tableName => $currentTable) {
            // $this->line($tableName);
            // $this->line(json_encode($currentTable));
            // break;
            if ($previousTables->has($tableName)) {
                $previousTable = $previousTables[$tableName];
                if ($currentTable->UPDATE_TIME !== $previousTable->UPDATE_TIME) {
                    // $this->info("Table altered: {$tableName}");
                    \Log::info("Table altered: {$tableName}");
                    $this->processLogs[] = "Table altered: {$tableName}";
                    $sqlUpdates[] = $this->generateAlterTableSQL($tableName);
                }
            }
        }
        unset($previousTables);
        unset($currentTables);
        \Log::info('Detect altered tables memory usage: ' . memory_get_usage(true));

        // Save current schema for future comparisons
        // $this->info('Saving current schema for future comparisons...');
        \Log::info("Saving current schema for next comparison...");
        $this->processLogs[] = "Saving current schema for next comparison...";
        $database = config('database.connections.mysql.database');
        $schemaFile = storage_path('sql/' . $database . '_database_schema.json');
        file_put_contents($schemaFile, json_encode($current));
        unset($current);
        \Log::info('Saved current schema memory usage: ' . memory_get_usage(true));

        if (empty($sqlUpdates)) {
            $this->processLogs[] = "No updates found on database.";
        }

        // Output and save generated SQL updates
        if (!empty($sqlUpdates)) {
            // $this->info("Generated SQL updates:");
            \Log::info("Generated SQL updates:");

            // Format timestamp for the filename
            $timestamp = now()->format('Y_m_d_His'); // e.g., 2024_11_15_123456
            $sqlFileName = "{$database}_sql_updates_{$timestamp}.sql";
            $sqlFilePath = storage_path("sql/{$sqlFileName}");

            // Prepare SQL content for file
            $sqlContent = implode(PHP_EOL, $sqlUpdates) . PHP_EOL;

            // Save to storage
            file_put_contents($sqlFilePath, $sqlContent);
            \Log::info('Save sql memory usage: ' . memory_get_usage(true));

            // $this->info("SQL updates have been saved to: {$sqlFilePath}");
            \Log::info("SQL updates have been saved to: {$sqlFilePath}");

            // Update tenant databases
            $tenantIds = DB::table('tenants')->pluck('id')->toArray();
            foreach ($tenantIds as $tenantId) {
                $tenantDb = 'vimi50_' . $tenantId;
                try {
                    $this->processLogs[] = "Processing tenant database {$tenantDb} updates...";
                    // Purge the DB connection and switch to the tenant's database
                    DB::purge('mysql');
                    config(['database.connections.mysql.database' => $tenantDb]);
                    DB::reconnect('mysql');
                    
                    //Back up updated tables
                    $this->backupTables($tenantDb);
                    
                    $this->processLogs[] = "Updating tenant database {$tenantDb}...";
                    // update tenant database
                    $sqlStatements = explode(';', $sqlContent); // split the Multiple ALTER TABLE statements in a single query 
                    foreach ($sqlStatements as $statement) {
                        $statement = trim($statement);
                        if (!empty($statement)) {
                            try {
                                DB::statement($statement);
                            } catch (\Exception $e) {
                                \Log::error("{$tenantDb} SQL Execution Failed: " . $e->getMessage());
                                $this->processLogs[] = "{$tenantDb} SQL Execution Failed: " . $e->getMessage();
                            }
                        }
                    }

                    // $this->info("Executed SQL updates on tenant database: {$tenantDb}");
                    \Log::info("Executed SQL updates on tenant database: {$tenantDb}");
                } catch (\Exception $e) {
                    // $this->error("Error executing SQL on tenant database {$tenantDb}: " . $e->getMessage());
                    \Log::error("Error executing SQL on tenant database {$tenantDb}: " . $e->getMessage());
                    $this->processLogs[] = "Error executing SQL on tenant database {$tenantDb}: " . $e->getMessage();
                }
            }
            \Log::info('Update tenant databases memory usage: ' . memory_get_usage(true));
            // Reset back to the default database
            DB::purge('mysql');
            config(['database.connections.mysql.database' => $database]);
            DB::reconnect('mysql');
        }
    }

    private function backupTables($tenantDb)
    {
        if(!empty($this->updatedTablesToBackup)) {
            foreach ($this->updatedTablesToBackup as $tableName) {
                \Log::info("Backing up table: {$tableName} of database: {$tenantDb}");
                $this->processLogs[] = "Backing up table: {$tableName} of database: {$tenantDb} ...";
                // Fetch the CREATE TABLE statement
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                if (empty($createTable)) {
                    $this->error("Table `{$tableName}` does not exist.");
                    continue;
                }
                $createStatement = $createTable[0]->{'Create Table'} . ";\n\n";

                // Fetch all rows from the table
                $rows = DB::table($tableName)->get();
                $insertStatements = $rows->map(function ($row) use ($tableName) {
                    $columns = implode('`, `', array_keys((array)$row));
                    $values = implode(', ', array_map(fn($value) => is_null($value) ? 'NULL' : "'" . addslashes($value) . "'", array_values((array)$row)));

                    return "INSERT INTO `{$tableName}` (`{$columns}`) VALUES ({$values});";
                })->implode("\n");

                $backupContent = $createStatement . $insertStatements;

                // Save to file
                $backupFilePath = storage_path("sql/{$tenantDb}_backups/{$tenantDb}_{$tableName}_" . now()->format('Y_m_d_H_i_s') . '.sql');
                // Ensure the directory exists
                if (!file_exists(dirname($backupFilePath))) {
                    mkdir(dirname($backupFilePath), 0755, true);
                }
                file_put_contents($backupFilePath, $backupContent);

                \Log::info("Backup completed and saved to: {$backupFilePath}");
            }
        }
        return true;
    }

    private function generateCreateTableSQL($tableName)
    {
        $createStatement = DB::select("SHOW CREATE TABLE `{$tableName}`");
        return $createStatement[0]->{'Create Table'} . ';';
    }

    private function generateAlterTableSQL($tableName)
    {
        // $this->info("Generating alter table sql");
        \Log::info("Generating alter table sql for {$tableName}");
        // Fetch current and previous schema
        $currentColumns = collect(DB::select("SHOW COLUMNS FROM `{$tableName}`"))->keyBy('Field')->map(function ($item) {
            return (object) $item; // Convert each item to an object
        });
        // $this->line(json_encode($currentColumns));

        // Load the previous columns from storage or other source
        $database = config('database.connections.mysql.database');
        $previousSchema = json_decode(file_get_contents(storage_path('sql/' . $database . '_database_schema.json')), true);
        $previousTable = collect($previousSchema)->where('TABLE_NAME', $tableName)->first();
        unset($previousSchema);

        if (!$previousTable) {
            \Log::info("No previous schema found for table `{$tableName}`");
            return "";
        }

        // Fetch previous columns from schema or backup
        $previousColumns = collect($previousTable['COLUMNS'])->keyBy('Field')->map(function ($item) {
            return (object) $item; // Convert each item to an object
        });;
        // $this->line(json_encode($previousColumns));

        $alterStatements = [];

        // // Detect renamed columns
        // $renamedColumns = $previousColumns->keys()->diff($currentColumns->keys());
        // foreach ($renamedColumns as $oldColumnName) {
        //     // Get the old column's attributes
        //     $previousColumn = $previousColumns->get($oldColumnName);
        
        //     // Try to find a match in the current schema for a renamed column
        //     $potentialMatch = $currentColumns->first(function ($currentColumn, $currentColumnName) use ($previousColumn) {
        //         // Ensure the column isn't already matched
        //         return $previousColumn->Type === $currentColumn->Type &&
        //             $previousColumn->Null === $currentColumn->Null &&
        //             $previousColumn->Default === $currentColumn->Default;
        //     });
        
        //     if ($potentialMatch) {
        //         $alterStatements[] = "CHANGE `{$oldColumnName}` `{$potentialMatch->Field}` {$potentialMatch->Type}" .
        //             ($potentialMatch->Null === 'NO' ? ' NOT NULL' : '') .
        //             ($potentialMatch->Default !== null ? " DEFAULT '{$potentialMatch->Default}'" : '');
        //     }
        // }

        // Detect added columns
        $addedColumns = $currentColumns->diffKeys($previousColumns);
        $existingColumns = collect(DB::select("
            SELECT COLUMN_NAME 
            FROM information_schema.columns 
            WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?
            ORDER BY ORDINAL_POSITION
        ", [$tableName, config('database.connections.mysql.database')]))
        ->pluck('COLUMN_NAME');
        foreach ($addedColumns as $fieldName => $column) {
            $this->processLogs[] = "Added column {$fieldName} on {$tableName}.";
            // Determine the column after which the new column will be added
            $previousColumnIndex = $existingColumns->search($fieldName) - 1; // Index of the column before the new one
            $afterClause = $previousColumnIndex >= 0 
            ? " AFTER `{$existingColumns[$previousColumnIndex]}`" 
            : ' FIRST'; // If it's the first column in the table

            // $this->line(json_encode($column));
            $alterStatements[] = "ADD COLUMN `{$fieldName}` {$column->Type}" .
                ($column->Null === 'NO' ? ' NOT NULL' : '') .
                ($column->Default !== null ? " DEFAULT '{$column->Default}'" : '') .
                ($column->Key === 'PRI' ? ' PRIMARY KEY' : '') .
                $afterClause;
        }

        // Detect removed columns
        $removedColumns = $previousColumns->diffKeys($currentColumns);
        foreach ($removedColumns as $fieldName => $column) {
            $this->processLogs[] = "Removed column {$fieldName} on {$tableName}.";
            $alterStatements[] = "DROP COLUMN `{$fieldName}`";
        }

        // Detect modified columns
        foreach ($currentColumns as $fieldName => $currentColumn) {
            if ($previousColumns->has($fieldName)) {
                $previousColumn = $previousColumns[$fieldName];
                if (
                    $currentColumn->Type !== $previousColumn->Type ||
                    $currentColumn->Null !== $previousColumn->Null ||
                    $currentColumn->Default !== $previousColumn->Default
                ) {
                    $this->processLogs[] = "Modified column {$fieldName} on {$tableName}.";
                    $alterStatements[] = "MODIFY COLUMN `{$fieldName}` {$currentColumn->Type}" .
                        ($currentColumn->Null === 'NO' ? ' NOT NULL' : '') .
                        ($currentColumn->Default !== null ? " DEFAULT '{$currentColumn->Default}'" : '');
                }
            }
        }

        $currentIndexes = $this->fetchIndexes($tableName);
        $previousIndexes = collect($previousTable['indexes'] ?? []);
        $alterStatements = array_merge($alterStatements, $this->compareIndexes($previousIndexes, $currentIndexes));

        // $currentConstraints = $this->fetchConstraints($tableName);
        // $previousConstraints = collect($previousTable['constraints'] ?? []);
        // $alterStatements = array_merge($alterStatements, $this->compareConstraints($previousConstraints, $currentConstraints));

        if (empty($alterStatements)) {
            \Log::info("No changes detected for table `{$tableName}`");
            return "";
        }
        
        $this->updatedTablesToBackup[] = $tableName;
        return "ALTER TABLE `{$tableName}` " . implode("\n, ", $alterStatements) . ";";
    }

    private function fetchIndexes($tableName)
    {
        $indexes = DB::select("SHOW INDEX FROM `{$tableName}`");
        // return collect($indexes)->keyBy(fn($index) => $index->Key_name);
        return collect($indexes)->groupBy('Key_name'); // Group by index name
    }

    private function fetchConstraints($tableName)
    {
        return DB::select("
            SELECT 
                tc.TABLE_NAME,
                tc.CONSTRAINT_NAME,
                tc.CONSTRAINT_TYPE,
                kcu.COLUMN_NAME,
                kcu.REFERENCED_TABLE_NAME,
                kcu.REFERENCED_COLUMN_NAME
            FROM information_schema.TABLE_CONSTRAINTS AS tc
            LEFT JOIN information_schema.KEY_COLUMN_USAGE AS kcu
            ON tc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
            AND tc.TABLE_NAME = kcu.TABLE_NAME
            WHERE tc.TABLE_NAME = ?
            AND tc.TABLE_SCHEMA = ?
        ", [$tableName, config('database.connections.mysql.database')]);
    }

    private function compareIndexes($previousIndexes, $currentIndexes)
    {
        $sql = [];

        // Detect removed indexes
        $removedIndexes = $previousIndexes->diffKeys($currentIndexes);
        foreach ($removedIndexes as $indexName => $index) {
            $this->processLogs[] = "Removed index {$indexName}.";
            $sql[] = "DROP INDEX `{$indexName}`";
        }

        // Detect added indexes
        $addedIndexes = $currentIndexes->diffKeys($previousIndexes);

        foreach ($addedIndexes as $indexName => $indexGroup) {
            // Collect column names for the index
            \Log::debug("Collect column names for the index - indexGroup: ",[$indexGroup]);
            $columns = $indexGroup->pluck('Column_name')->map(function ($column) {
                return "`{$column}`";
            })->implode(', ');
        
            // Determine index type
            $indexType = $indexGroup->first()->Index_type !== 'BTREE' ? strtoupper($indexGroup->first()->Index_type) . ' ' : '';
            
            // Add the SQL statement
            $this->processLogs[] = "Added index {$indexName}.";
            $sql[] = "ADD {$indexType}INDEX `{$indexName}` ({$columns})";
        }

        return $sql;
    }

    private function compareConstraints($previousConstraints, $currentConstraints)
    {
        $sql = [];

        // Detect removed constraints
        $removedConstraints = collect($previousConstraints)->diffKeys($currentConstraints);
        foreach ($removedConstraints as $constraint) {
            $sql[] = "ALTER TABLE `{$constraint->TABLE_NAME}` DROP CONSTRAINT `{$constraint->CONSTRAINT_NAME}`;";
        }

        // Detect added constraints
        $addedConstraints = collect($currentConstraints)->diffKeys($previousConstraints);
        foreach ($addedConstraints as $constraint) {
            if ($constraint->CONSTRAINT_TYPE === 'FOREIGN KEY') {
                $sql[] = "ALTER TABLE `{$constraint->TABLE_NAME}` ADD CONSTRAINT `{$constraint->CONSTRAINT_NAME}` FOREIGN KEY (`{$constraint->COLUMN_NAME}`) REFERENCES `{$constraint->REFERENCED_TABLE_NAME}` (`{$constraint->REFERENCED_COLUMN_NAME}`);";
            } elseif ($constraint->CONSTRAINT_TYPE === 'PRIMARY KEY') {
                $sql[] = "ALTER TABLE `{$constraint->TABLE_NAME}` ADD PRIMARY KEY (`{$constraint->COLUMN_NAME}`);";
            }
        }

        return $sql;
    }
}


