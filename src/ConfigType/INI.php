<?php

namespace Nikoutel\HelionConfig\ConfigType;

class INI implements ConfigTypeInterface
{
    public function getConfig($configSrc) {
        return 'INI getConfig() called!';
    }
}