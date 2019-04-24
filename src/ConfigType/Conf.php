<?php

namespace Nikoutel\HelionConfig\ConfigType;

class Conf implements ConfigType
{
    public function getConfig() {
        return 'Conf getConfig() called!';
    }
}