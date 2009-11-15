<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ShippingCarrierDetailsType
 *
 * @package PayPal
 */
class ShippingCarrierDetailsType extends XSDSimpleType
{
    /**
     * Calculated cost of shipping, based on shipping parameters and selected shipping
     * service. Only returned if ShippingType = 2 (i.e., calculated shipping rate).
     */
    var $CarrierShippingFee;

    var $InsuranceFee;

    var $InsuranceOption;

    /**
     * Optional fees a seller might assess for the shipping of the item.
     */
    var $PackagingHandlingCosts;

    /**
     * Describes any error message associated with the attempt to calculate shipping
     * rates. If there was no error, returns "No Error" (without the quotation marks).
     */
    var $ShippingRateErrorMessage;

    /**
     * is unique identified of shipping carrier, without this element the whole node
     * makes no sence
     */
    var $ShippingService;

    function ShippingCarrierDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'CarrierShippingFee' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'InsuranceFee' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'InsuranceOption' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PackagingHandlingCosts' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingRateErrorMessage' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingService' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getCarrierShippingFee()
    {
        return $this->CarrierShippingFee;
    }
    function setCarrierShippingFee($CarrierShippingFee, $charset = 'iso-8859-1')
    {
        $this->CarrierShippingFee = $CarrierShippingFee;
        $this->_elements['CarrierShippingFee']['charset'] = $charset;
    }
    function getInsuranceFee()
    {
        return $this->InsuranceFee;
    }
    function setInsuranceFee($InsuranceFee, $charset = 'iso-8859-1')
    {
        $this->InsuranceFee = $InsuranceFee;
        $this->_elements['InsuranceFee']['charset'] = $charset;
    }
    function getInsuranceOption()
    {
        return $this->InsuranceOption;
    }
    function setInsuranceOption($InsuranceOption, $charset = 'iso-8859-1')
    {
        $this->InsuranceOption = $InsuranceOption;
        $this->_elements['InsuranceOption']['charset'] = $charset;
    }
    function getPackagingHandlingCosts()
    {
        return $this->PackagingHandlingCosts;
    }
    function setPackagingHandlingCosts($PackagingHandlingCosts, $charset = 'iso-8859-1')
    {
        $this->PackagingHandlingCosts = $PackagingHandlingCosts;
        $this->_elements['PackagingHandlingCosts']['charset'] = $charset;
    }
    function getShippingRateErrorMessage()
    {
        return $this->ShippingRateErrorMessage;
    }
    function setShippingRateErrorMessage($ShippingRateErrorMessage, $charset = 'iso-8859-1')
    {
        $this->ShippingRateErrorMessage = $ShippingRateErrorMessage;
        $this->_elements['ShippingRateErrorMessage']['charset'] = $charset;
    }
    function getShippingService()
    {
        return $this->ShippingService;
    }
    function setShippingService($ShippingService, $charset = 'iso-8859-1')
    {
        $this->ShippingService = $ShippingService;
        $this->_elements['ShippingService']['charset'] = $charset;
    }
}
