<?php

namespace Nikoutel\HelionConfig\ConfigType;

class XML extends ConfigType implements ConfigTypeInterface
{
    public function getConfig($configSrc) {
        return 'XML getConfig() called!';
    }
}