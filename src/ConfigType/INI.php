<?php

namespace Nikoutel\HelionConfig\ConfigType;

use Nikoutel\HelionConfig\HelionConfigValue;

class INI extends ConfigType implements ConfigTypeInterface
{


    public function getConfig($configSrc) {
        $configString = $this->getConfigString($configSrc);
        $helionConfig = $this->parseConfigString($configString);
        return $helionConfig;
    }

    public function parseConfigString($configString) {
        $iniString = parse_ini_string($configString, true);
        $helionConfigValue = $this->toHelionConfigValue($iniString, $this->configRootName);
        return $helionConfigValue;
    }

    private function toHelionConfigValue($iniArray, $name) {
        $value = array();
        foreach ($iniArray as $key => $leaf) {
            if (is_array($leaf)) {
                $value[$key] = $this->toHelionConfigValue($leaf, $key);
            } else {
                $value[$key] = new HelionConfigValue($key, $leaf);
            }
        }
        $helionConfigValue = new HelionConfigValue($name, $value);
        return $helionConfigValue;
    }
}