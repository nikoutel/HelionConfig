<?php
/**
 *
 * HelionConfig: Facade for Helion Config
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

class HelionConfig
{
    /**
     * @var string
     */
    private $configTypeNS = __NAMESPACE__ . '\ConfigType\\';

    /**
     * Returns the configuration reader object
     *
     * @param $type
     * @return ConfigReader
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    public function getConfigReader($type) {
        !defined('SECTION_SEPARATOR') && define('SECTION_SEPARATOR', '.');
        if (in_array($type, $this->listConfigTypeOptions(true))) {
            $namespacedType = $this->configTypeNS . $type;
            return new ConfigReader(new $namespacedType);
        } else {
            throw new \InvalidArgumentException("$type is not a valid ConfigType!");
        }
    }

    /**
     * Returns an array with a list of available configuration type options
     *
     * @param bool $listValues
     * @return array
     * @throws \ReflectionException
     */
    public function listConfigTypeOptions($listValues = false) {
        $rf = new \ReflectionClass($this->configTypeNS . 'ConfigTypeOptions');
        return ($listValues) ? $rf->getConstants() : array_keys($rf->getConstants());
    }
}