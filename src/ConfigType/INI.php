<?php

namespace Nikoutel\HelionConfig\ConfigType;

use Nikoutel\HelionConfig\HelionConfigValue;

class INI extends ConfigType implements ConfigTypeInterface
{

    public function parseConfigString($configString) {
        $iniArray = parse_ini_string($configString, true);
        if ($iniArray === false || empty($iniArray)) {
            throw new \UnexpectedValueException('INI format error!');
        }
        $helionConfigValue = $this->toHelionConfigValue($iniArray, $this->configRootName);
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