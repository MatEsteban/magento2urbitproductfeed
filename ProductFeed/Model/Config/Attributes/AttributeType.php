<?php

namespace Urbit\ProductFeed\Model\Config\Attributes;

use Magento\Catalog\Model\Product as MagentoProduct;

/**
 * Class AttributeType
 * @package Urbit\ProductFeed\Model\Config\Attributes
 */
abstract class AttributeType
{
    const TYPE_KEY = '';

    /**
     * @var array
     */
    protected $_fields = array();

    /**
     * @var array
     */
    protected $_data = array();

    /**
     * @var MagentoProduct
     */
    protected $_product;

    abstract protected function _loadFields();

    /**
     * AttributeType constructor.
     * @param MagentoProduct $product
     */
    public function __construct(MagentoProduct $product)
    {
        $this->_product = $product;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        if (empty($this->_fields)) {
            $this->_loadFields();
        }

        return $this->_fields;
    }

    /**
     * @return string
     */
    public function getTypeKey()
    {
        return static::TYPE_KEY;
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function getAttributeValue($code)
    {
        $code = $this->_removeTypeKey($code);
        $value = null;

        try {
            $value = $this->_getAttributeValue($code);
        } catch (\Exception $e) {}

        return $value;
    }

    /**
     * @param string $code
     * @return mixed
     */
    abstract protected function _getAttributeValue($code);

    /**
     * @param string $code
     * @return string
     */
    protected function _removeTypeKey($code)
    {
        $code = str_replace(static::TYPE_KEY, '', $code);
        return str_replace('__', '', $code);
    }

    /**
     * @param string $key
     * @param string $value
     */
    protected function _addField($key, $value = '')
    {
        $this->_fields[static::TYPE_KEY . '_' . $key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function _getField($key)
    {
        return $this->_fields[static::TYPE_KEY . '_' . $key];
    }
}