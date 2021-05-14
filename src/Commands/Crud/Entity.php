<?php

namespace Ecoflow\Core\Commands\Crud;

use Illuminate\Support\Str;

class Entity
{
    /** Package name @var string */
    public string $package;
    /** Package lowercase @var string */
    public string $package_lc;

    /** Class name @var string */
    public string $class;
    /** Class lowercase @var string */
    public string $class_lc;

    /** array of Filed in Entity @var array[Entity] */
    public array $fields = [];

    public function __construct(string $package = '', string $class = '', array $fields = [])
    {
        $this->fields = $fields;
        $this->class = ucfirst($class);
        $this->package = ucfirst($package);
        $this->package_lc = strtolower($this->package);
        $this->class_lc = Str::plural(strtolower($this->class));
    }

    /** Get src path  */
    public function getSrc() {
        return base_path("packages/ecoflow/$this->package_lc/src");
    }

    /** Get formal namespace */
    public function getNamespace() {
        return "Ecoflow\\$this->package";
    }

    /** Get formal model namespace */
    public function getModelNamepace() {
        return $this->getNamespace() . "\\Models\\$this->class";
    }

    /**
     * Return fields name as a String Arrayfied.
     * ex: ['id', 'name', 'description']
     *
     * @return string
     */
    public function getFieldsAsFillable(): string
    {
        $string = '[ ';
       
        foreach ($this->fields as $index => $field) {
            if($index === count($this->fields) - 1) $string .= "'$field->name'";
            else $string .= "'$field->name', ";
        }
        $string .= ' ]';

        return $string;
    }

    /** Get field list for migration file */
    public function getFieldsAsMigration()
    {
        $migration = '';
        foreach ($this->fields as $field) {
            $migration .= '$table->' . $field->type . "('$field->name');\n\t\t\t";
            // $migration .= '$table->' . $field->type . "( '" . $field->name .' "); \n\t\t\t";
        }

        return $migration;
    }

    /** Get migration path */
    public function getMigrationPath():string
    {
        return $this->getSrc() . '/Database/migrations/' . date('Y_m_d_His') . '_create_' . Str::plural(strtolower($this->class_lc)) . "_table.php";
    }

    /** Get model path */
    public function getModelPath()
    {
        return $this->getSrc() . "/Models/$this->class.php";
    }

    /** Get repository path */
    public function getRepositoryPath()
    {
        return $this->getSrc() . "/Repositories/$this->class" ."Repository.php";
    }

    /** Get Controller path */
    public function getControllerPath()
    {
        return $this->getSrc() . "/Controllers/$this->class/$this->class" ."Controller.php";
    }
}