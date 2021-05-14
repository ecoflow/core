<?php

namespace Ecoflow\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Ecoflow\Core\Configuration\ComposerConfiguration;

class Checkdb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ecoflow:checkdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if all ecoflow tables are created';

    /**
     * List of errors
     *
     * @var array
     */
    protected array $errors = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ComposerConfiguration $config)
    {
        $this->tables = $this->getTableMigrations($config->ecoflowPackageName);
        $this->errors = $this->checkTablesIfExists($this->tables);
        $this->display();
    }


    /**
     * Get table names of given packages
     *
     * @param array $packages
     * @return array
     */
    public function getTableMigrations(array $packages): array
    {
        $tablesInMigration = [];
        foreach ($packages as $pack) {
            $file = "." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "ecoflow" . DIRECTORY_SEPARATOR . "$pack" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "Database" . DIRECTORY_SEPARATOR . "migrations";
            if (file_exists($file)) {
                $migrations = scandir($file);
                foreach ($migrations as $mig) {
                    if (preg_match('/create_(\w*)_table/', $mig, $matches)) {
                        array_push($tablesInMigration, [$pack, $matches[1]]);
                    }
                }
            }
        }
        return $tablesInMigration;
    }

    /**
     * Check if table exists in DB
     *
     * @param array $tables
     * @return array
     */
    protected function checkTablesIfExists(array $tables): array
    {
        $errors = [];

        foreach ($tables as $table) {
            Schema::hasTable($table[1]) ? '' : array_push($errors, [$table[0], $table[1]]);
        }
        return $errors;
    }

    /**
     * Display result of the checking
     *
     * @return void
     */
    protected function display()
    {
        if (count($this->errors) !== 0) {
            foreach ($this->errors as $error) {
                $this->error("$error[0].$error[1] don't exists");
            }
        } else {
            $this->info("All tables exists in db");
        }
        return;
    }
}
