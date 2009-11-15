<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * PaymentTransactionType
 * 
 * PaymentTransactionType Information about a PayPal payment from the seller side
 *
 * @package PayPal
 */
class PaymentTransactionType extends XSDSimpleType
{
    /**
     * Information about the recipient of the payment
     */
    var $ReceiverInfo;

    /**
     * Information about the payer
     */
    var $PayerInfo;

    /**
     * Information about the transaction
     */
    var $PaymentInfo;

    /**
     * Information about an individual item in the transaction
     */
    var $PaymentItemInfo;

    function PaymentTransactionType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ReceiverInfo' => 
              array (
                'required' => true,
                'type' => 'ReceiverInfoType',
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
              'PaymentItemInfo' => 
              array (
                'required' => false,
                'type' => 'PaymentItemInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getReceiverInfo()
    {
        return $this->ReceiverInfo;
    }
    function setReceiverInfo($ReceiverInfo, $charset = 'iso-8859-1')
    {
        $this->ReceiverInfo = $ReceiverInfo;
        $this->_elements['ReceiverInfo']['charset'] = $charset;
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
    function getPaymentItemInfo()
    {
        return $this->PaymentItemInfo;
    }
    function setPaymentItemInfo($PaymentItemInfo, $charset = 'iso-8859-1')
    {
        $this->PaymentItemInfo = $PaymentItemInfo;
        $this->_elements['PaymentItemInfo']['charset'] = $charset;
    }
}
