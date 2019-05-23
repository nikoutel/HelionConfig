<?php

namespace Nikoutel\HelionConfig\ConfigType;

class PHPArray implements ConfigTypeInterface
{
    public function getConfig($configSrc) {
        return 'PHPArray getConfig() called!';
    }
}
