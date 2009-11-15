<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ShippingOptionType
 * 
 * Fallback shipping options type.
 *
 * @package PayPal
 */
class ShippingOptionType extends XSDSimpleType
{
    var $ShippingOptionIsDefault;

    var $ShippingOptionAmount;

    var $ShippingOptionLabel;

    var $ShippingOptionName;

    function ShippingOptionType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ShippingOptionIsDefault' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingOptionAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingOptionLabel' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingOptionName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getShippingOptionIsDefault()
    {
        return $this->ShippingOptionIsDefault;
    }
    function setShippingOptionIsDefault($ShippingOptionIsDefault, $charset = 'iso-8859-1')
    {
        $this->ShippingOptionIsDefault = $ShippingOptionIsDefault;
        $this->_elements['ShippingOptionIsDefault']['charset'] = $charset;
    }
    function getShippingOptionAmount()
    {
        return $this->ShippingOptionAmount;
    }
    function setShippingOptionAmount($ShippingOptionAmount, $charset = 'iso-8859-1')
    {
        $this->ShippingOptionAmount = $ShippingOptionAmount;
        $this->_elements['ShippingOptionAmount']['charset'] = $charset;
    }
    function getShippingOptionLabel()
    {
        return $this->ShippingOptionLabel;
    }
    function setShippingOptionLabel($ShippingOptionLabel, $charset = 'iso-8859-1')
    {
        $this->ShippingOptionLabel = $ShippingOptionLabel;
        $this->_elements['ShippingOptionLabel']['charset'] = $charset;
    }
    function getShippingOptionName()
    {
        return $this->ShippingOptionName;
    }
    function setShippingOptionName($ShippingOptionName, $charset = 'iso-8859-1')
    {
        $this->ShippingOptionName = $ShippingOptionName;
        $this->_elements['ShippingOptionName']['charset'] = $charset;
    }
}
