<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * TransactionType
 * 
 * Contains information about a single transaction. A transaction contains
 * information about the sale of a particular item. The system creates a
 * transaction when a buyer has made a purchase (Fixed Price items) or is the
 * winning bidder (BIN and auction items). A listing can be associated with one or
 * more transactions in these cases: Multi-Item Fixed Price Listings Dutch Auction
 * Listings A listing is associated with a single transaction in these cases:
 * Single-Item Fixed Price Listings Single-Item Auction Listings
 *
 * @package PayPal
 */
class TransactionType extends XSDSimpleType
{
    /**
     * The amount the buyer paid for the item or agreed to pay, depending on how far
     * into the checkout process the item is. If the seller allowed the buyer to change
     * the item total, the buyer is able to change the total until the time that the
     * transaction's status moves to Complete. Determine whether the buyer changed the
     * amount by calling GetSellerTransactions or GetSellerTransactions and comparing
     * the AmountPaid value to what the seller expected. For Motors items, AmountPaid
     * is the amount paid by the buyer for the deposit.
     */
    var $AmountPaid;

    /**
     * Container for buyer data.
     */
    var $Buyer;

    /**
     * Includes shipping payment data.
     */
    var $ShippingDetails;

    /**
     * Value returned in the Transaction/AmountPaid element, converted to the currency
     * indicated by SiteCurrency.
     */
    var $ConvertedAmountPaid;

    /**
     * Value returned in the Transaction/TransactionPrice element, converted to the
     * currency indicated by SiteCurrency.
     */
    var $ConvertedTransactionPrice;

    /**
     * For fixed-price, Stores, and BIN items indicates when the purchase (or BIN)
     * occurred. For all other item types indicates when the transaction was created
     * (the time when checkout was initiated).
     */
    var $CreatedDate;

    /**
     * Deposit type for Motors items. If item is not a Motors item, then returns a
     * DepositType of None. Possible values: None Other Method Fast Deposit
     */
    var $DepositType;

    /**
     * Item info associated with the transaction.
     */
    var $Item;

    /**
     * Contains the number of individual items the buyer purchased in the transaction.
     */
    var $QuantityPurchased;

    /**
     * Shipping cost totals shown to user (for both flat and calculated rates).
     */
    var $ShippingHandlingTotal;

    /**
     * Container node for transaction status data.
     */
    var $Status;

    /**
     * Unique identifier for a transaction. Returns 0 when Type=1 (Chinese auction).
     * Typically, an ItemID and a TransactionID uniquely identify a checkout
     * transaction.
     */
    var $TransactionID;

    /**
     * Unique identifier for an authorization.
     */
    var $AuthorizationID;

    /**
     * Price of the item, before shipping and sales tax. For Motors, TransactionPrice
     * is the deposit amount.
     */
    var $TransactionPrice;

    /**
     * VAT rate for the item, if the item price includes the VAT rate. Specify the
     * VATPercent if you want include the net price in addition to the gross price in
     * the listing. VAT rates vary depending on the item and on the user's country of
     * residence; therefore a business seller is responsible for entering the correct
     * VAT rate (it will not be calculated by eBay).
     */
    var $VATPercent;

    function TransactionType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'AmountPaid' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Buyer' => 
              array (
                'required' => false,
                'type' => 'UserType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingDetails' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ConvertedAmountPaid' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ConvertedTransactionPrice' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CreatedDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'DepositType' => 
              array (
                'required' => false,
                'type' => 'DepositTypeCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Item' => 
              array (
                'required' => false,
                'type' => 'ItemType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'QuantityPurchased' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingHandlingTotal' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Status' => 
              array (
                'required' => false,
                'type' => 'TransactionStatusType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TransactionID' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AuthorizationID' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TransactionPrice' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'VATPercent' => 
              array (
                'required' => false,
                'type' => 'decimal',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getAmountPaid()
    {
        return $this->AmountPaid;
    }
    function setAmountPaid($AmountPaid, $charset = 'iso-8859-1')
    {
        $this->AmountPaid = $AmountPaid;
        $this->_elements['AmountPaid']['charset'] = $charset;
    }
    function getBuyer()
    {
        return $this->Buyer;
    }
    function setBuyer($Buyer, $charset = 'iso-8859-1')
    {
        $this->Buyer = $Buyer;
        $this->_elements['Buyer']['charset'] = $charset;
    }
    function getShippingDetails()
    {
        return $this->ShippingDetails;
    }
    function setShippingDetails($ShippingDetails, $charset = 'iso-8859-1')
    {
        $this->ShippingDetails = $ShippingDetails;
        $this->_elements['ShippingDetails']['charset'] = $charset;
    }
    function getConvertedAmountPaid()
    {
        return $this->ConvertedAmountPaid;
    }
    function setConvertedAmountPaid($ConvertedAmountPaid, $charset = 'iso-8859-1')
    {
        $this->ConvertedAmountPaid = $ConvertedAmountPaid;
        $this->_elements['ConvertedAmountPaid']['charset'] = $charset;
    }
    function getConvertedTransactionPrice()
    {
        return $this->ConvertedTransactionPrice;
    }
    function setConvertedTransactionPrice($ConvertedTransactionPrice, $charset = 'iso-8859-1')
    {
        $this->ConvertedTransactionPrice = $ConvertedTransactionPrice;
        $this->_elements['ConvertedTransactionPrice']['charset'] = $charset;
    }
    function getCreatedDate()
    {
        return $this->CreatedDate;
    }
    function setCreatedDate($CreatedDate, $charset = 'iso-8859-1')
    {
        $this->CreatedDate = $CreatedDate;
        $this->_elements['CreatedDate']['charset'] = $charset;
    }
    function getDepositType()
    {
        return $this->DepositType;
    }
    function setDepositType($DepositType, $charset = 'iso-8859-1')
    {
        $this->DepositType = $DepositType;
        $this->_elements['DepositType']['charset'] = $charset;
    }
    function getItem()
    {
        return $this->Item;
    }
    function setItem($Item, $charset = 'iso-8859-1')
    {
        $this->Item = $Item;
        $this->_elements['Item']['charset'] = $charset;
    }
    function getQuantityPurchased()
    {
        return $this->QuantityPurchased;
    }
    function setQuantityPurchased($QuantityPurchased, $charset = 'iso-8859-1')
    {
        $this->QuantityPurchased = $QuantityPurchased;
        $this->_elements['QuantityPurchased']['charset'] = $charset;
    }
    function getShippingHandlingTotal()
    {
        return $this->ShippingHandlingTotal;
    }
    function setShippingHandlingTotal($ShippingHandlingTotal, $charset = 'iso-8859-1')
    {
        $this->ShippingHandlingTotal = $ShippingHandlingTotal;
        $this->_elements['ShippingHandlingTotal']['charset'] = $charset;
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
    function getTransactionID()
    {
        return $this->TransactionID;
    }
    function setTransactionID($TransactionID, $charset = 'iso-8859-1')
    {
        $this->TransactionID = $TransactionID;
        $this->_elements['TransactionID']['charset'] = $charset;
    }
    function getAuthorizationID()
    {
        return $this->AuthorizationID;
    }
    function setAuthorizationID($AuthorizationID, $charset = 'iso-8859-1')
    {
        $this->AuthorizationID = $AuthorizationID;
        $this->_elements['AuthorizationID']['charset'] = $charset;
    }
    function getTransactionPrice()
    {
        return $this->TransactionPrice;
    }
    function setTransactionPrice($TransactionPrice, $charset = 'iso-8859-1')
    {
        $this->TransactionPrice = $TransactionPrice;
        $this->_elements['TransactionPrice']['charset'] = $charset;
    }
    function getVATPercent()
    {
        return $this->VATPercent;
    }
    function setVATPercent($VATPercent, $charset = 'iso-8859-1')
    {
        $this->VATPercent = $VATPercent;
        $this->_elements['VATPercent']['charset'] = $charset;
    }
}
