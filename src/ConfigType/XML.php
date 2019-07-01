<?php
/**
 *
 * XML: XML configuration type parser
 *
 *
 * @package HelionConfig
 * @author Nikos Koutelidis nikoutel@gmail.com
 * @copyright 2019 Nikos Koutelidis
 * @license http://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 *
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 *
 */

namespace Nikoutel\HelionConfig\ConfigType;

use Nikoutel\HelionConfig\HelionConfigValue;

class XML extends AbstractConfigType implements ConfigTypeInterface
{
    /**
     * Parses the XML configuration string and returns
     * the equivalent Helion configuration value object
     *
     * @param string $configString
     * @return HelionConfigValue
     */
    public function parseConfigString($configString) {
        libxml_use_internal_errors(true);
        if (isset($this->options['libxmlOptions'])) {
            if (array_product(array_map("is_int", $this->options['libxmlOptions']))) {
                $options = $this->bitmask($this->options['libxmlOptions']);
            } else {
                throw new \UnexpectedValueException('Error: Wrong libxml options.');
            }
        } else {
            $options = 0;
        }
        try {
            $simpleXMLElement = new \SimpleXMLElement($configString, $options);
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

    /**
     * Generates the Helion configuration value object
     *
     * @param \SimpleXMLElement $simpleXMLElement
     * @return HelionConfigValue
     */
    private function toHelionConfigValue(\SimpleXMLElement $simpleXMLElement) {
        $innerHelionConfigValueIn = $this->toInnerHelionConfigValue($simpleXMLElement);
        $helionConfigValue = new HelionConfigValue($this->configRootName, array((string)$simpleXMLElement->getName() => $innerHelionConfigValueIn));
        return $helionConfigValue;
    }

    /**
     * Generates the inner Helion configuration value object
     *
     * @param \SimpleXMLElement $simpleXMLElement
     * @return HelionConfigValue
     */
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
                        if (!is_array($children[$childName]->helionConfigValue) || !array_key_exists(0, $children[$childName]->helionConfigValue)) { // UGLY HACK!!
                            $children[$childName]->helionConfigValue = array($childClone);
                        }
                        $childConfigValue = $this->toInnerHelionConfigValue($child);
                        $childConfigValue->helionConfigName = $childConfigValue->helionConfigName . (string)(count($children[$childName]->helionConfigValue));
                        $children[$childName]->helionConfigValue[] = $childConfigValue;
                        $children[$childName]->helionConfigName = strstr($children[$childName]->helionConfigName, '.', true) ?: $children[$childName]->helionConfigName;
                        $children[$childName]->helionConfigValue[0]->helionConfigName = $children[$childName]->helionConfigName . '.0';
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
        if (empty($attributes)) $attributes = null;
        $returnVal = new HelionConfigValue($name, $value, $attributes);
        return $returnVal;
    }

}