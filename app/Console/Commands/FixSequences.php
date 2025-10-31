<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSequences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fix-sequences';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix PostgreSQL sequences after data migration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->error('This command is only for PostgreSQL databases.');
            return 1;
        }

        $this->info('Fixing PostgreSQL sequences...');

        // Get all tables with id column
        $tables = DB::select("
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = 'public'
            AND table_type = 'BASE TABLE'
            ORDER BY table_name
        ");

        $fixed = 0;
        $skipped = 0;

        foreach ($tables as $table) {
            $tableName = $table->table_name;

            // Check if table has id column
            $hasId = DB::select("
                SELECT column_name
                FROM information_schema.columns
                WHERE table_name = ? AND column_name = 'id'
            ", [$tableName]);

            if (empty($hasId)) {
                continue;
            }

            // Check if sequence exists
            $sequenceName = "{$tableName}_id_seq";
            $sequenceExists = DB::select("
                SELECT sequence_name
                FROM information_schema.sequences
                WHERE sequence_name = ?
            ", [$sequenceName]);

            if (empty($sequenceExists)) {
                continue;
            }

            // Get max id from table
            $result = DB::select("SELECT MAX(id) as max_id FROM {$tableName}");
            $maxId = $result[0]->max_id ?? 0;

            if ($maxId === null) {
                $this->line("  <comment>Skipped:</comment> {$tableName} (no records)");
                $skipped++;
                continue;
            }

            // Get current sequence value
            $seqResult = DB::select("SELECT last_value FROM {$sequenceName}");
            $currentSeq = $seqResult[0]->last_value;

            if ($currentSeq >= $maxId) {
                $this->line("  <comment>OK:</comment> {$tableName} (seq: {$currentSeq}, max: {$maxId})");
                $skipped++;
            } else {
                // Fix sequence
                DB::statement("SELECT setval('{$sequenceName}', COALESCE((SELECT MAX(id) FROM {$tableName}), 1))");
                $this->line("  <info>Fixed:</info> {$tableName} (was: {$currentSeq}, now: {$maxId})");
                $fixed++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("  Fixed: {$fixed}");
        $this->info("  OK/Skipped: {$skipped}");

        return 0;
    }
}
