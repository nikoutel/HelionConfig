<?php
/**
 *
 * ConfigTypeInterface: Configuration type parser interface
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

interface ConfigTypeInterface
{
    /**
     * Returns the matching Helion configuration object
     * from a configuration file path, URL, or string
     *
     * @param string $configSrc
     * @return HelionConfigValue
     */
    public function getConfig($configSrc);

    /**
     * Parses the configuration string and returns
     * the equivalent Helion configuration value object
     *
     * @param string $configString
     * @return HelionConfigValue
     */
    public function parseConfigString($configString);

    /**
     * Returns a specific value from a Helion configuration object.
     * This could be a configuration string or a child Helion configuration object
     *
     * @param string $name
     * @param HelionConfigValue $helionConfigValue
     * @return string|HelionConfigValue
     */
    public function getConfigValue($name, HelionConfigValue $helionConfigValue);
}