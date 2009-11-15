<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * SellingStatusType
 * 
 * Contains the listed items price details which consists of following information:
 * BuyItNowPrice, ConvertedBuyItNowPrice, ConvertedPrice, ConvertedStartPrice,
 * CurrentPrice, MinimumToBid, ReservePrice, and StartPrice. need to take in
 * account get seller events when defining minoccurs = 0
 *
 * @package PayPal
 */
class SellingStatusType extends XSDSimpleType
{
    /**
     * Number of bids placed so far against the item. Not returned for International
     * Fixed Price items.
     */
    var $BidCount;

    /**
     * Smallest amount a bid must be above the current high bid. Not returned
     * International Fixed Price items.
     */
    var $BidIncrement;

    /**
     * Converted current price of listed item.
     */
    var $ConvertedCurrentPrice;

    /**
     * For auction-format listings, current minimum asking price or the current highest
     * bid for the item if bids have been placed. Shows minimum bid if no bids have
     * been placed against the item. This field does not reflect the actual current
     * price of the item if it's a Type=7 or Type=9 (Fixed Price) item and the price
     * has been revised. (See StartPrice for revised asking price.)
     */
    var $CurrentPrice;

    /**
     * Contains one User node representing the current high bidder. GetItem returns a
     * high bidder for auctions that have ended and have a winning bidder. For Fixed
     * Price listings, in-progress auctions, or auction items that received no bids,
     * GetItem returns a HighBidder node with empty tags.
     */
    var $HighBidder;

    /**
     * Applicable to ad-format items only. Indicates how many leads to potential buyers
     * are associated with this item. For other item types (other than ad-format
     * items), returns a value of 0 (zero).
     */
    var $LeadCount;

    /**
     * Minimum acceptable bid for the item. Not returned for International Fixed Price
     * items.
     */
    var $MinimumToBid;

    /**
     * Number of items purchased so far. (Subtract from the value returned in the
     * Quantity field to calculate the number of items remaining.)
     */
    var $QuantitySold;

    /**
     * Returns true if the reserve price was met or no reserve price was specified.
     */
    var $ReserveMet;

    var $SecondChanceEligible;

    function SellingStatusType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'BidCount' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BidIncrement' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ConvertedCurrentPrice' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CurrentPrice' => 
              array (
                'required' => true,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'HighBidder' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LeadCount' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MinimumToBid' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'QuantitySold' => 
              array (
                'required' => true,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReserveMet' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SecondChanceEligible' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getBidCount()
    {
        return $this->BidCount;
    }
    function setBidCount($BidCount, $charset = 'iso-8859-1')
    {
        $this->BidCount = $BidCount;
        $this->_elements['BidCount']['charset'] = $charset;
    }
    function getBidIncrement()
    {
        return $this->BidIncrement;
    }
    function setBidIncrement($BidIncrement, $charset = 'iso-8859-1')
    {
        $this->BidIncrement = $BidIncrement;
        $this->_elements['BidIncrement']['charset'] = $charset;
    }
    function getConvertedCurrentPrice()
    {
        return $this->ConvertedCurrentPrice;
    }
    function setConvertedCurrentPrice($ConvertedCurrentPrice, $charset = 'iso-8859-1')
    {
        $this->ConvertedCurrentPrice = $ConvertedCurrentPrice;
        $this->_elements['ConvertedCurrentPrice']['charset'] = $charset;
    }
    function getCurrentPrice()
    {
        return $this->CurrentPrice;
    }
    function setCurrentPrice($CurrentPrice, $charset = 'iso-8859-1')
    {
        $this->CurrentPrice = $CurrentPrice;
        $this->_elements['CurrentPrice']['charset'] = $charset;
    }
    function getHighBidder()
    {
        return $this->HighBidder;
    }
    function setHighBidder($HighBidder, $charset = 'iso-8859-1')
    {
        $this->HighBidder = $HighBidder;
        $this->_elements['HighBidder']['charset'] = $charset;
    }
    function getLeadCount()
    {
        return $this->LeadCount;
    }
    function setLeadCount($LeadCount, $charset = 'iso-8859-1')
    {
        $this->LeadCount = $LeadCount;
        $this->_elements['LeadCount']['charset'] = $charset;
    }
    function getMinimumToBid()
    {
        return $this->MinimumToBid;
    }
    function setMinimumToBid($MinimumToBid, $charset = 'iso-8859-1')
    {
        $this->MinimumToBid = $MinimumToBid;
        $this->_elements['MinimumToBid']['charset'] = $charset;
    }
    function getQuantitySold()
    {
        return $this->QuantitySold;
    }
    function setQuantitySold($QuantitySold, $charset = 'iso-8859-1')
    {
        $this->QuantitySold = $QuantitySold;
        $this->_elements['QuantitySold']['charset'] = $charset;
    }
    function getReserveMet()
    {
        return $this->ReserveMet;
    }
    function setReserveMet($ReserveMet, $charset = 'iso-8859-1')
    {
        $this->ReserveMet = $ReserveMet;
        $this->_elements['ReserveMet']['charset'] = $charset;
    }
    function getSecondChanceEligible()
    {
        return $this->SecondChanceEligible;
    }
    function setSecondChanceEligible($SecondChanceEligible, $charset = 'iso-8859-1')
    {
        $this->SecondChanceEligible = $SecondChanceEligible;
        $this->_elements['SecondChanceEligible']['charset'] = $charset;
    }
}
