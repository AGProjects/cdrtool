<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * DoAuthorizationRequestType
 *
 * @package PayPal
 */
class DoAuthorizationRequestType extends AbstractRequestType
{
    /**
     * The value of the order ’s transaction identification number returned by a
     * PayPal product.
     */
    var $TransactionID;

    /**
     * Type of transaction to authorize. The only allowable value is
     */
    var $TransactionEntity;

    /**
     * Amount to authorize.
     */
    var $Amount;

    function DoAuthorizationRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'TransactionID' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
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
}
