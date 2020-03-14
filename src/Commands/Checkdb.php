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
     * List of errors
     *
     * @var array
     */
    protected array $errors = [];

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
     * List of all tables of the EcoFlow ecosystem
     *
     * @var mixed
     */
    protected $db = null;

    /**
     * List of DB tables path
     *
     * @var string
     */
    protected $db_path = __DIR__ . '/../db.json';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        try {
            $file = File::get($this->db_path);
            $this->db = json_decode($file);
            $this->db = $this->db->tables;
        } catch (\Exception $e) {
            die("db file empty ! Please create a db.json file \n");
        }

        $this->packages = $this->getInstalledPackages();
        $this->flowpackages = $this->getPackageName($this->getFlowPackages($this->packages));

        // if no Ecoflow package is installed
        if (!count($this->flowpackages)) {
            return $this->error("\n We can't find any ecoflow package");
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tables = $this->tables($this->flowpackages);
        if (count($tables)) {
            $this->errors = $this->checkTablesIfExists($tables);
            $this->display();
        }
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
     * Get tables list of installed packages
     *
     * @param array $packages
     * @return array
     */
    protected function tables(array $packages): array
    {
        $tables = [];
        for ($i = 1; $i <= count($packages); $i++) {
            if (isset($this->db->{$packages[$i]})) {
                $tables[$packages[$i]] = [$packages[$i], $this->db->{$packages[$i]}];
            }
        }
        return $tables;
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

        foreach ($tables as $packages) {
            foreach ($packages[1] as $table) {
                Schema::hasTable($table) ? '' : array_push($errors, [$packages[0], $table]);
            }
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
