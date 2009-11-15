<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * AuctionInfoType
 * 
 * AuctionInfoType Basic information about an auction.
 *
 * @package PayPal
 */
class AuctionInfoType extends XSDSimpleType
{
    /**
     * Customer's auction ID
     */
    var $BuyerID;

    /**
     * Auction's close date
     */
    var $ClosingDate;

    function AuctionInfoType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'BuyerID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ClosingDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
        $this->_attributes = array_merge($this->_attributes,
            array (
              'multiItem' => 
              array (
                'name' => 'multiItem',
                'type' => 'xs:string',
                'use' => 'required',
              ),
            ));
    }

    function getBuyerID()
    {
        return $this->BuyerID;
    }
    function setBuyerID($BuyerID, $charset = 'iso-8859-1')
    {
        $this->BuyerID = $BuyerID;
        $this->_elements['BuyerID']['charset'] = $charset;
    }
    function getClosingDate()
    {
        return $this->ClosingDate;
    }
    function setClosingDate($ClosingDate, $charset = 'iso-8859-1')
    {
        $this->ClosingDate = $ClosingDate;
        $this->_elements['ClosingDate']['charset'] = $charset;
    }
}
