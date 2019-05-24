<?php

namespace Nikoutel\HelionConfig;

class HelionConfig
{
    private $configTypeNS = __NAMESPACE__ . '\ConfigType\\';

    public function getConfigReader($type) {

        if (in_array($type, $this->listConfigTypeOptions(true))) {
            $namespacedType = $this->configTypeNS . $type;
            return new ConfigReader(new $namespacedType);
        } else {
            throw new \InvalidArgumentException("$type is not a valid ConfigType!");
        }
    }

    public function listConfigTypeOptions($listValues = false) {
        $rf = new \ReflectionClass($this->configTypeNS . 'ConfigTypeOptions');
        return ($listValues) ? $rf->getConstants() : array_keys($rf->getConstants());
    }
}