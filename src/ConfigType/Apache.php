<?php

namespace Nikoutel\HelionConfig\ConfigType;

class Apache extends ConfigType implements ConfigTypeInterface
{
    public function getConfig($configSrc) {
        return 'Apache getConfig() called!';
    }
}
