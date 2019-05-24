<?php


namespace Nikoutel\HelionConfig;

class HelionConfigValue
{
    public $name;
    public $value;
    public $attributes;

    public function __construct($name, $value, $attributes = null) {
        $this->name = $name;
        $this->value = $value;
        $this->attributes = $attributes;
    }

}