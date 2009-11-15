<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * FeeType
 * 
 * Definition of an eBay Fee type.
 *
 * @package PayPal
 */
class FeeType extends XSDSimpleType
{
    var $Name;

    var $Fee;

    function FeeType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Name' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Fee' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getName()
    {
        return $this->Name;
    }
    function setName($Name, $charset = 'iso-8859-1')
    {
        $this->Name = $Name;
        $this->_elements['Name']['charset'] = $charset;
    }
    function getFee()
    {
        return $this->Fee;
    }
    function setFee($Fee, $charset = 'iso-8859-1')
    {
        $this->Fee = $Fee;
        $this->_elements['Fee']['charset'] = $charset;
    }
}
