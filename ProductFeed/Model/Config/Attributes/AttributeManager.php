<?php

namespace Urbit\ProductFeed\Model\Config\Attributes;

use Magento\Catalog\Model\Product as MagentoProduct;

use Urbit\ProductFeed\Model\Config\Attributes\Type\Attributes;
use Urbit\ProductFeed\Model\Config\Attributes\Type\Db;
use Urbit\ProductFeed\Model\Config\Attributes\Type\Calculated;

/**
 * Class AttributeManager
 * @package Urbit\ProductFeed\Model\Config\Attributes
 */
class AttributeManager
{
    const COMMON_KEY = 'custom_attributes';

    /**
     * @var AttributeManager
     */
    protected static $_instance;

    /**
     * @var array
     */
    protected $_types = array();

    /**
     * @var array
     */
    protected $_fields = array();

    /**
     * @var bool
     */
    protected $_productLoaded = false;

    /**
     * @var MagentoProduct
     */
    protected $_product;

    /**
     * @param MagentoProduct|null $product
     * @return AttributeManager
     */
    public static function getInstance($product = null)
    {
        if (!(static::$_instance instanceof AttributeType)) {
            static::$_instance = new static();

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            static::$_instance->_product = $objectManager
                ->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
                ->addAttributeToSelect('*')
                ->getFirstItem();

            static::$_instance->_productLoaded = false;

            static::$_instance->_loadType(new Attributes(static::$_instance->_product));
            static::$_instance->_loadType(new Db(static::$_instance->_product));
            static::$_instance->_loadType(new Calculated(static::$_instance->_product));
        }

        return static::$_instance;
    }

    /**
     * AttributeManager constructor.
     */
    protected function __construct() {}

    /**
     * @param MagentoProduct $product
     * @return $this
     */
    public function loadProduct(MagentoProduct $product)
    {
        $this->_product = $product;
        $this->_productLoaded = true;

        $this->_destroyTypes();

        $this->_loadType(new Attributes($this->_product));
        $this->_loadType(new Db($this->_product));
        $this->_loadType(new Calculated($this->_product));

        return $this;
    }

    private function __clone() {}

    private function __wakeup() {}

    /**
     * @return array
     */
    public function getFields()
    {
        if (empty($this->_fields)) {
            $fields = array();

            /** @var AttributeType $type */
            foreach ($this->_types as $type) {
                $fields = array_merge($type->getFields(), $fields);
            }

            $fields = array_merge(array('' => '--Please Select--'), $fields);

            $fields = $this->_addCommonKey($fields);
            $this->_fields = $fields;
        }

        return $this->_fields;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function checkAttributeCode($code)
    {
        if (strpos($code, static::COMMON_KEY) !== false) {
            return true;
        }

        return false;
    }

    /**
     * @param string $code
     * @return mixed|null
     */
    public function getAttributeValue($code)
    {
        $code = str_replace(static::COMMON_KEY, '', $code);
        $typeObject = $this->_getTypeObjectByCode($code);

        if (!$typeObject) {
            return null;
        }

        return $typeObject->getAttributeValue($code);
    }

    /**
     * @param string $code
     * @return AttributeType|bool
     */
    protected function _getTypeObjectByCode($code)
    {
        /** @var AttributeType $type */
        foreach ($this->_types as $type) {
            if (strpos($code, $type::TYPE_KEY) !== false) {
                return $type;
            }
        }

        return false;
    }

    /**
     * @param AttributeType $type
     */
    protected function _loadType(AttributeType $type)
    {
        $this->_types[$type->getTypeKey()] = $type;
    }

    protected function _destroyTypes()
    {
        foreach ($this->_types as $type) {
            unset($type);
        }

        $this->_types = array();
    }

    /**
     * @param array $fields
     * @return array
     */
    protected function _addCommonKey($fields)
    {
        $resultFields = array();

        foreach ($fields as $key => $field) {
            $resultFields[static::COMMON_KEY . '_' . $key] = $field;
        }

        return $resultFields;
    }
}