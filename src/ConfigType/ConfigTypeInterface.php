<?php

namespace Nikoutel\HelionConfig\ConfigType;


interface ConfigTypeInterface
{
    public function getConfig($configSrc);

    public function parseConfigString($configString);
}