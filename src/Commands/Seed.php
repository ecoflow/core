<?php

namespace Ecoflow\Core\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Seed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ecoflow:seed {module : Name of the module} {table : Name of the talbe (plural)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the datatable';

    /**
     * Namespace of the seeder.
     *
     * @var string
     */
    protected $pathMask = "Ecoflow\Module\Database\seeds";

    protected $class = '';

    /**
     * __construct
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
        $this->module = Str::singular(ucfirst($this->argument('module')));

        $this->table = Str::plural(ucfirst($this->argument('table')));

        $this->class = Str::replaceFirst('Module', $this->module, $this->pathMask) . "\\$this->table" . "TableSeeder";

        if (class_exists($this->class)) {
            Artisan::call('db:seed', [
                '--class' => $this->class,
            ]);
            $this->info("$this->class seed with success !");
        } else {
            $this->error("ERR: Can't find " . $this->class);
        }
    }
}
