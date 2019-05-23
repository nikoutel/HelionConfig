<?php

namespace Nikoutel\HelionConfig\ConfigType;

class PHPArray extends ConfigType implements ConfigTypeInterface
{
    public function getConfig($configSrc) {
        return 'PHPArray getConfig() called!';
    }
}
