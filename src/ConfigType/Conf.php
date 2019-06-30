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
     * @var array
     */
    private $symbolTable = array(
        'sectionStart' => "[",
        'sectionEnd' => "]",
        'equals' => "=",
        'multiLineSeparator' => "\\",
        'commentStart' => ";",
    );

    /**
     * Parses the Generic type configuration string and returns
     * the equivalent Helion configuration value object
     *
     * @param string $configString
     * @return HelionConfigValue
     */
    public function parseConfigString($configString) {

        $previousLine = '';
        $confArrayResult = array();
        $section = null;
        $symbols = $this->getSymbols();

        $configArray = preg_split('/$\R?^/m', $configString); // Split $configString by new line
        foreach ($configArray as $configLine) {
            if (!preg_match("/^\s*" . $symbols['commentStart'] . "/", $configLine) && preg_match("/^\s*(.*)" . $symbols['multiLineSeparator'] . "\s*$/", $configLine, $configMatches)) {
                $previousLine .= $configMatches[1] . ' ';
                continue;
            }
            if (!empty($previousLine)) {
                $configLine = $previousLine . trim($configLine);
                $previousLine = '';
            }
            if (preg_match("/^\s*(.*?)\s*" . $symbols['equals'] . "\s*((.*?)|)\s*$/", $configLine, $configMatches)) {
                if (!isset($section)) {
                    $confArrayResult[$configMatches[1]] = $configMatches[2];
                } else {
                    $confArrayResult[$section][$configMatches[1]] = $configMatches[2];
                }
            }
            if (preg_match("/^\s*" . $symbols['sectionStart'] . "\s*(.*)\s*" . $symbols['sectionEnd'] . "\s*$/", $configLine, $configMatches)) {
                $section = $configMatches[1];
                $confArrayResult[$section] = array();
            }
        }

        $helionConfigValue = $this->toHelionConfigValue($confArrayResult, $this->configRootName);
        return $helionConfigValue;
    }


    /**
     * Sets the generic Conf symbols
     *
     * @return array
     */
    private function getSymbols() {
        $symbols = $this->symbolTable;
        if (isset($this->options['genericConf']['sectionStart'])) {
            $symbols['sectionStart'] = $this->options['genericConf']['sectionStart'];
            $symbols['sectionEnd'] = '';
        }
        if (isset($this->options['genericConf']['sectionEnd'])) {
            $symbols['sectionEnd'] = $this->options['genericConf']['sectionEnd'];
        }
        if (isset($this->options['genericConf']['equals'])) {
            $symbols['equals'] = $this->options['genericConf']['equals'];
        }
        if (isset($this->options['genericConf']['multiLineSeparator'])) {
            $symbols['multiLineSeparator'] = $this->options['genericConf']['multiLineSeparator'];
        }
        if (isset($this->options['genericConf']['commentStart'])) {
            $symbols['commentStart'] = $this->options['genericConf']['commentStart'];
        }

        return $this->prep($symbols);
    }

    /**
     * Prepares pattern part for regex
     *
     * @param array $symbols
     * @return array
     */
    private function prep(array $symbols) {
        $symbolsPrepd = array();
        foreach ($symbols as $key => $symbol) {
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