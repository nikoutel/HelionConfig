<?php

namespace Nikoutel\HelionConfig\ConfigType;

class Conf extends ConfigType implements ConfigTypeInterface
{
    public function getConfig($configSrc) {
        return 'Conf getConfig() called!';
    }
}