<?php

namespace Nikoutel\HelionConfig\ConfigType;

class INI extends ConfigType implements ConfigTypeInterface
{
    public function getConfig($configSrc) {
        return 'INI getConfig() called!';
    }
}