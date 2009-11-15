<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ShippingDetailsType
 * 
 * Specifies the shipping payment details.
 *
 * @package PayPal
 */
class ShippingDetailsType extends XSDSimpleType
{
    /**
     * Indicates whether the buyer edited the payment amount.
     */
    var $AllowPaymentEdit;

    /**
     * Calculated shipping rate details. If present, then the calculated shipping rate
     * option was used.
     */
    var $CalculatedShippingRate;

    /**
     * Indicates whether the payment instructions are included (e.g., for updating the
     * details of a transaction).
     */
    var $ChangePaymentInstructions;

    /**
     * Flat shipping rate details. If present, then the flat shipping rate option was
     * used.
     */
    var $FlatShippingRate;

    /**
     * Total cost of insurance for the transaction.
     */
    var $InsuranceTotal;

    /**
     * Indicates whether buyer selected to have insurance.
     */
    var $InsuranceWanted;

    /**
     * Payment instuctions.
     */
    var $PaymentInstructions;

    /**
     * Sales tax details. Sales tax applicable for only US sites. For non-US sites this
     * sub-element should not be used.
     */
    var $SalesTax;

    /**
     * Postal/Zip code from where the seller will ship the item.
     */
    var $SellerPostalCode;

    function ShippingDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'AllowPaymentEdit' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CalculatedShippingRate' => 
              array (
                'required' => false,
                'type' => 'CalculatedShippingRateType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ChangePaymentInstructions' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FlatShippingRate' => 
              array (
                'required' => false,
                'type' => 'FlatShippingRateType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'InsuranceTotal' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'InsuranceWanted' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentInstructions' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SalesTax' => 
              array (
                'required' => false,
                'type' => 'SalesTaxType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SellerPostalCode' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getAllowPaymentEdit()
    {
        return $this->AllowPaymentEdit;
    }
    function setAllowPaymentEdit($AllowPaymentEdit, $charset = 'iso-8859-1')
    {
        $this->AllowPaymentEdit = $AllowPaymentEdit;
        $this->_elements['AllowPaymentEdit']['charset'] = $charset;
    }
    function getCalculatedShippingRate()
    {
        return $this->CalculatedShippingRate;
    }
    function setCalculatedShippingRate($CalculatedShippingRate, $charset = 'iso-8859-1')
    {
        $this->CalculatedShippingRate = $CalculatedShippingRate;
        $this->_elements['CalculatedShippingRate']['charset'] = $charset;
    }
    function getChangePaymentInstructions()
    {
        return $this->ChangePaymentInstructions;
    }
    function setChangePaymentInstructions($ChangePaymentInstructions, $charset = 'iso-8859-1')
    {
        $this->ChangePaymentInstructions = $ChangePaymentInstructions;
        $this->_elements['ChangePaymentInstructions']['charset'] = $charset;
    }
    function getFlatShippingRate()
    {
        return $this->FlatShippingRate;
    }
    function setFlatShippingRate($FlatShippingRate, $charset = 'iso-8859-1')
    {
        $this->FlatShippingRate = $FlatShippingRate;
        $this->_elements['FlatShippingRate']['charset'] = $charset;
    }
    function getInsuranceTotal()
    {
        return $this->InsuranceTotal;
    }
    function setInsuranceTotal($InsuranceTotal, $charset = 'iso-8859-1')
    {
        $this->InsuranceTotal = $InsuranceTotal;
        $this->_elements['InsuranceTotal']['charset'] = $charset;
    }
    function getInsuranceWanted()
    {
        return $this->InsuranceWanted;
    }
    function setInsuranceWanted($InsuranceWanted, $charset = 'iso-8859-1')
    {
        $this->InsuranceWanted = $InsuranceWanted;
        $this->_elements['InsuranceWanted']['charset'] = $charset;
    }
    function getPaymentInstructions()
    {
        return $this->PaymentInstructions;
    }
    function setPaymentInstructions($PaymentInstructions, $charset = 'iso-8859-1')
    {
        $this->PaymentInstructions = $PaymentInstructions;
        $this->_elements['PaymentInstructions']['charset'] = $charset;
    }
    function getSalesTax()
    {
        return $this->SalesTax;
    }
    function setSalesTax($SalesTax, $charset = 'iso-8859-1')
    {
        $this->SalesTax = $SalesTax;
        $this->_elements['SalesTax']['charset'] = $charset;
    }
    function getSellerPostalCode()
    {
        return $this->SellerPostalCode;
    }
    function setSellerPostalCode($SellerPostalCode, $charset = 'iso-8859-1')
    {
        $this->SellerPostalCode = $SellerPostalCode;
        $this->_elements['SellerPostalCode']['charset'] = $charset;
    }
}
