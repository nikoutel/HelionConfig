<?php

namespace Nikoutel\HelionConfig\ConfigType;

class Conf implements ConfigTypeInterface
{
    public function getConfig($configSrc) {
        return 'Conf getConfig() called!';
    }
}