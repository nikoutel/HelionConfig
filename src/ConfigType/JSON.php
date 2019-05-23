<?php

namespace Nikoutel\HelionConfig\ConfigType;

class JSON implements ConfigTypeInterface
{
    public function getConfig($configSrc) {
        return 'JSON getConfig() called!';
    }
}