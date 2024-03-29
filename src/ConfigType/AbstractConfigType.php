<?php
/**
 *
 * ConfigType: Configuration type parser parent
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

abstract class AbstractConfigType
{
    /**
     * @var string
     */
    protected $configRootName = 'configRoot';
    /**
     * @var
     */
    protected $options;


    /**
     * Setter for the options array
     * @param $options
     */
    public function setOptions($options) {
        $this->options = $options;
        if (isset($options['rootName'])) {
            $this->configRootName = $options['rootName'];
        }
    }

    /**
     * Returns the matching Helion configuration object
     * from a configuration file path, URL, or string
     *
     * @param string $configSrc
     * @return HelionConfigValue
     */
    public function getConfig($configSrc) {

        try {
            $configString = $this->getConfigString($configSrc);
        } catch (\ErrorException $e) {
            return new HelionConfigValue('Error', $e->getMessage());
        } catch (\UnexpectedValueException $e) {
            return new HelionConfigValue('Error', $e->getMessage());
        }
        try {
            $helionConfig = $this->parseConfigString($configString);
        } catch (\UnexpectedValueException $e) {
            return new HelionConfigValue('Error', $e->getMessage());
        }
        return $helionConfig;
    }

    /**
     * @param $configSrc
     * @return bool|false|string
     * @throws \ErrorException
     */
    public function getConfigString($configSrc) {
        if (file_exists($configSrc)) {
            $fileContents = file_get_contents($configSrc);
            if ($fileContents === false) {
                throw new \ErrorException(error_get_last()["message"]);
            }
            return $fileContents;
        } elseif (filter_var($configSrc, FILTER_VALIDATE_URL)) {
            $curlSession = curl_init();
            curl_setopt($curlSession, CURLOPT_URL, $configSrc);
            curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlSession, CURLOPT_FAILONERROR, true);
            if (isset($this->options['curlOptions'])) {
                if (curl_setopt_array($curlSession, $this->options['curlOptions']) === false) {
                    throw new \UnexpectedValueException('Error: Wrong curl options.');
                }
            }
            $result = curl_exec($curlSession);
            if (curl_error($curlSession)) {
                throw new \ErrorException(curl_error($curlSession));
            }
            curl_close($curlSession);
            return $result;
        } else {
            return $configSrc;
        }
    }

    /**
     * Returns a specific value from Helion configuration object
     * This could be a configuration string or a child Helion configuration object
     *
     * @param string $name
     * @param HelionConfigValue $helionConfigValue
     * @return string|array|HelionConfigValue
     */
    public function getConfigValue($name, HelionConfigValue $helionConfigValue) {

        $sectionSeparator = SECTION_SEPARATOR;
        $nameParts = explode($sectionSeparator, $name);
        foreach ($nameParts as $namePartValue) {
            $nextNamePartValue = next($nameParts);
            if ($namePartValue !== '@attributes') {
                if (array_key_exists($namePartValue, $helionConfigValue->helionConfigValue)) {
                    $helionConfigValue = $helionConfigValue->helionConfigValue[$namePartValue];
                } else {
                    return new HelionConfigValue('Error', "$namePartValue not found!");
                }
            } else {
                if ($nextNamePartValue) {
                    if (array_key_exists($nextNamePartValue, $helionConfigValue->helionConfigAttributes)) {
                        return $helionConfigValue->helionConfigAttributes[$nextNamePartValue];
                    } else {
                        return new HelionConfigValue('Error', "$nextNamePartValue not found!");
                    }
                } else {
                    if (isset($helionConfigValue->helionConfigAttributes)) {
                        return $helionConfigValue->helionConfigAttributes;
                    } else {
                        return new HelionConfigValue('Error', "@attributes  not found!");
                    }
                }
            }
        }
        if (is_array($helionConfigValue->helionConfigValue)) {
            return $helionConfigValue;
        }
        return $helionConfigValue->helionConfigValue;
    }

    /**
     * @param array $bitmaskArray
     * @return int
     */
    protected function bitmask(array $bitmaskArray) {
        $bitmask = 0;
        foreach ($bitmaskArray as $value) {
            $bitmask |= $value;
        }
        return $bitmask;
    }
}