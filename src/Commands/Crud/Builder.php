<?php

namespace Ecoflow\Core\Commands\Crud;

use Illuminate\Support\Str;

class Builder
{
    /** Entity @var Entity */
    protected $entity;

    /** Model @var string */
    protected string $model;

    /** Controller @var string */
    protected $controller;

    /** Migration @var string */
    protected $migration;

    /** Repository @var string */
    protected $repository;

    public function __construct(Entity $entity)
    {
        /**** Entity ****/
        $this->entity = $entity;


        /**** Make the Migration ****/
        $this->makeMigration();

        /**** Make the Model ****/
        $this->makeModel();

        /**** Repository ****/
        $this->makeRepository();

        /**** Make the Controller ****/
        $this->makeController();


        /**** Make the Routes ****/
        // For routes we need to grab the existing routes file of the package
        // and try to append a new resource route.

        /**** Request ****/
        // TODO: make a StoreRequest and a UpdateRequest


        dd('DONE');

    }

    /**
     * Create the migration string file.
     *
     * @return void
     */
    private function makeMigration(): void
    {
        $migration =  str_replace(
            ['@class_p_uc@', '@class_p_lc@', '@fields_as_migration@'],
            [
                Str::plural($this->entity->class), Str::plural($this->entity->class_lc),
                $this->entity->getFieldsAsMigration()
            ],
            file_get_contents(__DIR__ . '/stubs/migration.stub')
        );

        file_put_contents(
            $this->entity->getMigrationPath(),
            $migration
        );
    }

    /**
     * Return a Model string File with correct values.
     *
     * @return string
     */
    private function makeModel(): void
    {
        $model =  str_replace(
            ['@package_s_uc@', '@class_s_uc@', '@fillables@'],
            [$this->entity->package, $this->entity->class, $this->entity->getFieldsAsFillable()],
            file_get_contents(__DIR__ . '/stubs/Model.stub')
        );

        file_put_contents(
            $this->entity->getModelPath(),
            $model
        );
    }

    /**
     * Create the Repository String file.
     *
     * @return string
     */
    private function makeRepository()
    {
        
        $repo =  str_replace(
            ['@namepsace@', '@class_uc@'],
            [$this->entity->getNamespace(), $this->entity->class],
            file_get_contents(__DIR__ . '/stubs/Repository.stub')
        );

        // check if folder Repositories exists or not
        if (!file_exists($this->entity->getSrc() . '/Repositories')) {
            mkdir($this->entity->getSrc() . '/Repositories', 0777, true);
        }

        file_put_contents(
            $this->entity->getRepositoryPath(),
            $repo
        );
    }

    /**
     * Return a Controller string file with correct value.
     *
     * @return void
     */
    private function makeController(): void
    {
        $controller =  str_replace(
            ['@namespace@', '@class_s_uc@'],
            [$this->entity->getNamespace(), $this->entity->class],
            file_get_contents(__DIR__ . '/stubs/Controller.stub')
        );

        // check if folder Controllers exists or not
        if (!file_exists($this->entity->getSrc() . "/Controllers")) {
            mkdir($this->entity->getSrc() . "/Controllers", 0777, true);
        }

        if (!file_exists($this->entity->getSrc() . "/Controllers/" . $this->entity->class)) {
            mkdir($this->entity->getSrc() . "/Controllers/" . $this->entity->class, 0777, true);
        }

        file_put_contents(
            $this->entity->getControllerPath(),
            $controller
        );
    }

    

}