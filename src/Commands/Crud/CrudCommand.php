<?php

namespace Ecoflow\Core\Commands\Crud;

use Exception;
use Illuminate\Console\Command;
use Ecoflow\Core\Commands\Crud\Entity;
use Ecoflow\Core\Commands\Crud\Builder;

class CrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ecoflow:crud {package} {class} {--fields=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a crud on a Ecoflow package.';

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
    public function handle()
    {
        $class = trim($this->argument('class'));
        $package = trim($this->argument('package'));
        $fields = $this->generateFields($this->option('fields'));

        new Builder(new Entity($package, $class, $fields));
    }

    /**
     * Generate Field Objects depends on option 'fields'.
     *
     * @param string $string
     * @return array
     */
    private function generateFields(string $string): array
    {
        $fields = [];

        foreach (explode(';', $string) as $field) {
            try {
                array_push($fields, new Field(trim($field), $this));
            } catch(Exception $e) {
                $this->error($e->getMessage());
                exit();
            }
        }

        return $fields;
    }

}
