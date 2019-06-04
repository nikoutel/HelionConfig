<?php

namespace Nikoutel\HelionConfig\ConfigType;

use Nikoutel\HelionConfig\HelionConfigValue;

class XML extends ConfigType implements ConfigTypeInterface
{
    public function parseConfigString($configString) {
        libxml_use_internal_errors(true);
        try {
            $simpleXMLElement = new \SimpleXMLElement($configString);
        } catch (\Exception $e) {
            $xmlError = ' - ';
            foreach (libxml_get_errors() as $error) {
                $xmlError .= $error->message;
            }
            throw new \UnexpectedValueException('XML format error! ' . $e->getMessage() . $xmlError);
        }
        $helionConfigValue = $this->toHelionConfigValue($simpleXMLElement);
        return $helionConfigValue;
    }

    private function toHelionConfigValue(\SimpleXMLElement $simpleXMLElement) {
        $innerHelionConfigValueIn = $this->toInnerHelionConfigValue($simpleXMLElement);
        $helionConfigValue = new HelionConfigValue($this->configRootName, $innerHelionConfigValueIn);
        return $helionConfigValue;
    }

    private function toInnerHelionConfigValue(\SimpleXMLElement $simpleXMLElement) {
        $children = array();
        $attributes = array();

        $name = (string)$simpleXMLElement->getName();
        $text = (string)$simpleXMLElement;
        if (empty($text)) {
            $text = NULL;
        }

        $namespace = $simpleXMLElement->getDocNamespaces(true);
        $namespace[NULL] = NULL;

        if (is_object($simpleXMLElement)) {
            foreach ($namespace as $nsName => $nsURI) {

                $simpleXMLElementAttributes = $simpleXMLElement->attributes($nsName, true);
                foreach ($simpleXMLElementAttributes as $attributeName => $attributeValue) {
                    $attributeNameStr = (string)$attributeName;
                    $attributeValueStr = (string)$attributeValue;
                    if (!empty($nsName)) {
                        $attributeNameStr = $nsName . ':' . $attributeNameStr;
                    }
                    $attributes[$attributeNameStr] = $attributeValueStr;
                }

                $simpleXMLElementChildren = $simpleXMLElement->children($nsName, true);
                foreach ($simpleXMLElementChildren as $childName => $child) {
                    $childName = (string)$childName;
                    if (!empty($nsName)) {
                        $childName = $nsName . ':' . $childName;
                    }
                    if (array_key_exists($childName, $children)) {
                        $childClone = clone $children[$childName];
                        if (!is_array($children[$childName]->value) || !array_key_exists(0, $children[$childName]->value)) { // UGLY HACK!!
                            $children[$childName]->value = array($childClone);
                        }
                        $childConfigValue = $this->toInnerHelionConfigValue($child);
                        $childConfigValue->name = $childConfigValue->name . (string)(count($children[$childName]->value));
                        $children[$childName]->value[] = $childConfigValue;
                        $children[$childName]->name = strstr($children[$childName]->name, '.', true) ?: $children[$childName]->name;
                        $children[$childName]->value[0]->name = $children[$childName]->name . '.0';
                    } else {
                        $children[$childName] = $this->toInnerHelionConfigValue($child);
                    }
                }
            }
        }
        if (!empty($children)) {
            $value = $children;
        } else {
            $value = $text;
        }
        $returnVal = new HelionConfigValue($name, $value, $attributes);
        return $returnVal;
    }

}