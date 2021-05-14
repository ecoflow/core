<?php

namespace Ecoflow\Core\Commands\Crud;

use Exception;

class Field
{

    public string $name;

    public string $type;

    /** TODO: apres le nom et le type tout le rest est des options **/
    public array $options;

    /** TODO: **/
    public array $foreigns = [];

    public function __construct(string $string, CrudCommand $command)
    {
        $ex = explode('#', trim($string));

        $name = trim($ex[0]);
        $type = trim($ex[1]);
        $options = [];

        // get options
        for($index = 2; $index < count($ex); $index++){
            $ex[$index] = trim($ex[$index]);
            if($this->valideOption($ex[$index])) array_push($options, $ex[$index]);
                else throw new Exception("Indefined option $ex[$index]");
        }

        // get type
        if ($this->valideType($type)) $this->type = $type;
            else throw new Exception("Undefined type $type");

        // set name
        $this->name = $name;
        // set options
        $this->options = $options;
    }


    private function valideOption($option) {
        $_OPTIONS = ['notnull', 'unsigned'];
        return in_array($option, $_OPTIONS);
    }

    private function valideType($type) {
        $_TYPES = ['string', 'text', 'boolean', 'tinyInt', 'unsignedBigInteger', 'foreignId'];
        return in_array($type, $_TYPES);
    }

}