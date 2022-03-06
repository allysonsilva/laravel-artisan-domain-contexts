<?php

namespace Allyson\ArtisanDomainContext\Commands\Database\Migrations\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @codeCoverageIgnore
 */
trait RunInMultiDatabases
{
    /**
     * Change the database connection configuration.
     *
     * @param string $database
     *
     * @return void
     */
    protected function connectUsingDatabase(string $database): void
    {
        config(['database.connections.mysql.database' => $database]);

        DB::purge();
    }

    /**
     * Run migrations in given databases.
     *
     * @param string[] $databases
     *
     * @return void
     */
    protected function runInDatabases(array $databases): void
    {
        $defaultDatabase = config('database.connections.mysql.database');

        foreach ($databases as $database) {
            $this->connectUsingDatabase($database);

            $this->line('');
            $this->comment('Database: ' . $database);
            $this->line('');

            parent::handle();
        }

        if (empty($databases)) {
            $this->line('');
            $this->comment("No database found in `config('context.migrations.databases')`");
        }

        config(['database.connections.mysql.database' => $defaultDatabase]);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function multiDatabasesHandle(): int
    {
        $optionName = 'multi-databases';

        if ($this->hasOption($optionName) && boolval($this->option($optionName))) {
            $this->runInDatabases(Arr::wrap(config('context.migrations.databases')));

            return 0;
        }

        /** @phpstan-ignore-next-line */
        return (int) parent::handle();
    }
}
