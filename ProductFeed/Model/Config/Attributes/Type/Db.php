<?php

namespace Urbit\ProductFeed\Model\Config\Attributes\Type;

use Urbit\ProductFeed\Model\Config\Attributes\AttributeType;

/**
 * Class Db
 * @package Urbit\ProductFeed\Model\Config\Attributes\Type
 */
class Db extends AttributeType
{
    const TYPE_KEY = 'db';

    protected function _loadFields()
    {
        $this->_addField('', '--Database fields--');

        /**
         * Load all product attributes
         */
        $this->_data = $this->_product->toArray();

        foreach ($this->_data as $key => $item) {
            $this->_addField($key, $this->_formatFieldTitle($key));
        }
    }

    /**
     * @param string $code
     * @return mixed
     */
    protected function _getAttributeValue($code)
    {
        try {
            return $this->_product->getData($code);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param string $title
     * @return string
     */
    protected function _formatFieldTitle($title)
    {
        return ucfirst(str_replace('_', ' ', $title));
    }
}