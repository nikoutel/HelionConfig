<?php


namespace Nikoutel\HelionConfig;


trait arrayTrait
{
    public function castToArray($helionConfigValueObject) {
        if (is_scalar($helionConfigValueObject)) {
            return $helionConfigValueObject;
        }
        return array_map(array($this, 'castToArray'), array_filter((array)$helionConfigValueObject));
    }

    private function flattenArray($helionConfigArray, $sectionPrefix = '') {
        $sectionSeparator = SECTION_SEPARATOR;
        $helionConfigArrayFlatt = array();
        foreach ($helionConfigArray as $key => $value) {
            if ($key === 'name') {
                continue;
            }
            if (is_array($value)) {
                if ($key === 'value') {
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