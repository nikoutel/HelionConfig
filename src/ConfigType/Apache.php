<?php

namespace Nikoutel\HelionConfig\ConfigType;

class Apache implements ConfigTypeInterface
{
    public function getConfig($configSrc) {
        return 'Apache getConfig() called!';
    }
}
