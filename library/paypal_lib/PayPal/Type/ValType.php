<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ValType
 *
 * @package PayPal
 */
class ValType extends XSDSimpleType
{
    var $ValueLiteral;

    function ValType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ValueLiteral' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
        $this->_attributes = array_merge($this->_attributes,
            array (
              'ValueID' => 
              array (
                'name' => 'ValueID',
                'type' => 'xs:string',
                'use' => 'optional',
              ),
            ));
    }

    function getValueLiteral()
    {
        return $this->ValueLiteral;
    }
    function setValueLiteral($ValueLiteral, $charset = 'iso-8859-1')
    {
        $this->ValueLiteral = $ValueLiteral;
        $this->_elements['ValueLiteral']['charset'] = $charset;
    }
}
