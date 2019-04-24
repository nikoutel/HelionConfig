<?php

namespace Nikoutel\HelionConfig\ConfigType;

class INI implements ConfigType
{
    public function getConfig() {
        return 'INI getConfig() called!';
    }
}