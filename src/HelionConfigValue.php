<?php


namespace Nikoutel\HelionConfig;

final class HelionConfigValue
{
    use arrayTrait;

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

    public function asArrayFlat() {
        return $this->flattenArray($this->castToArray($this));
    }
}