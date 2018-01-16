<?php

namespace Urbit\ProductFeed\Model\Config\Attributes\Type;

use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection as AttributeCollection;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory as EavAttributeFactory;
use Magento\Eav\Model\Entity\Type as EntityType;
use Magento\Eav\Model\Entity\TypeFactory as EntityTypeFactory;
use Magento\Framework\App\ObjectManager;

use Urbit\ProductFeed\Model\Config\Attributes\AttributeType;

/**
 * Class Attributes
 * @package Urbit\ProductFeed\Model\Config\Attributes\Type
 */
class Attributes extends AttributeType
{
    const TYPE_KEY = 'attr';

    protected function _loadFields()
    {
        $this->_addField('', '--Attributes fields--');

        /** @var EntityType $entityType */
        $entityType = ObjectManager::getInstance()
            ->create(EntityTypeFactory::class)
            ->create()
            ->loadByCode('catalog_product');

        /** @var AttributeCollection $collection */
        $collection = ObjectManager::getInstance()
            ->create(EavAttributeFactory::class)
            ->create()
            ->getCollection()
            ->addFieldToFilter('entity_type_id', $entityType->getId())
            ->setOrder('attribute_code')
        ;

        /** @var Attribute $attribute */
        foreach ($collection as $attribute) {
            if (!$attribute->getIsUserDefined()) {
                continue;
            }

            $code = $attribute->getAttributeCode();
            $this->_addField($code, $attribute->getFrontendLabel());
        }
    }

    /**
     * @param string $code
     * @return mixed|void
     */
    protected function _getAttributeValue($code)
    {
        $attr = $this->_getAttribute($code);

        if (!$attr) {
            return null;
        }

        return $attr->getFrontend()->getValue($this->_product);
    }

    /**
     * Helper function
     * Get product attribute object
     * @param string $name
     * @return
     */
    protected function _getAttribute($name)
    {
        return $this->_product->getResource()->getAttribute($name);
    }
}