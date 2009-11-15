<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * SalesTaxType
 *
 * @package PayPal
 */
class SalesTaxType extends XSDSimpleType
{
    /**
     * Amount of the sales tax to be collected for the transaction. Sales tax is only
     * for US.
     */
    var $SalesTaxPercent;

    /**
     * Sales tax for the transaction, expressed as a percentage. Should be empty for
     * items listed on international sites (hence, this is US-only element).
     */
    var $SalesTaxState;

    /**
     * Indicates whether shipping is included in the tax. Applicable if ShippingType =
     * 1 or 2. This element is used for US-only.
     */
    var $ShippingIncludedInTax;

    function SalesTaxType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'SalesTaxPercent' => 
              array (
                'required' => false,
                'type' => 'float',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SalesTaxState' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingIncludedInTax' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getSalesTaxPercent()
    {
        return $this->SalesTaxPercent;
    }
    function setSalesTaxPercent($SalesTaxPercent, $charset = 'iso-8859-1')
    {
        $this->SalesTaxPercent = $SalesTaxPercent;
        $this->_elements['SalesTaxPercent']['charset'] = $charset;
    }
    function getSalesTaxState()
    {
        return $this->SalesTaxState;
    }
    function setSalesTaxState($SalesTaxState, $charset = 'iso-8859-1')
    {
        $this->SalesTaxState = $SalesTaxState;
        $this->_elements['SalesTaxState']['charset'] = $charset;
    }
    function getShippingIncludedInTax()
    {
        return $this->ShippingIncludedInTax;
    }
    function setShippingIncludedInTax($ShippingIncludedInTax, $charset = 'iso-8859-1')
    {
        $this->ShippingIncludedInTax = $ShippingIncludedInTax;
        $this->_elements['ShippingIncludedInTax']['charset'] = $charset;
    }
}
