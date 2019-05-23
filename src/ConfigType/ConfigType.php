<?php

namespace Nikoutel\HelionConfig\ConfigType;

class ConfigType
{
    public function getConfigString($configSrc) {
        if (file_exists($configSrc)) {
            return file_get_contents($configSrc);
        } elseif (filter_var($configSrc, FILTER_VALIDATE_URL) ) {
            $curlSession = curl_init();
            curl_setopt($curlSession, CURLOPT_URL, $configSrc);
            curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true); //@todo allow additional external CURL options
            $result = curl_exec($curlSession);
            curl_close($curlSession);
            return $result;
        } else {
            return $configSrc;
        }
    }

}