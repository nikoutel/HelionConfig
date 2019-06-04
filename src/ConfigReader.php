<?php
/**
 *
 * ConfigReader: Strategy initialisation
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

use Nikoutel\HelionConfig\ConfigType\ConfigTypeInterface;

class ConfigReader
{
    /**
     * @var ConfigTypeInterface
     */
    private $type;

    /**
     * ConfigReader constructor
     * Sets the config type dynamically according to $configType
     *
     * @param ConfigTypeInterface $configType
     */
    public function __construct(ConfigTypeInterface $configType) {
        $this->type = $configType;
    }

    /**
     * Returns the Helion configuration object dynamically
     *
     * @param string $configSrc
     * @return HelionConfigValue
     */
    public function getConfig($configSrc) {
        return $this->type->getConfig($configSrc);
    }

    /**
     * Returns a value from Helion configuration object dynamically
     *
     * @param string $name
     * @param HelionConfigValue $helionConfigValue
     * @return string|HelionConfigValue
     */
    public function getConfigValue($name, HelionConfigValue $helionConfigValue) {
        return $this->type->getConfigValue($name, $helionConfigValue);
    }
}