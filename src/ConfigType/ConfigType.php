<?php

namespace Nikoutel\HelionConfig\ConfigType;

use Nikoutel\HelionConfig\HelionConfigValue;

class ConfigType
{
    protected $configRootName = 'configRoot';

    public function getConfigValue($name, HelionConfigValue $helionConfig) {

        $sectionSeparator = SECTION_SEPARATOR;
        $nameParts = explode($sectionSeparator, $name);
        foreach ($nameParts as $v) {
            if (array_key_exists($v, $helionConfig->value)) {
                $helionConfig = $helionConfig->value[$v];
            } else {
                return new HelionConfigValue('Error', "$v not found!");
            }
        }
        if (is_array($helionConfig->value)) {
            return $helionConfig;
        }
        return $helionConfig->value;
    }

    public function getConfigString($configSrc) {
        if (file_exists($configSrc)) {
            $fileContents = file_get_contents($configSrc);
            if ($fileContents === false) {
                throw new \ErrorException(error_get_last()["message"]);
            }
            return $fileContents;
        } elseif (filter_var($configSrc, FILTER_VALIDATE_URL)) {
            $curlSession = curl_init();
            curl_setopt($curlSession, CURLOPT_URL, $configSrc);
            curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true); //@todo allow additional external CURL options
            curl_setopt($curlSession, CURLOPT_FAILONERROR, true);
            $result = curl_exec($curlSession);
            if (curl_error($curlSession)) {
                throw new \ErrorException(curl_error($curlSession));
            }
            curl_close($curlSession);
            return $result;
        } else {
            return $configSrc;
        }
    }

}