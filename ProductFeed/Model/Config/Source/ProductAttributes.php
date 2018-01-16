<?php

namespace Urbit\ProductFeed\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory as EavAttributeFactory;
use Magento\Eav\Model\Entity\TypeFactory as EntityTypeFactory;

use Urbit\ProductFeed\Model\Config\Attributes\AttributeManager;

/**
 * Class ProductAttributes
 * @package Urbit\ProductFeed\Model\Config\Source
 */
class ProductAttributes extends ProductStandardAttributes
{
    /**
     * @var AttributeManager
     */
    protected $_attributeManager;

    /**
     * ProductAttributes constructor.
     * @param EavAttributeFactory $attributeFactory
     * @param EntityTypeFactory $typeFactory
     */
    public function __construct(EavAttributeFactory $attributeFactory, EntityTypeFactory $typeFactory)
    {
        parent::__construct($attributeFactory, $typeFactory);
        $this->_attributeManager = AttributeManager::getInstance();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->_attributeManager->getFields();
    }
}