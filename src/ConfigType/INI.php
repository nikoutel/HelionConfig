<?php

namespace Nikoutel\HelionConfig\ConfigType;

use Nikoutel\HelionConfig\HelionConfigValue;

class INI extends ConfigType implements ConfigTypeInterface
{

    public function getConfig($configSrc) {

        try {
            $configString = $this->getConfigString($configSrc);
        } catch (\ErrorException $e) {
            return new HelionConfigValue('Error', $e->getMessage());
        }
        try {
            $helionConfig = $this->parseConfigString($configString);
        } catch (\UnexpectedValueException $e) {
            return new HelionConfigValue('Error', $e->getMessage());
        }
        return $helionConfig;
    }

    public function parseConfigString($configString) {
        $iniString = parse_ini_string($configString, true);
        if ($iniString === false || empty($iniString)) {
            throw new \UnexpectedValueException('INI format error!');
        }
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