<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SqlCommand extends Command
{
    protected $signature = 'sql
        {query? : SQL query to execute}
        {--file= : Execute SQL from a file}
        {--limit=50 : Limit rows for SELECT queries}
        {--no-limit : Disable row limit}';

    protected $description = 'Execute SQL queries directly from the command line';

    public function handle(): int
    {
        $query = $this->argument('query');
        $file = $this->option('file');

        if ($file) {
            return $this->executeFile($file);
        }

        if ($query) {
            return $this->executeQuery($query);
        }

        return $this->interactiveMode();
    }

    private function executeQuery(string $query): int
    {
        $query = trim($query, "; \t\n\r");

        try {
            $type = strtoupper(strtok($query, ' '));

            if (in_array($type, ['SELECT', 'SHOW', 'DESCRIBE', 'DESC', 'EXPLAIN'])) {
                return $this->executeSelect($query);
            }

            $affected = DB::affectingStatement($query);
            $this->info("Query OK, {$affected} row(s) affected.");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("ERROR: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    private function executeSelect(string $query): int
    {
        $limit = $this->option('no-limit') ? null : (int) $this->option('limit');

        if ($limit && !preg_match('/\bLIMIT\b/i', $query)) {
            $query .= " LIMIT {$limit}";
        }

        $results = DB::select($query);

        if (empty($results)) {
            $this->info('Empty set (0 rows).');

            return self::SUCCESS;
        }

        $headers = array_keys((array) $results[0]);
        $rows = array_map(fn($row) => array_map(
            fn($v) => is_null($v) ? 'NULL' : (string) $v,
            (array) $row
        ), $results);

        $this->table($headers, $rows);
        $this->info(count($results) . ' row(s) returned.');

        return self::SUCCESS;
    }

    private function executeFile(string $file): int
    {
        if (!file_exists($file)) {
            $this->error("File not found: {$file}");

            return self::FAILURE;
        }

        $sql = file_get_contents($file);
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        $this->info("Executing " . count($statements) . " statement(s) from {$file}...");

        foreach ($statements as $i => $stmt) {
            if (empty($stmt)) {
                continue;
            }

            $this->line('');
            $this->comment('> ' . \Illuminate\Support\Str::limit($stmt, 80));
            $this->executeQuery($stmt);
        }

        return self::SUCCESS;
    }

    private function interactiveMode(): int
    {
        $this->info('MySQL Interactive Mode (Laravel). Type "exit" to quit.');
        $this->line('Database: ' . config('database.connections.' . config('database.default') . '.database'));
        $this->line('');

        while (true) {
            $query = $this->ask('mysql>');

            if (is_null($query) || in_array(strtolower(trim($query)), ['exit', 'quit', 'q'])) {
                $this->info('Bye.');
                break;
            }

            if (empty(trim($query))) {
                continue;
            }

            $this->executeQuery($query);
        }

        return self::SUCCESS;
    }
}
