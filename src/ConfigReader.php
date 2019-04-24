<?php

namespace Nikoutel\HelionConfig;

use Nikoutel\HelionConfig\ConfigType\ConfigType;

class ConfigReader
{
    private $type;

    public function __construct(ConfigType $configType) {
        $this->type = $configType;
    }

    public function getConfig() {
        return $this->type->getConfig();
    }
}