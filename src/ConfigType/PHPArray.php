<?php
/**
 *
 * PHPArray: PHP array file configuration type parser
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

class PHPArray extends ConfigType implements ConfigTypeInterface
{
    /**
     * Parses the PHPArray configuration string and returns
     * the equivalent Helion configuration value object
     *
     * @param string $configString
     * @return HelionConfigValue
     */
    public function parseConfigString($configString) {
        $configString = preg_replace('/(<\?php|<\?|\?>)/i', '', $configString);
        $phpArray = eval($configString);  // What do you mean eval is evil? I don't see any eval! Ok.. @todo, but don't tell anybody
        if (!is_array($phpArray)) {
            throw new \UnexpectedValueException('PHPArray format error!');
        }
        $helionConfigValue = $this->toHelionConfigValue($phpArray, $this->configRootName);
        return $helionConfigValue;
    }

    /**
     * Generates the Helion configuration value object
     *
     * @param array $phpArray
     * @param string $name
     * @return HelionConfigValue
     */
    private function toHelionConfigValue(array $phpArray, $name) {
        $value = array();
        foreach ($phpArray as $key => $leaf) {
            if (isset($phpArray[0])) {
                $keyName = $name . '.' . $key;
            } else {
                $keyName = $key;
            }
            if (is_array($leaf)) {
                $value[$key] = $this->toHelionConfigValue($leaf, $keyName);
            } else {
                $value[$key] = new HelionConfigValue($keyName, $leaf);
            }
        }
        $helionConfigValue = new HelionConfigValue($name, $value);
        return $helionConfigValue;
    }
}
