<?php
/**
 *
 * JSON: JSON file configuration type parser
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

class JSON extends AbstractConfigType implements ConfigTypeInterface
{
    /**
     * Parses the JSON configuration string and returns
     * the equivalent Helion configuration value object
     *
     * @param string $configString
     * @return HelionConfigValue
     */
    public function parseConfigString($configString) {
        if (isset($this->options['jsonOptions'])) {
            if (array_product(array_map("is_int", $this->options['jsonOptions']))) {
                $options = $this->bitmask($this->options['jsonOptions']);
            } else {
                throw new \UnexpectedValueException('Error: Wrong json options.');
            }
        } else {
            $options = 0;
        }
        $jsonElement = json_decode($configString, false, 512, $options);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \UnexpectedValueException('JSON error! ' . json_last_error_msg());
        }
        $helionConfigValue = $this->toHelionConfigValue($jsonElement, $this->configRootName);
        return $helionConfigValue;
    }

    /**
     * Generates the Helion configuration value object
     *
     * @param mixed $jsonElement
     * @param string $name
     * @return HelionConfigValue
     */
    private function toHelionConfigValue($jsonElement, $name) {
        $value = array();
        foreach ($jsonElement as $key => $leaf) {
            if (!is_object($jsonElement) && isset($jsonElement[0])) {
                $keyName = $name . '.' . $key;
            } else {
                $keyName = $key;
            }
            if (is_array($leaf) || is_object($leaf)) {
                $value[$key] = $this->toHelionConfigValue($leaf, $keyName);
            } else {
                $value[$key] = new HelionConfigValue($keyName, $leaf);
            }
        }
        $helionConfigValue = new HelionConfigValue($name, $value);
        return $helionConfigValue;
    }

}