<?php
/**
 *
 * Conf: generic configuration type parser
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

class Conf extends ConfigType implements ConfigTypeInterface
{
    /**
     * Parses the Generic type configuration string and returns
     * the equivalent Helion configuration value object
     *
     * @param string $configString
     * @return HelionConfigValue
     */
    public function parseConfigString($configString) {

        $symb['sectionStart'] = "[";
        $symb['sectionEnd'] = "]";
        $symb['equals'] = ':';
        $symb['multiLineSeparator'] = '\\';
        $symb['commentStart'] = '#';

        $symb = $this->prep($symb);

        $previousLine = '';
        $confArrayResult = array();
        $section = null;

        $configArray = preg_split('/$\R?^/m', $configString); // Split $configString by new line
        foreach ($configArray as $configLine) {
            if (!preg_match("/^\s*" . $symb['commentStart'] . "/", $configLine) && preg_match("/^\s*(.*)" . $symb['multiLineSeparator'] . "\s*$/", $configLine, $configMatches)) {
                $previousLine .= $configMatches[1] . ' ';
                continue;
            }
            if (!empty($previousLine)) {
                $configLine = $previousLine . trim($configLine);
                $previousLine = '';
            }
            if (preg_match("/^\s*([\w-]+)\s*" . $symb['equals'] . "\s*((.*?)|)\s*$/", $configLine, $configMatches)) {
                if (!isset($section)) {
                    $confArrayResult[$configMatches[1]] = $configMatches[2];
                } else {
                    $confArrayResult[$section][$configMatches[1]] = $configMatches[2];
                }
            }
            if (preg_match("/^\s*" . $symb['sectionStart'] . "\s*(.*)\s*" . $symb['sectionEnd'] . "\s*$/", $configLine, $configMatches)) {
                $section = $configMatches[1];
                $confArrayResult[$section] = array();
            }
        }

        $helionConfigValue = $this->toHelionConfigValue($confArrayResult, $this->configRootName);
        return $helionConfigValue;
    }

    /**
     * Prepares pattern part for regex
     *
     * @param array $symbols
     * @return array
     */
    private function prep(array $symbols){
        $symbolsPrepd = array();
        foreach ($symbols as $key => $symbol){
            $symbolsPrepd[$key] = preg_replace('/(.)/', '\\\${1}', $symbol);
        }
        return $symbolsPrepd;
    }
    
    /**
     * Generates the Helion configuration value object
     *
     * @param array $confArrayResult
     * @param string $name
     * @return HelionConfigValue
     */
    private function toHelionConfigValue(array $confArrayResult, $name) {
        $value = array();
        foreach ($confArrayResult as $key => $leaf) {
            if (is_array($leaf)) {
                $value[$key] = $this->toHelionConfigValue($leaf, $key);
            } else {
                if (isset($confArrayResult[0])) {
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