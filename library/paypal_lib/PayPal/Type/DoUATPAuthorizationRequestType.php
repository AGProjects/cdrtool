<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * DoUATPAuthorizationRequestType
 *
 * @package PayPal
 */
class DoUATPAuthorizationRequestType extends AbstractRequestType
{
    var $UATPDetails;

    /**
     * Type of transaction to authorize. The only allowable value is
     */
    var $TransactionEntity;

    /**
     * Amount to authorize.
     */
    var $Amount;

    /**
     * Invoice ID. A pass through.
     */
    var $InvoiceID;

    function DoUATPAuthorizationRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'UATPDetails' => 
              array (
                'required' => true,
                'type' => 'UATPDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TransactionEntity' => 
              array (
                'required' => false,
                'type' => 'TransactionEntityType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Amount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'InvoiceID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getUATPDetails()
    {
        return $this->UATPDetails;
    }
    function setUATPDetails($UATPDetails, $charset = 'iso-8859-1')
    {
        $this->UATPDetails = $UATPDetails;
        $this->_elements['UATPDetails']['charset'] = $charset;
    }
    function getTransactionEntity()
    {
        return $this->TransactionEntity;
    }
    function setTransactionEntity($TransactionEntity, $charset = 'iso-8859-1')
    {
        $this->TransactionEntity = $TransactionEntity;
        $this->_elements['TransactionEntity']['charset'] = $charset;
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
    function getInvoiceID()
    {
        return $this->InvoiceID;
    }
    function setInvoiceID($InvoiceID, $charset = 'iso-8859-1')
    {
        $this->InvoiceID = $InvoiceID;
        $this->_elements['InvoiceID']['charset'] = $charset;
    }
}
