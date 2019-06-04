<?php
/**
 *
 * INI: INI file configuration type parser
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

namespace Nikoutel\HelionConfig\ConfigType;

use Nikoutel\HelionConfig\HelionConfigValue;

class INI extends ConfigType implements ConfigTypeInterface
{

    /**
     * Parses the ΙΝΙ configuration string and returns
     * the equivalent Helion configuration value object
     *
     * @param string $configString
     * @return HelionConfigValue
     */
    public function parseConfigString($configString) {
        $iniArray = parse_ini_string($configString, true);
        if ($iniArray === false || empty($iniArray)) {
            throw new \UnexpectedValueException('INI format error!');
        }
        $helionConfigValue = $this->toHelionConfigValue($iniArray, $this->configRootName);
        return $helionConfigValue;
    }

    /**
     * Generates the Helion configuration value object
     *
     * @param array $iniArray
     * @param string $name
     * @return HelionConfigValue
     */
    private function toHelionConfigValue(array $iniArray, $name) {
        $value = array();
        foreach ($iniArray as $key => $leaf) {
            if (is_array($leaf)) {
                $value[$key] = $this->toHelionConfigValue($leaf, $key);
            } else {
                if (isset($iniArray[0])) {
                    $keyName = $name . '.' . $key;
                } else {
                    $keyName = $key;
                }
                $value[$key] = new HelionConfigValue($keyName, $leaf);
            }
        }
        $helionConfigValue = new HelionConfigValue($name, $value);
        return $helionConfigValue;
    }
}