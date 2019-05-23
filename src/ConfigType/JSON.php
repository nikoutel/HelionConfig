<?php

namespace Nikoutel\HelionConfig\ConfigType;

class JSON extends ConfigType implements ConfigTypeInterface
{
    public function getConfig($configSrc) {
        return 'JSON getConfig() called!';
    }
}