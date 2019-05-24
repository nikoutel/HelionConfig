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

    public function asArray() {
        return $this->castToArray($this);
    }

    private function castToArray($helionConfigValueObject) {
        if (is_scalar($helionConfigValueObject)) {
            return $helionConfigValueObject;
        }
        return array_map(array($this, 'castToArray'), (array)$helionConfigValueObject);
    }
}