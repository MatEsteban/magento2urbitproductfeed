<?php

namespace Urbit\ProductFeed\Model\Config\Attributes\Type;

use Urbit\ProductFeed\Model\Config\Attributes\AttributeType;

/**
 * Class Calculated
 * @package Urbit\ProductFeed\Model\Config\Attributes\Type
 */
class Calculated extends AttributeType
{
    const TYPE_KEY = 'calc';

    protected function _loadFields()
    {
        $this->_addField('', '--Calculated fields--');

        $methods = get_class_methods(static::class);
        foreach ($methods as $method) {
            if (strpos($method, 'get') !== 0 || in_array($method, get_class_methods(AttributeType::class))) {
                continue;
            }

            $this->_addField($this->_getFieldKey($method), $this->_formatFieldTitle($method));
        }
    }

    /**
     * @param string $code
     * @return mixed
     */
    protected function _getAttributeValue($code)
    {
        $result = null;
        $funcName = str_replace('_', '', preg_replace_callback(
            '/(?<!^)(_)([a-zA-Z0-9-]*)/',
            function($match) {
                return ucfirst($match[2]);
            },
            $code
        ));

        try {
            $result = $this->{$funcName}();
        } catch (\Exception $e) {}

        return $result;
    }

    /**
     * @param string $title
     * @return string
     */
    protected function _formatFieldTitle($title)
    {
        return str_replace('get', '', preg_replace_callback(
            '/(?<!^)([A-Z])/',
            function($match) {
                return ' ' . $match[1];
            },
            $title
        ));
    }

    /**
     * @param $title
     * @return null|string|string[]
     */
    protected function _getFieldKey($title)
    {
        return preg_replace_callback(
            '/(?<!^)([A-Z])/',
            function($match) {
                return '_' . strtolower($match[1]);
            },
            $title
        );
    }

    /****************************************
     * Getters
     ***************************************/

    /**
     * @return string
     */
    public function getProductId()
    {
        return (string)$this->_product->getId();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_product->getName();
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->_product->getDescription();
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->_product->getProductUrl();
    }
}