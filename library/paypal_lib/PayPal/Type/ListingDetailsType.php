<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ListingDetailsType
 * 
 * Contains the listed item details which consists of following information: .
 *
 * @package PayPal
 */
class ListingDetailsType extends XSDSimpleType
{
    var $Adult;

    var $BindingAuction;

    var $CheckoutEnabled;

    /**
     * Converted value of the BuyItNowPrice in the currency indicated by SiteCurrency.
     * This value must be refreshed every 24 hours to pick up the current conversion
     * rates.
     */
    var $ConvertedBuyItNowPrice;

    /**
     * Converted value of the StartPrice field in the currency indicated by
     * SiteCurrency. This value must be refreshed every 24 hours to pick up the current
     * conversion rates.
     */
    var $ConvertedStartPrice;

    /**
     * Indicates the converted reserve price for a reserve auction. Returned only if
     * DetailLevel = 4. ReservePrice is only returned for auctions with a reserve price
     * where the user calling GetItem is the item's seller. Returned as null for
     * International Fixed Price items. For more information on reserve price auctions,
     * see http://pages.ebay.com/help/basics/f-format.html#1.
     */
    var $ConvertedReservePrice;

    var $HasReservePrice;

    var $RegionName;

    /**
     * Indicates the new ItemID for a relisted item. When an item is relisted, the old
     * (expired) listing is annotated with the new (relist) ItemID. This field only
     * appears when the old listing is retrieved.
     */
    var $RelistedItemID;

    /**
     * The ItemID for the original listing (i.e., OriginalItemID specific to Second
     * Chance Offer items).
     */
    var $SecondChanceOriginalItemID;

    /**
     * Time stamp for the start of the listing (in GMT). For regular items, StartTime
     * is not sent in at listing time.
     */
    var $StartTime;

    /**
     * Time stamp for the end of the listing (in GMT).
     */
    var $EndTime;

    var $ViewItemURL;

    function ListingDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Adult' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BindingAuction' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CheckoutEnabled' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ConvertedBuyItNowPrice' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ConvertedStartPrice' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ConvertedReservePrice' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'HasReservePrice' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RegionName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RelistedItemID' => 
              array (
                'required' => false,
                'type' => 'ItemIDType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SecondChanceOriginalItemID' => 
              array (
                'required' => false,
                'type' => 'ItemIDType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'StartTime' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'EndTime' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ViewItemURL' => 
              array (
                'required' => false,
                'type' => 'anyURI',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getAdult()
    {
        return $this->Adult;
    }
    function setAdult($Adult, $charset = 'iso-8859-1')
    {
        $this->Adult = $Adult;
        $this->_elements['Adult']['charset'] = $charset;
    }
    function getBindingAuction()
    {
        return $this->BindingAuction;
    }
    function setBindingAuction($BindingAuction, $charset = 'iso-8859-1')
    {
        $this->BindingAuction = $BindingAuction;
        $this->_elements['BindingAuction']['charset'] = $charset;
    }
    function getCheckoutEnabled()
    {
        return $this->CheckoutEnabled;
    }
    function setCheckoutEnabled($CheckoutEnabled, $charset = 'iso-8859-1')
    {
        $this->CheckoutEnabled = $CheckoutEnabled;
        $this->_elements['CheckoutEnabled']['charset'] = $charset;
    }
    function getConvertedBuyItNowPrice()
    {
        return $this->ConvertedBuyItNowPrice;
    }
    function setConvertedBuyItNowPrice($ConvertedBuyItNowPrice, $charset = 'iso-8859-1')
    {
        $this->ConvertedBuyItNowPrice = $ConvertedBuyItNowPrice;
        $this->_elements['ConvertedBuyItNowPrice']['charset'] = $charset;
    }
    function getConvertedStartPrice()
    {
        return $this->ConvertedStartPrice;
    }
    function setConvertedStartPrice($ConvertedStartPrice, $charset = 'iso-8859-1')
    {
        $this->ConvertedStartPrice = $ConvertedStartPrice;
        $this->_elements['ConvertedStartPrice']['charset'] = $charset;
    }
    function getConvertedReservePrice()
    {
        return $this->ConvertedReservePrice;
    }
    function setConvertedReservePrice($ConvertedReservePrice, $charset = 'iso-8859-1')
    {
        $this->ConvertedReservePrice = $ConvertedReservePrice;
        $this->_elements['ConvertedReservePrice']['charset'] = $charset;
    }
    function getHasReservePrice()
    {
        return $this->HasReservePrice;
    }
    function setHasReservePrice($HasReservePrice, $charset = 'iso-8859-1')
    {
        $this->HasReservePrice = $HasReservePrice;
        $this->_elements['HasReservePrice']['charset'] = $charset;
    }
    function getRegionName()
    {
        return $this->RegionName;
    }
    function setRegionName($RegionName, $charset = 'iso-8859-1')
    {
        $this->RegionName = $RegionName;
        $this->_elements['RegionName']['charset'] = $charset;
    }
    function getRelistedItemID()
    {
        return $this->RelistedItemID;
    }
    function setRelistedItemID($RelistedItemID, $charset = 'iso-8859-1')
    {
        $this->RelistedItemID = $RelistedItemID;
        $this->_elements['RelistedItemID']['charset'] = $charset;
    }
    function getSecondChanceOriginalItemID()
    {
        return $this->SecondChanceOriginalItemID;
    }
    function setSecondChanceOriginalItemID($SecondChanceOriginalItemID, $charset = 'iso-8859-1')
    {
        $this->SecondChanceOriginalItemID = $SecondChanceOriginalItemID;
        $this->_elements['SecondChanceOriginalItemID']['charset'] = $charset;
    }
    function getStartTime()
    {
        return $this->StartTime;
    }
    function setStartTime($StartTime, $charset = 'iso-8859-1')
    {
        $this->StartTime = $StartTime;
        $this->_elements['StartTime']['charset'] = $charset;
    }
    function getEndTime()
    {
        return $this->EndTime;
    }
    function setEndTime($EndTime, $charset = 'iso-8859-1')
    {
        $this->EndTime = $EndTime;
        $this->_elements['EndTime']['charset'] = $charset;
    }
    function getViewItemURL()
    {
        return $this->ViewItemURL;
    }
    function setViewItemURL($ViewItemURL, $charset = 'iso-8859-1')
    {
        $this->ViewItemURL = $ViewItemURL;
        $this->_elements['ViewItemURL']['charset'] = $charset;
    }
}
