<?php

namespace Nikoutel\HelionConfig\ConfigType;

class JSON implements ConfigType
{
    public function getConfig() {
        return 'JSON getConfig() called!';
    }
}