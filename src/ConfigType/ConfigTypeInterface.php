<?php

namespace Nikoutel\HelionConfig\ConfigType;

use Nikoutel\HelionConfig\HelionConfigValue;

interface ConfigTypeInterface
{
    public function getConfig($configSrc);

    public function parseConfigString($configString);

    public function getConfigValue($name, HelionConfigValue $helionConfigValue);
}