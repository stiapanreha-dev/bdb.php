<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateFromSQLite extends Command
{
    protected $signature = 'db:migrate-from-sqlite {sqlite_path?}';
    protected $description = 'Migrate data from SQLite database to current database connection';

    public function handle()
    {
        $sqlitePath = $this->argument('sqlite_path') ?? database_path('database.sqlite');

        if (!file_exists($sqlitePath)) {
            $this->error("SQLite database not found at: {$sqlitePath}");
            return 1;
        }

        $this->info("Migrating data from SQLite: {$sqlitePath}");

        config(['database.connections.sqlite_source' => [
            'driver' => 'sqlite',
            'database' => $sqlitePath,
            'prefix' => '',
        ]]);

        $tables = [
            'users' => ['id', 'name', 'email', 'phone', 'email_verified_at', 'password', 'balance', 'role', 'email_verified', 'phone_verified', 'remember_token', 'created_at', 'updated_at'],
            'news' => ['id', 'title', 'content', 'is_published', 'created_at', 'updated_at'],
            'ideas' => ['id', 'user_id', 'title', 'description', 'status', 'created_at', 'updated_at'],
            'transactions' => ['id', 'user_id', 'amount', 'payment_id', 'status', 'created_at', 'updated_at'],
        ];

        DB::beginTransaction();

        try {
            foreach ($tables as $table => $columns) {
                $this->info("Migrating table: {$table}");

                $records = DB::connection('sqlite_source')->table($table)->get();

                if ($records->isEmpty()) {
                    $this->warn("  No records found in {$table}");
                    continue;
                }

                foreach ($records as $record) {
                    $data = [];
                    foreach ($columns as $column) {
                        $data[$column] = $record->$column ?? null;
                    }
                    DB::table($table)->insert($data);
                }

                $this->info("  Migrated {$records->count()} records from {$table}");
            }

            DB::commit();
            $this->info("\nMigration completed successfully!");
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Migration failed: " . $e->getMessage());
            return 1;
        }
    }
}
