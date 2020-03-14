<?php

namespace Ecoflow\Core;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    protected $commandsArray = [Commands\Checkdb::class];

    public function register()
    {
        if (app()->runningInConsole()) {
            $this->commands($this->commandsArray);
        }
    }

    public function boot()
    {
    }
}
