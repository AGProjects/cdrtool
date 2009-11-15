<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * AttributeType
 * 
 * Specific physical attribute of an item.
 *
 * @package PayPal
 */
class AttributeType extends XSDSimpleType
{
    /**
     * ValueList of the Attribute being described by the AttributeID. Constant name of
     * the attribute that identifies a physical attribute within a set of
     * characteristics that describe something in a formalised way.
     */
    var $Value;

    function AttributeType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Value' => 
              array (
                'required' => true,
                'type' => 'ValType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
        $this->_attributes = array_merge($this->_attributes,
            array (
              'AttributeID' => 
              array (
                'name' => 'AttributeID',
                'type' => 'xs:string',
              ),
            ));
    }

    function getValue()
    {
        return $this->Value;
    }
    function setValue($Value, $charset = 'iso-8859-1')
    {
        $this->Value = $Value;
        $this->_elements['Value']['charset'] = $charset;
    }
}
