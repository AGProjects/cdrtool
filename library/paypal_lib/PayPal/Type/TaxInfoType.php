<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * TaxInfoType
 *
 * @package PayPal
 */
class TaxInfoType extends XSDSimpleType
{
    var $TaxAmount;

    var $SalesTaxPercentage;

    var $TaxState;

    function TaxInfoType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'TaxAmount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SalesTaxPercentage' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TaxState' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
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
    function getSalesTaxPercentage()
    {
        return $this->SalesTaxPercentage;
    }
    function setSalesTaxPercentage($SalesTaxPercentage, $charset = 'iso-8859-1')
    {
        $this->SalesTaxPercentage = $SalesTaxPercentage;
        $this->_elements['SalesTaxPercentage']['charset'] = $charset;
    }
    function getTaxState()
    {
        return $this->TaxState;
    }
    function setTaxState($TaxState, $charset = 'iso-8859-1')
    {
        $this->TaxState = $TaxState;
        $this->_elements['TaxState']['charset'] = $charset;
    }
}
