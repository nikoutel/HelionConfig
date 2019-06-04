<?php
/**
 *
 * HelionConfigValue:  Helion configuration value object
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

final class HelionConfigValue
{
    use ArrayTrait;


    /**
     * @var string
     */
    public $name;

    /**
     * @var string|array
     */
    public $value;

    /**
     * @var null|array
     */
    public $attributes;

    /**
     * HelionConfigValue constructor
     *
     * @param string $name
     * @param string|array $value
     * @param null|array $attributes
     */
    public function __construct($name, $value, $attributes = null) {
        $this->name = $name;
        $this->value = $value;
        $this->attributes = $attributes;
    }

    /**
     * Returns the value object as an multidimensional array
     *
     * @return array
     */
    public function asArray() {
        return $this->castToArray($this);
    }

    /**
     * Returns the value object as an flat array
     *
     * @return array
     */
    public function asArrayFlat() {
        return $this->flattenArray($this->castToArray($this));
    }
}