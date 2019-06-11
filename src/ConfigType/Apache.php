<?php
/**
 *
 * Apache: Apache file configuration type parser
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

class Apache extends ConfigType implements ConfigTypeInterface
{
    /**
     * Parses the Apache configuration string and returns
     * the equivalent Helion configuration value object
     *
     * @param string $configString
     * @return HelionConfigValue
     */
    public function parseConfigString($configString) {

        $apacheConfigArray = preg_split('/$\R?^/m', $configString); // Split $configString by new line
        $result = array();
        $block = array();
        $level = $lastLevel = 0;
        $previousLine = '';
        $sectionName = array();
        foreach ($apacheConfigArray as $configLine) {
            if (!preg_match('/^\s*#/', $configLine) && preg_match('/^\s*(.*)\s+\\\$/', $configLine, $configMatches)) { // Multiple lines
                $previousLine .= $configMatches[1] . ' ';
                continue;
            }
            if (!empty($previousLine)) {
                $configLine = $previousLine . trim($configLine);
                $previousLine = '';
            }
            if (preg_match('/^\s*(\w+)(?:\s+(.*?)|)\s*$/', $configLine, $configMatches)) { // Property
                if (!isset($configMatches[2])) {
                    throw new \UnexpectedValueException("Apache config format error!");
                }
                if ($level === 0) {
                    $block[$configMatches[1]] = $configMatches[2];
                    $result = $this->append($block, $result);
                    $block = array();
                } else {
                    $blockChild[$configMatches[1]] = $configMatches[2];
                }
            }
            if (preg_match('/^\s*<(\w+)(?:\s+([^>]*)|\s*)>\s*$/', $configLine, $configMatches)) { // Section start
                $level++;
                if (!isset($configMatches[2])) {
                    throw new \UnexpectedValueException("Apache config format error!");
                }
                $sectionName[] = $configMatches[1];
                if ($level === $lastLevel) {
                    $section = array($configMatches[1] => array('@attributes' => $configMatches[2]));
                    if (!isset($blockChild)) {
                        $block = $this->append($section, $block);
                        $blockChild = &$block[$configMatches[1]];
                    } else {
                        $blockChild = $this->append($section, $blockChild);
                        if (array_key_exists(0, $blockChild[$configMatches[1]])) {
                            $blockChild = &$blockChild[$configMatches[1]][count($blockChild[$configMatches[1]]) - 1];
                        } else {
                            $blockChild = &$blockChild[$configMatches[1]];
                        }
                    }
                } else {
                    if (empty($block)) {
                        $block[$configMatches[1]] = array('@attributes' => $configMatches[2]);
                        $blockChild = &$block[$configMatches[1]];
                    } else {
                        $blockChild[$configMatches[1]] = array('@attributes' => $configMatches[2]);
                        $blockParent[$level] = &$blockChild;
                        $blockChild = &$blockChild[$configMatches[1]];
                    }

                }
                $lastLevel = $level;
            }
            if (preg_match('/^\s*<\/(\w+)\s*>\s*$/', $configLine, $configMatches)) { // Section end
                $previousSectionName = array_pop($sectionName);
                if ($previousSectionName !== $configMatches[1]) {
                    throw new \UnexpectedValueException("Apache config format error! Section '$configMatches[1]' not closed");
                }
                unset($blockChild);
                $blockChild = &$blockParent[$level];
                if ($level === 1) {
                    $result = $this->append($block, $result);
                    $block = array();
                }
                $level--;
            }
        }
        $helionConfigValue = $this->toHelionConfigValue($result, $this->configRootName);
        return $helionConfigValue;
    }

    /**
     * Appends the current nested section block to the previous blocks in result
     *
     * @param array $block
     * @param array $result
     * @return array
     */
    private function append($block, $result) {
        $key = key($block);
        if (!array_key_exists($key, $result)) {
            $result = array_merge($result, $block);
        } else {
            if (!is_array($result[$key]) || !array_key_exists(0, $result[$key])) {
                $result[$key] = array($result[$key]);
            }
            $result[$key][] = $block[$key];
        }
        return $result;
    }

    /**
     * Generates the Helion configuration value object
     *
     * @param array $apacheConfigArray
     * @param string $name
     * @return HelionConfigValue
     */
    private function toHelionConfigValue(array $apacheConfigArray, $name) {
        $value = array();
        $attribute = null;
        if (array_key_exists('@attributes', $apacheConfigArray)) {
            $attribute = $apacheConfigArray['@attributes'];
            unset($apacheConfigArray['@attributes']);
        }
        foreach ($apacheConfigArray as $key => &$leaf) {
            if (isset($apacheConfigArray[0])) {
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
        $helionConfigValue = new HelionConfigValue($name, $value, $attribute);
        return $helionConfigValue;
    }
}
