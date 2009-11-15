<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * BillingAgreementDetailsType
 *
 * @package PayPal
 */
class BillingAgreementDetailsType extends XSDSimpleType
{
    var $BillingType;

    /**
     * Only needed for AutoBill billinng type.
     */
    var $BillingAgreementDescription;

    var $PaymentType;

    /**
     * Custom annotation field for your exclusive use.
     */
    var $BillingAgreementCustom;

    function BillingAgreementDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'BillingType' => 
              array (
                'required' => true,
                'type' => 'BillingCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingAgreementDescription' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentType' => 
              array (
                'required' => false,
                'type' => 'MerchantPullPaymentCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingAgreementCustom' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getBillingType()
    {
        return $this->BillingType;
    }
    function setBillingType($BillingType, $charset = 'iso-8859-1')
    {
        $this->BillingType = $BillingType;
        $this->_elements['BillingType']['charset'] = $charset;
    }
    function getBillingAgreementDescription()
    {
        return $this->BillingAgreementDescription;
    }
    function setBillingAgreementDescription($BillingAgreementDescription, $charset = 'iso-8859-1')
    {
        $this->BillingAgreementDescription = $BillingAgreementDescription;
        $this->_elements['BillingAgreementDescription']['charset'] = $charset;
    }
    function getPaymentType()
    {
        return $this->PaymentType;
    }
    function setPaymentType($PaymentType, $charset = 'iso-8859-1')
    {
        $this->PaymentType = $PaymentType;
        $this->_elements['PaymentType']['charset'] = $charset;
    }
    function getBillingAgreementCustom()
    {
        return $this->BillingAgreementCustom;
    }
    function setBillingAgreementCustom($BillingAgreementCustom, $charset = 'iso-8859-1')
    {
        $this->BillingAgreementCustom = $BillingAgreementCustom;
        $this->_elements['BillingAgreementCustom']['charset'] = $charset;
    }
}
