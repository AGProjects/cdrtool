<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * EbayItemPaymentDetailsItemType
 * 
 * EbayItemPaymentDetailsItemType - Type declaration to be used by other schemas.
 * Information about an Ebay Payment Item.
 *
 * @package PayPal
 */
class EbayItemPaymentDetailsItemType extends XSDSimpleType
{
    /**
     * Auction ItemNumber.
     */
    var $ItemNumber;

    /**
     * Auction Transaction ID.
     */
    var $AuctionTransactionId;

    /**
     * Ebay Order ID.
     */
    var $OrderId;

    /**
     * Ebay Cart ID.
     */
    var $CartID;

    function EbayItemPaymentDetailsItemType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ItemNumber' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AuctionTransactionId' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OrderId' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CartID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getItemNumber()
    {
        return $this->ItemNumber;
    }
    function setItemNumber($ItemNumber, $charset = 'iso-8859-1')
    {
        $this->ItemNumber = $ItemNumber;
        $this->_elements['ItemNumber']['charset'] = $charset;
    }
    function getAuctionTransactionId()
    {
        return $this->AuctionTransactionId;
    }
    function setAuctionTransactionId($AuctionTransactionId, $charset = 'iso-8859-1')
    {
        $this->AuctionTransactionId = $AuctionTransactionId;
        $this->_elements['AuctionTransactionId']['charset'] = $charset;
    }
    function getOrderId()
    {
        return $this->OrderId;
    }
    function setOrderId($OrderId, $charset = 'iso-8859-1')
    {
        $this->OrderId = $OrderId;
        $this->_elements['OrderId']['charset'] = $charset;
    }
    function getCartID()
    {
        return $this->CartID;
    }
    function setCartID($CartID, $charset = 'iso-8859-1')
    {
        $this->CartID = $CartID;
        $this->_elements['CartID']['charset'] = $charset;
    }
}
