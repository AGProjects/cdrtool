<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * RefundTransactionRequestType
 *
 * @package PayPal
 */
class RefundTransactionRequestType extends AbstractRequestType
{
    /**
     * Unique identifier of the transaction you are refunding.
     */
    var $TransactionID;

    /**
     * Type of refund you are making
     */
    var $RefundType;

    /**
     * Refund amount.
     */
    var $Amount;

    /**
     * Custom memo about the refund.
     */
    var $Memo;

    function RefundTransactionRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'TransactionID' => 
              array (
                'required' => true,
                'type' => 'TransactionId',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'RefundType' => 
              array (
                'required' => false,
                'type' => 'RefundType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Amount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Memo' => 
              array (
                'required' => false,
                'type' => 'string',
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
    function getRefundType()
    {
        return $this->RefundType;
    }
    function setRefundType($RefundType, $charset = 'iso-8859-1')
    {
        $this->RefundType = $RefundType;
        $this->_elements['RefundType']['charset'] = $charset;
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
    function getMemo()
    {
        return $this->Memo;
    }
    function setMemo($Memo, $charset = 'iso-8859-1')
    {
        $this->Memo = $Memo;
        $this->_elements['Memo']['charset'] = $charset;
    }
}
