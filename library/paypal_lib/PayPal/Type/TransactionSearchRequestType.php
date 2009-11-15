<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * TransactionSearchRequestType
 *
 * @package PayPal
 */
class TransactionSearchRequestType extends AbstractRequestType
{
    /**
     * The earliest transaction date at which to start the search. No wildcards are
     * allowed.
     */
    var $StartDate;

    /**
     * The latest transaction date to be included in the search
     */
    var $EndDate;

    /**
     * Search by the buyer's email address
     */
    var $Payer;

    /**
     * Search by the receiver's email address. If the merchant account has only one
     * email, this is the primary email. Can also be a non-primary email.
     */
    var $Receiver;

    /**
     * Search by the PayPal Account Optional receipt ID
     */
    var $ReceiptID;

    /**
     * Search by the transaction ID.
     */
    var $TransactionID;

    /**
     * Search by Recurring Payment Profile id. The ProfileID is returned as part of the
     * CreateRecurringPaymentsProfile API response.
     */
    var $ProfileID;

    /**
     * Search by the buyer's name
     */
    var $PayerName;

    /**
     * Search by item number of the purchased goods.
     */
    var $AuctionItemNumber;

    /**
     * Search by invoice identification key, as set by you for the original
     * transaction. InvoiceID searches the invoice records for items sold by the
     * merchant, not the items purchased.
     */
    var $InvoiceID;

    var $CardNumber;

    /**
     * Search by classification of transaction. Some kinds of possible classes of
     * transactions are not searchable with TransactionSearchRequest. You cannot search
     * for bank transfer withdrawals, for example.
     */
    var $TransactionClass;

    /**
     * Search by transaction amount
     */
    var $Amount;

    /**
     * Search by currency code
     */
    var $CurrencyCode;

    /**
     * Search by transaction status
     */
    var $Status;

    function TransactionSearchRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'StartDate' => 
              array (
                'required' => true,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'EndDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Payer' => 
              array (
                'required' => false,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Receiver' => 
              array (
                'required' => false,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ReceiptID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'TransactionID' => 
              array (
                'required' => false,
                'type' => 'TransactionId',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ProfileID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'PayerName' => 
              array (
                'required' => false,
                'type' => 'PersonNameType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'AuctionItemNumber' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'InvoiceID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'CardNumber' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'TransactionClass' => 
              array (
                'required' => false,
                'type' => 'PaymentTransactionClassCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Amount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'CurrencyCode' => 
              array (
                'required' => false,
                'type' => 'CurrencyCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Status' => 
              array (
                'required' => false,
                'type' => 'PaymentTransactionStatusCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getStartDate()
    {
        return $this->StartDate;
    }
    function setStartDate($StartDate, $charset = 'iso-8859-1')
    {
        $this->StartDate = $StartDate;
        $this->_elements['StartDate']['charset'] = $charset;
    }
    function getEndDate()
    {
        return $this->EndDate;
    }
    function setEndDate($EndDate, $charset = 'iso-8859-1')
    {
        $this->EndDate = $EndDate;
        $this->_elements['EndDate']['charset'] = $charset;
    }
    function getPayer()
    {
        return $this->Payer;
    }
    function setPayer($Payer, $charset = 'iso-8859-1')
    {
        $this->Payer = $Payer;
        $this->_elements['Payer']['charset'] = $charset;
    }
    function getReceiver()
    {
        return $this->Receiver;
    }
    function setReceiver($Receiver, $charset = 'iso-8859-1')
    {
        $this->Receiver = $Receiver;
        $this->_elements['Receiver']['charset'] = $charset;
    }
    function getReceiptID()
    {
        return $this->ReceiptID;
    }
    function setReceiptID($ReceiptID, $charset = 'iso-8859-1')
    {
        $this->ReceiptID = $ReceiptID;
        $this->_elements['ReceiptID']['charset'] = $charset;
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
    function getProfileID()
    {
        return $this->ProfileID;
    }
    function setProfileID($ProfileID, $charset = 'iso-8859-1')
    {
        $this->ProfileID = $ProfileID;
        $this->_elements['ProfileID']['charset'] = $charset;
    }
    function getPayerName()
    {
        return $this->PayerName;
    }
    function setPayerName($PayerName, $charset = 'iso-8859-1')
    {
        $this->PayerName = $PayerName;
        $this->_elements['PayerName']['charset'] = $charset;
    }
    function getAuctionItemNumber()
    {
        return $this->AuctionItemNumber;
    }
    function setAuctionItemNumber($AuctionItemNumber, $charset = 'iso-8859-1')
    {
        $this->AuctionItemNumber = $AuctionItemNumber;
        $this->_elements['AuctionItemNumber']['charset'] = $charset;
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
    function getCardNumber()
    {
        return $this->CardNumber;
    }
    function setCardNumber($CardNumber, $charset = 'iso-8859-1')
    {
        $this->CardNumber = $CardNumber;
        $this->_elements['CardNumber']['charset'] = $charset;
    }
    function getTransactionClass()
    {
        return $this->TransactionClass;
    }
    function setTransactionClass($TransactionClass, $charset = 'iso-8859-1')
    {
        $this->TransactionClass = $TransactionClass;
        $this->_elements['TransactionClass']['charset'] = $charset;
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
    function getCurrencyCode()
    {
        return $this->CurrencyCode;
    }
    function setCurrencyCode($CurrencyCode, $charset = 'iso-8859-1')
    {
        $this->CurrencyCode = $CurrencyCode;
        $this->_elements['CurrencyCode']['charset'] = $charset;
    }
    function getStatus()
    {
        return $this->Status;
    }
    function setStatus($Status, $charset = 'iso-8859-1')
    {
        $this->Status = $Status;
        $this->_elements['Status']['charset'] = $charset;
    }
}
