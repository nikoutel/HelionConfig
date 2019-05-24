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

    public function asArrayFlat() {
        return $this->flattenArray($this->castToArray($this));
    }

    private function castToArray($helionConfigValueObject) {
        if (is_scalar($helionConfigValueObject)) {
            return $helionConfigValueObject;
        }
        return array_map(array($this, 'castToArray'), array_filter((array)$helionConfigValueObject));
    }

    private function flattenArray($helionConfigArray, $sectionPrefix = '') {
        $sectionSeparator = '.';
        $helionConfigArrayFlatt = array();
        foreach ($helionConfigArray as $key => $value) {
            if ($key == 'name') {
                continue;
            }
            if (is_array($value)) {
                if ($key == 'value') {
                    $helionConfigArrayFlatt = $helionConfigArrayFlatt + $this->flattenArray($value, $sectionPrefix);
                } else {
                    $helionConfigArrayFlatt = $helionConfigArrayFlatt + $this->flattenArray($value, $sectionPrefix . $key . $sectionSeparator);
                }
            } else {
                $helionConfigArrayFlatt[rtrim($sectionPrefix, $sectionSeparator)] = $value;
            }
        }
        return $helionConfigArrayFlatt;
    }
}