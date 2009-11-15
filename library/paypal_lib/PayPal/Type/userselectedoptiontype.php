<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * UserSelectedOptionType
 * 
 * Information on user selected options
 *
 * @package PayPal
 */
class UserSelectedOptionType extends XSDSimpleType
{
    var $ShippingCalculationMode;

    var $InsuranceOptionSelected;

    var $ShippingOptionIsDefault;

    var $ShippingOptionAmount;

    var $ShippingOptionName;

    function UserSelectedOptionType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ShippingCalculationMode' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'InsuranceOptionSelected' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
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
              'ShippingOptionName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getShippingCalculationMode()
    {
        return $this->ShippingCalculationMode;
    }
    function setShippingCalculationMode($ShippingCalculationMode, $charset = 'iso-8859-1')
    {
        $this->ShippingCalculationMode = $ShippingCalculationMode;
        $this->_elements['ShippingCalculationMode']['charset'] = $charset;
    }
    function getInsuranceOptionSelected()
    {
        return $this->InsuranceOptionSelected;
    }
    function setInsuranceOptionSelected($InsuranceOptionSelected, $charset = 'iso-8859-1')
    {
        $this->InsuranceOptionSelected = $InsuranceOptionSelected;
        $this->_elements['InsuranceOptionSelected']['charset'] = $charset;
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
