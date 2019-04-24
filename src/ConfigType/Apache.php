<?php

namespace Nikoutel\HelionConfig\ConfigType;

class Apache implements ConfigType
{
    public function getConfig() {
        return 'Apache getConfig() called!';
    }
}
