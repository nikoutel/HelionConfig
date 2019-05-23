<?php

namespace Nikoutel\HelionConfig;

use Nikoutel\HelionConfig\ConfigType\ConfigTypeInterface;

class ConfigReader
{
    private $type;

    public function __construct(ConfigTypeInterface $configType) {
        $this->type = $configType;
    }

    public function getConfig($configSrc) {
        return $this->type->getConfig($configSrc);
    }
}