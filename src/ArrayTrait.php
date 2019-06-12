<?php
/**
 *
 * ArrayTrait: Array modification methods
 *
 *
 * @package HelionConfig
 * @author Nikos Koutelidis nikoutel@gmail.com
 * @copyright 2019 Nikos Koutelidis
 * @license http://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 *
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 *
 */

namespace Nikoutel\HelionConfig;

trait ArrayTrait
{
    /**
     * Casts a Helion configuration object to an array
     *
     * @param string|HelionConfigValue $helionConfigValue
     * @return string|array
     */
    public function castToArray($helionConfigValue) {
        if (is_scalar($helionConfigValue)) {
            return $helionConfigValue;
        }
        return array_map(array($this, 'castToArray'), array_filter((array)$helionConfigValue, function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        }));
    }

    /**
     * Flattens an array
     *
     * @param array $helionConfigArray
     * @param string $sectionPrefix
     * @param bool $isAttribute
     * @return array
     */
    private function flattenArray($helionConfigArray, $sectionPrefix = '', $isAttribute = false) {
        $sectionSeparator = SECTION_SEPARATOR;
        $helionConfigArrayFlatt = array();
        foreach ($helionConfigArray as $key => $value) {
            if ($key === 'helionConfigName') {
                continue;
            }
            if ($key == 'helionConfigAttributes') {
                $key = "@attribute";
                $isAttribute = true;
            }
            if (is_array($value)) {
                if ($key === 'helionConfigValue') {
                    $helionConfigArrayFlatt = $helionConfigArrayFlatt + $this->flattenArray($value, $sectionPrefix);
                } elseif ($key == 'helionConfigAttributes') {
                    $helionConfigArrayFlatt = $helionConfigArrayFlatt + $this->flattenArray($value, $sectionPrefix . $key . $sectionSeparator, true);
                } else {
                    $helionConfigArrayFlatt = $helionConfigArrayFlatt + $this->flattenArray($value, $sectionPrefix . $key . $sectionSeparator);
                }
            } else {
                if (!$isAttribute) {
                    $helionConfigArrayFlatt[rtrim($sectionPrefix, $sectionSeparator)] = $value;
                } else {
                    $helionConfigArrayFlatt[rtrim($sectionPrefix . $key, $sectionSeparator)] = $value;
                }
            }
        }
        return $helionConfigArrayFlatt;
    }
}