<?php

namespace Nikoutel\HelionConfig\ConfigType;

class XML implements ConfigTypeInterface
{
    public function getConfig($configSrc) {
        return 'XML getConfig() called!';
    }
}