<?php

namespace Nikoutel\HelionConfig\ConfigType;

class XML implements ConfigType
{
    public function getConfig() {
        return 'XML getConfig() called!';
    }
}