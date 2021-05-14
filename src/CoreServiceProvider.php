<?php

namespace Ecoflow\Core;

use Ecoflow\Core\Commands\Crud\Entity;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    protected $commandsArray = [
        Commands\Checkdb::class,
        Commands\Seed::class,
        Commands\Crud\CrudCommand::class
    ];

    public function register()
    {
        if (app()->runningInConsole()) {
            $this->commands($this->commandsArray);
        }

        // app()->singleton(Entity::class, function(){
        //     return new Entity();
        // });
    }

    public function boot()
    {
    }
}
