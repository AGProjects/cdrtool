<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * PromotedItemType
 * 
 * Merchandizing info for an Item. This contains a list of crosssell or upsell
 * items.
 *
 * @package PayPal
 */
class PromotedItemType extends XSDSimpleType
{
    /**
     * Item ID for the base item. Based on this item other items are promoted. it is
     * teh only tag that would show up in all calls that use promoted item type. some
     * are not in soap yet, such as get and ser promotion rules
     */
    var $ItemID;

    /**
     * URL for the picture of the promoted item.
     */
    var $PictureURL;

    /**
     * Where to display in the list of items.currentl y even forget and set does not
     * have to be minoccur =0 but if we ever were to do revise promotion tems, it can
     * be omitted
     */
    var $position;

    /**
     * Promotion Price. Price at which the buyer can buy the item now.
     */
    var $PromotionPrice;

    var $PromotionPriceType;

    var $SelectionType;

    /**
     * Item Title for the promoted item.
     */
    var $Title;

    var $ListingType;

    function PromotedItemType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ItemID' => 
              array (
                'required' => true,
                'type' => 'ItemIDType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PictureURL' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'position' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PromotionPrice' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PromotionPriceType' => 
              array (
                'required' => false,
                'type' => 'PromotionItemPriceTypeCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SelectionType' => 
              array (
                'required' => false,
                'type' => 'PromotionItemSelectionCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Title' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ListingType' => 
              array (
                'required' => false,
                'type' => 'ListingTypeCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getItemID()
    {
        return $this->ItemID;
    }
    function setItemID($ItemID, $charset = 'iso-8859-1')
    {
        $this->ItemID = $ItemID;
        $this->_elements['ItemID']['charset'] = $charset;
    }
    function getPictureURL()
    {
        return $this->PictureURL;
    }
    function setPictureURL($PictureURL, $charset = 'iso-8859-1')
    {
        $this->PictureURL = $PictureURL;
        $this->_elements['PictureURL']['charset'] = $charset;
    }
    function getposition()
    {
        return $this->position;
    }
    function setposition($position, $charset = 'iso-8859-1')
    {
        $this->position = $position;
        $this->_elements['position']['charset'] = $charset;
    }
    function getPromotionPrice()
    {
        return $this->PromotionPrice;
    }
    function setPromotionPrice($PromotionPrice, $charset = 'iso-8859-1')
    {
        $this->PromotionPrice = $PromotionPrice;
        $this->_elements['PromotionPrice']['charset'] = $charset;
    }
    function getPromotionPriceType()
    {
        return $this->PromotionPriceType;
    }
    function setPromotionPriceType($PromotionPriceType, $charset = 'iso-8859-1')
    {
        $this->PromotionPriceType = $PromotionPriceType;
        $this->_elements['PromotionPriceType']['charset'] = $charset;
    }
    function getSelectionType()
    {
        return $this->SelectionType;
    }
    function setSelectionType($SelectionType, $charset = 'iso-8859-1')
    {
        $this->SelectionType = $SelectionType;
        $this->_elements['SelectionType']['charset'] = $charset;
    }
    function getTitle()
    {
        return $this->Title;
    }
    function setTitle($Title, $charset = 'iso-8859-1')
    {
        $this->Title = $Title;
        $this->_elements['Title']['charset'] = $charset;
    }
    function getListingType()
    {
        return $this->ListingType;
    }
    function setListingType($ListingType, $charset = 'iso-8859-1')
    {
        $this->ListingType = $ListingType;
        $this->_elements['ListingType']['charset'] = $charset;
    }
}
