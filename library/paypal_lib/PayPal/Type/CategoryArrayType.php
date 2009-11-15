<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * CategoryArrayType
 *
 * @package PayPal
 */
class CategoryArrayType extends XSDSimpleType
{
    var $Category;

    function CategoryArrayType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Category' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getCategory()
    {
        return $this->Category;
    }
    function setCategory($Category, $charset = 'iso-8859-1')
    {
        $this->Category = $Category;
        $this->_elements['Category']['charset'] = $charset;
    }
}
