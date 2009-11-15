<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ItemArrayType
 *
 * @package PayPal
 */
class ItemArrayType extends XSDSimpleType
{
    var $Item;

    function ItemArrayType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Item' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getItem()
    {
        return $this->Item;
    }
    function setItem($Item, $charset = 'iso-8859-1')
    {
        $this->Item = $Item;
        $this->_elements['Item']['charset'] = $charset;
    }
}
