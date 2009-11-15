<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ShippingInfoType
 *
 * @package PayPal
 */
class ShippingInfoType extends XSDSimpleType
{
    var $ShippingMethod;

    var $ShippingCarrier;

    var $ShippingAmount;

    var $HandlingAmount;

    var $InsuranceAmount;

    function ShippingInfoType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ShippingMethod' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingCarrier' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingAmount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'HandlingAmount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'InsuranceAmount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getShippingMethod()
    {
        return $this->ShippingMethod;
    }
    function setShippingMethod($ShippingMethod, $charset = 'iso-8859-1')
    {
        $this->ShippingMethod = $ShippingMethod;
        $this->_elements['ShippingMethod']['charset'] = $charset;
    }
    function getShippingCarrier()
    {
        return $this->ShippingCarrier;
    }
    function setShippingCarrier($ShippingCarrier, $charset = 'iso-8859-1')
    {
        $this->ShippingCarrier = $ShippingCarrier;
        $this->_elements['ShippingCarrier']['charset'] = $charset;
    }
    function getShippingAmount()
    {
        return $this->ShippingAmount;
    }
    function setShippingAmount($ShippingAmount, $charset = 'iso-8859-1')
    {
        $this->ShippingAmount = $ShippingAmount;
        $this->_elements['ShippingAmount']['charset'] = $charset;
    }
    function getHandlingAmount()
    {
        return $this->HandlingAmount;
    }
    function setHandlingAmount($HandlingAmount, $charset = 'iso-8859-1')
    {
        $this->HandlingAmount = $HandlingAmount;
        $this->_elements['HandlingAmount']['charset'] = $charset;
    }
    function getInsuranceAmount()
    {
        return $this->InsuranceAmount;
    }
    function setInsuranceAmount($InsuranceAmount, $charset = 'iso-8859-1')
    {
        $this->InsuranceAmount = $InsuranceAmount;
        $this->_elements['InsuranceAmount']['charset'] = $charset;
    }
}
