<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * DoDirectPaymentRequestDetailsType
 *
 * @package PayPal
 */
class DoDirectPaymentRequestDetailsType extends XSDSimpleType
{
    /**
     * How you want to obtain payment.
     */
    var $PaymentAction;

    /**
     * Information about the payment
     */
    var $PaymentDetails;

    /**
     * Information about the credit card to be charged.
     */
    var $CreditCard;

    /**
     * IP address of the payer's browser as recorded in its HTTP request to your
     * website. PayPal records this IP addresses as a means to detect possible fraud.
     */
    var $IPAddress;

    /**
     * Your customer session identification token. PayPal records this optional session
     * identification token as an additional means to detect possible fraud.
     */
    var $MerchantSessionId;

    var $ReturnFMFDetails;

    function DoDirectPaymentRequestDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'PaymentAction' => 
              array (
                'required' => true,
                'type' => 'PaymentActionCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentDetails' => 
              array (
                'required' => true,
                'type' => 'PaymentDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CreditCard' => 
              array (
                'required' => true,
                'type' => 'CreditCardDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'IPAddress' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MerchantSessionId' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReturnFMFDetails' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getPaymentAction()
    {
        return $this->PaymentAction;
    }
    function setPaymentAction($PaymentAction, $charset = 'iso-8859-1')
    {
        $this->PaymentAction = $PaymentAction;
        $this->_elements['PaymentAction']['charset'] = $charset;
    }
    function getPaymentDetails()
    {
        return $this->PaymentDetails;
    }
    function setPaymentDetails($PaymentDetails, $charset = 'iso-8859-1')
    {
        $this->PaymentDetails = $PaymentDetails;
        $this->_elements['PaymentDetails']['charset'] = $charset;
    }
    function getCreditCard()
    {
        return $this->CreditCard;
    }
    function setCreditCard($CreditCard, $charset = 'iso-8859-1')
    {
        $this->CreditCard = $CreditCard;
        $this->_elements['CreditCard']['charset'] = $charset;
    }
    function getIPAddress()
    {
        return $this->IPAddress;
    }
    function setIPAddress($IPAddress, $charset = 'iso-8859-1')
    {
        $this->IPAddress = $IPAddress;
        $this->_elements['IPAddress']['charset'] = $charset;
    }
    function getMerchantSessionId()
    {
        return $this->MerchantSessionId;
    }
    function setMerchantSessionId($MerchantSessionId, $charset = 'iso-8859-1')
    {
        $this->MerchantSessionId = $MerchantSessionId;
        $this->_elements['MerchantSessionId']['charset'] = $charset;
    }
    function getReturnFMFDetails()
    {
        return $this->ReturnFMFDetails;
    }
    function setReturnFMFDetails($ReturnFMFDetails, $charset = 'iso-8859-1')
    {
        $this->ReturnFMFDetails = $ReturnFMFDetails;
        $this->_elements['ReturnFMFDetails']['charset'] = $charset;
    }
}
