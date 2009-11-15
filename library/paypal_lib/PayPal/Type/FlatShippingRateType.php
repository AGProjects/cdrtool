<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * FlatShippingRateType
 *
 * @package PayPal
 */
class FlatShippingRateType extends XSDSimpleType
{
    /**
     * Any additional shipping costs for the item.
     */
    var $AdditionalShippingCosts;

    var $FlatShippingHandlingCosts;

    var $InsuranceFee;

    var $InsuranceOption;

    var $ShippingService;

    function FlatShippingRateType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'AdditionalShippingCosts' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FlatShippingHandlingCosts' => 
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
              'ShippingService' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getAdditionalShippingCosts()
    {
        return $this->AdditionalShippingCosts;
    }
    function setAdditionalShippingCosts($AdditionalShippingCosts, $charset = 'iso-8859-1')
    {
        $this->AdditionalShippingCosts = $AdditionalShippingCosts;
        $this->_elements['AdditionalShippingCosts']['charset'] = $charset;
    }
    function getFlatShippingHandlingCosts()
    {
        return $this->FlatShippingHandlingCosts;
    }
    function setFlatShippingHandlingCosts($FlatShippingHandlingCosts, $charset = 'iso-8859-1')
    {
        $this->FlatShippingHandlingCosts = $FlatShippingHandlingCosts;
        $this->_elements['FlatShippingHandlingCosts']['charset'] = $charset;
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
