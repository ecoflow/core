<?php

namespace Ecoflow\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

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
     * All packages installed
     * 
     * @var array
     */
    protected array $packages = [];

    /**
     * Ecoflow packages installed
     * 
     * @var array
     */
    protected array $flowpackages = [];

    /**
     * Tables found in ecoflow migrations files (will test against it)
     *
     * @var array
     */
    protected array $tablesInMigration = [];

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

        // get all packages installed
        $this->packages = $this->getInstalledPackages();
        // get package names only for ecoflow pkgs
        $this->flowpackages = $this->getPackageName($this->getFlowPackages($this->packages));

        // if no Ecoflow package is installed
        if (!count($this->flowpackages)) {
            return $this->error("\n We can't find any ecoflow package");
        }

        foreach ($this->flowpackages as $pack) {
            $file = "." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "ecoflow" . DIRECTORY_SEPARATOR . "$pack" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "Database" . DIRECTORY_SEPARATOR . "migrations";

            if (file_exists($file)) {
                $migrations = scandir($file);
                foreach ($migrations as $mig) {
                    if (preg_match('/create_(\w*)_table/', $mig, $matches)) {
                        array_push($this->tablesInMigration, [$pack, $matches[1]]);
                    }
                }
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->errors = $this->checkTablesIfExists($this->tablesInMigration);
        $this->display();
    }

    /**
     * Return all required packages in packages.json file
     *
     * @return array
     */
    protected function getInstalledPackages(): array
    {
        $composer = json_decode(File::get('composer.json'));
        return array_keys(get_object_vars($composer->require));
    }

    /**
     * Get EcoFlow packages with vendor ecoflow/<pkg-name> from a list of packages
     *
     * @param array $pkgs
     * @return void
     */
    protected function getFlowPackages(array $pkgs): array
    {
        return array_filter($pkgs, function ($package) {
            return explode('/', $package)[0] === "ecoflow";
        });
    }

    /**
     * Get package name from a string or array(vendor/name)
     *
     * @param array|mixed $packages
     * @return array
     */
    protected function getPackageName($packages): array
    {
        if (is_array($packages)) {
            return array_map(function ($package) {
                return explode('/', $package)[1];
            }, $packages);
        }

        return explode('/', $packages)[1];
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
