<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * DoAuthorizationResponseType
 *
 * @package PayPal
 */
class DoAuthorizationResponseType extends AbstractResponseType
{
    /**
     * An authorization identification number.
     */
    var $TransactionID;

    /**
     * The amount and currency you specified in the request.
     */
    var $Amount;

    var $AuthorizationInfo;

    function DoAuthorizationResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'TransactionID' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Amount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'AuthorizationInfo' => 
              array (
                'required' => false,
                'type' => 'AuthorizationInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getTransactionID()
    {
        return $this->TransactionID;
    }
    function setTransactionID($TransactionID, $charset = 'iso-8859-1')
    {
        $this->TransactionID = $TransactionID;
        $this->_elements['TransactionID']['charset'] = $charset;
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
    function getAuthorizationInfo()
    {
        return $this->AuthorizationInfo;
    }
    function setAuthorizationInfo($AuthorizationInfo, $charset = 'iso-8859-1')
    {
        $this->AuthorizationInfo = $AuthorizationInfo;
        $this->_elements['AuthorizationInfo']['charset'] = $charset;
    }
}
