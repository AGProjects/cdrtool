<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * BillingPeriodDetailsType
 *
 * @package PayPal
 */
class BillingPeriodDetailsType extends XSDSimpleType
{
    /**
     * Unit of meausre for billing cycle
     */
    var $BillingPeriod;

    /**
     * Number of BillingPeriod that make up one billing cycle
     */
    var $BillingFrequency;

    /**
     * Total billing cycles in this portion of the schedule
     */
    var $TotalBillingCycles;

    /**
     * Amount to charge
     */
    var $Amount;

    /**
     * Additional shipping amount to charge
     */
    var $ShippingAmount;

    /**
     * Additional tax amount to charge
     */
    var $TaxAmount;

    function BillingPeriodDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'BillingPeriod' => 
              array (
                'required' => true,
                'type' => 'BillingPeriodType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingFrequency' => 
              array (
                'required' => true,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TotalBillingCycles' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Amount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TaxAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getBillingPeriod()
    {
        return $this->BillingPeriod;
    }
    function setBillingPeriod($BillingPeriod, $charset = 'iso-8859-1')
    {
        $this->BillingPeriod = $BillingPeriod;
        $this->_elements['BillingPeriod']['charset'] = $charset;
    }
    function getBillingFrequency()
    {
        return $this->BillingFrequency;
    }
    function setBillingFrequency($BillingFrequency, $charset = 'iso-8859-1')
    {
        $this->BillingFrequency = $BillingFrequency;
        $this->_elements['BillingFrequency']['charset'] = $charset;
    }
    function getTotalBillingCycles()
    {
        return $this->TotalBillingCycles;
    }
    function setTotalBillingCycles($TotalBillingCycles, $charset = 'iso-8859-1')
    {
        $this->TotalBillingCycles = $TotalBillingCycles;
        $this->_elements['TotalBillingCycles']['charset'] = $charset;
    }
    function getAmount()
    {
        return $this->Amount;
    }
    function setAmount($Amount, $charset = 'iso-8859-1')
    {
        $this->Amount = $Amount;
        $this->_elements['Amount']['charset'] = $charset;
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
    function getTaxAmount()
    {
        return $this->TaxAmount;
    }
    function setTaxAmount($TaxAmount, $charset = 'iso-8859-1')
    {
        $this->TaxAmount = $TaxAmount;
        $this->_elements['TaxAmount']['charset'] = $charset;
    }
}
