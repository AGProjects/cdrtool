<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * PaymentItemInfoType
 * 
 * PaymentItemInfoType Information about a PayPal item.
 *
 * @package PayPal
 */
class PaymentItemInfoType extends XSDSimpleType
{
    /**
     * Invoice number you set in the original transaction.
     */
    var $InvoiceID;

    /**
     * Custom field you set in the original transaction.
     */
    var $Custom;

    /**
     * Memo entered by your customer in PayPal Website Payments note field.
     */
    var $Memo;

    /**
     * Amount of tax charged on transaction
     */
    var $SalesTax;

    /**
     * Details about the indivudal purchased item
     */
    var $PaymentItem;

    /**
     * Information about the transaction if it was created via PayPal Subcriptions
     */
    var $Subscription;

    /**
     * Information about the transaction if it was created via an auction
     */
    var $Auction;

    function PaymentItemInfoType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'InvoiceID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Custom' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Memo' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SalesTax' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentItem' => 
              array (
                'required' => false,
                'type' => 'PaymentItemType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Subscription' => 
              array (
                'required' => false,
                'type' => 'SubscriptionInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Auction' => 
              array (
                'required' => false,
                'type' => 'AuctionInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
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
    function getCustom()
    {
        return $this->Custom;
    }
    function setCustom($Custom, $charset = 'iso-8859-1')
    {
        $this->Custom = $Custom;
        $this->_elements['Custom']['charset'] = $charset;
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
    function getSalesTax()
    {
        return $this->SalesTax;
    }
    function setSalesTax($SalesTax, $charset = 'iso-8859-1')
    {
        $this->SalesTax = $SalesTax;
        $this->_elements['SalesTax']['charset'] = $charset;
    }
    function getPaymentItem()
    {
        return $this->PaymentItem;
    }
    function setPaymentItem($PaymentItem, $charset = 'iso-8859-1')
    {
        $this->PaymentItem = $PaymentItem;
        $this->_elements['PaymentItem']['charset'] = $charset;
    }
    function getSubscription()
    {
        return $this->Subscription;
    }
    function setSubscription($Subscription, $charset = 'iso-8859-1')
    {
        $this->Subscription = $Subscription;
        $this->_elements['Subscription']['charset'] = $charset;
    }
    function getAuction()
    {
        return $this->Auction;
    }
    function setAuction($Auction, $charset = 'iso-8859-1')
    {
        $this->Auction = $Auction;
        $this->_elements['Auction']['charset'] = $charset;
    }
}
