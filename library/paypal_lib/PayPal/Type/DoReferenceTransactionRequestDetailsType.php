<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * DoReferenceTransactionRequestDetailsType
 *
 * @package PayPal
 */
class DoReferenceTransactionRequestDetailsType extends XSDSimpleType
{
    var $ReferenceID;

    var $PaymentAction;

    var $PaymentType;

    var $PaymentDetails;

    var $CreditCard;

    var $IPAddress;

    var $MerchantSessionId;

    var $ReqConfirmShipping;

    var $SoftDescriptor;

    function DoReferenceTransactionRequestDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ReferenceID' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentAction' => 
              array (
                'required' => true,
                'type' => 'PaymentActionCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentType' => 
              array (
                'required' => false,
                'type' => 'MerchantPullPaymentCodeType',
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
                'required' => false,
                'type' => 'ReferenceCreditCardDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'IPAddress' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MerchantSessionId' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReqConfirmShipping' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SoftDescriptor' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getReferenceID()
    {
        return $this->ReferenceID;
    }
    function setReferenceID($ReferenceID, $charset = 'iso-8859-1')
    {
        $this->ReferenceID = $ReferenceID;
        $this->_elements['ReferenceID']['charset'] = $charset;
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
    function getPaymentType()
    {
        return $this->PaymentType;
    }
    function setPaymentType($PaymentType, $charset = 'iso-8859-1')
    {
        $this->PaymentType = $PaymentType;
        $this->_elements['PaymentType']['charset'] = $charset;
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
    function getReqConfirmShipping()
    {
        return $this->ReqConfirmShipping;
    }
    function setReqConfirmShipping($ReqConfirmShipping, $charset = 'iso-8859-1')
    {
        $this->ReqConfirmShipping = $ReqConfirmShipping;
        $this->_elements['ReqConfirmShipping']['charset'] = $charset;
    }
    function getSoftDescriptor()
    {
        return $this->SoftDescriptor;
    }
    function setSoftDescriptor($SoftDescriptor, $charset = 'iso-8859-1')
    {
        $this->SoftDescriptor = $SoftDescriptor;
        $this->_elements['SoftDescriptor']['charset'] = $charset;
    }
}
