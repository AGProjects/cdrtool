<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * CrossPromotionsType
 * 
 * Merchandizing info for an Item. This contains a list of crosssell or upsell
 * items. PrimaryScheme, PromotionMethod,SellerId,ItemId, ShippingDiscount do not
 * have be min occur 0
 *
 * @package PayPal
 */
class CrossPromotionsType extends XSDSimpleType
{
    /**
     * Item ID for the base item. Based on this item other items are promoted.
     */
    var $ItemID;

    var $PrimaryScheme;

    var $PromotionMethod;

    /**
     * Id of the Seller who is promoting this item.
     */
    var $SellerID;

    /**
     * Shipping Discount offered or not by the seller.
     */
    var $ShippingDiscount;

    /**
     * Key of the Seller who is promoting this item.
     */
    var $SellerKey;

    /**
     * Store Name for the seller.
     */
    var $StoreName;

    var $PromotedItem;

    function CrossPromotionsType()
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
              'PrimaryScheme' => 
              array (
                'required' => true,
                'type' => 'PromotionSchemeCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PromotionMethod' => 
              array (
                'required' => true,
                'type' => 'PromotionMethodCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SellerID' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingDiscount' => 
              array (
                'required' => true,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SellerKey' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'StoreName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PromotedItem' => 
              array (
                'required' => true,
                'type' => 'PromotedItemType',
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
    function getPrimaryScheme()
    {
        return $this->PrimaryScheme;
    }
    function setPrimaryScheme($PrimaryScheme, $charset = 'iso-8859-1')
    {
        $this->PrimaryScheme = $PrimaryScheme;
        $this->_elements['PrimaryScheme']['charset'] = $charset;
    }
    function getPromotionMethod()
    {
        return $this->PromotionMethod;
    }
    function setPromotionMethod($PromotionMethod, $charset = 'iso-8859-1')
    {
        $this->PromotionMethod = $PromotionMethod;
        $this->_elements['PromotionMethod']['charset'] = $charset;
    }
    function getSellerID()
    {
        return $this->SellerID;
    }
    function setSellerID($SellerID, $charset = 'iso-8859-1')
    {
        $this->SellerID = $SellerID;
        $this->_elements['SellerID']['charset'] = $charset;
    }
    function getShippingDiscount()
    {
        return $this->ShippingDiscount;
    }
    function setShippingDiscount($ShippingDiscount, $charset = 'iso-8859-1')
    {
        $this->ShippingDiscount = $ShippingDiscount;
        $this->_elements['ShippingDiscount']['charset'] = $charset;
    }
    function getSellerKey()
    {
        return $this->SellerKey;
    }
    function setSellerKey($SellerKey, $charset = 'iso-8859-1')
    {
        $this->SellerKey = $SellerKey;
        $this->_elements['SellerKey']['charset'] = $charset;
    }
    function getStoreName()
    {
        return $this->StoreName;
    }
    function setStoreName($StoreName, $charset = 'iso-8859-1')
    {
        $this->StoreName = $StoreName;
        $this->_elements['StoreName']['charset'] = $charset;
    }
    function getPromotedItem()
    {
        return $this->PromotedItem;
    }
    function setPromotedItem($PromotedItem, $charset = 'iso-8859-1')
    {
        $this->PromotedItem = $PromotedItem;
        $this->_elements['PromotedItem']['charset'] = $charset;
    }
}
