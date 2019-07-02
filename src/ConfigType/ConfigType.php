<?php
/**
 *
 * ConfigType: Enumerates the 'configuration type options'
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

final Class ConfigType
{

    const XML = 'XML';
    const INI = 'INI';
    const APACHE = 'Apache';
    const PHPARRAY = 'PHPArray';
    const JSON = 'JSON';
    const CONF = 'Conf';

    private function __construct() {

    }

}