<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * DoMobileCheckoutPaymentResponseDetailsType
 *
 * @package PayPal
 */
class DoMobileCheckoutPaymentResponseDetailsType extends XSDSimpleType
{
    /**
     * A free-form field for your own use, such as a tracking number or other value you
     * want returned to you in IPN.
     */
    var $Custom;

    /**
     * Your own unique invoice or tracking number.
     */
    var $InvoiceID;

    /**
     * Information about the payer
     */
    var $PayerInfo;

    /**
     * Information about the transaction
     */
    var $PaymentInfo;

    function DoMobileCheckoutPaymentResponseDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Custom' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'InvoiceID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerInfo' => 
              array (
                'required' => true,
                'type' => 'PayerInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentInfo' => 
              array (
                'required' => true,
                'type' => 'PaymentInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getCustom()
    {
        return $this->Custom;
    }
    function setCustom($Custom, $charset = 'iso-8859-1')
    {
        $this->Custom = $Custom;
        $this->_elements['Custom']['charset'] = $charset;
    }
    function getInvoiceID()
    {
        return $this->InvoiceID;
    }
    function setInvoiceID($InvoiceID, $charset = 'iso-8859-1')
    {
        $this->InvoiceID = $InvoiceID;
        $this->_elements['InvoiceID']['charset'] = $charset;
    }
    function getPayerInfo()
    {
        return $this->PayerInfo;
    }
    function setPayerInfo($PayerInfo, $charset = 'iso-8859-1')
    {
        $this->PayerInfo = $PayerInfo;
        $this->_elements['PayerInfo']['charset'] = $charset;
    }
    function getPaymentInfo()
    {
        return $this->PaymentInfo;
    }
    function setPaymentInfo($PaymentInfo, $charset = 'iso-8859-1')
    {
        $this->PaymentInfo = $PaymentInfo;
        $this->_elements['PaymentInfo']['charset'] = $charset;
    }
}
