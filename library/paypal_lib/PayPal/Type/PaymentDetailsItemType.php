<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * PaymentDetailsItemType
 * 
 * PaymentDetailsItemType Information about a Payment Item.
 *
 * @package PayPal
 */
class PaymentDetailsItemType extends XSDSimpleType
{
    /**
     * Item name.
     */
    var $Name;

    /**
     * Item number.
     */
    var $Number;

    /**
     * Item quantity.
     */
    var $Quantity;

    /**
     * Item sales tax.
     */
    var $Tax;

    /**
     * Cost of item You must set the currencyID attribute to one of the three-character
     * currency codes for any of the supported PayPal currencies.
     */
    var $Amount;

    /**
     * Ebay specific details.
     */
    var $EbayItemPaymentDetailsItem;

    /**
     * Promotional financing code for item. Part of the Merchant Services Promotion
     * Financing feature.
     */
    var $PromoCode;

    var $ProductCategory;

    /**
     * Item description.
     */
    var $Description;

    /**
     * Information about the Item weight.
     */
    var $ItemWeight;

    /**
     * Information about the Item length.
     */
    var $ItemLength;

    /**
     * Information about the Item width.
     */
    var $ItemWidth;

    /**
     * Information about the Item height.
     */
    var $ItemHeight;

    /**
     * URL for the item.
     */
    var $ItemURL;

    /**
     * Enhanced data for each item in the cart.
     */
    var $EnhancedItemData;

    function PaymentDetailsItemType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Name' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Number' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Quantity' => 
              array (
                'required' => false,
                'type' => 'integer',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Tax' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Amount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'EbayItemPaymentDetailsItem' => 
              array (
                'required' => false,
                'type' => 'EbayItemPaymentDetailsItemType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PromoCode' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ProductCategory' => 
              array (
                'required' => false,
                'type' => 'ProductCategoryType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Description' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemWeight' => 
              array (
                'required' => false,
                'type' => 'MeasureType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemLength' => 
              array (
                'required' => false,
                'type' => 'MeasureType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemWidth' => 
              array (
                'required' => false,
                'type' => 'MeasureType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemHeight' => 
              array (
                'required' => false,
                'type' => 'MeasureType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemURL' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'EnhancedItemData' => 
              array (
                'required' => false,
                'type' => 'EnhancedItemDataType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getName()
    {
        return $this->Name;
    }
    function setName($Name, $charset = 'iso-8859-1')
    {
        $this->Name = $Name;
        $this->_elements['Name']['charset'] = $charset;
    }
    function getNumber()
    {
        return $this->Number;
    }
    function setNumber($Number, $charset = 'iso-8859-1')
    {
        $this->Number = $Number;
        $this->_elements['Number']['charset'] = $charset;
    }
    function getQuantity()
    {
        return $this->Quantity;
    }
    function setQuantity($Quantity, $charset = 'iso-8859-1')
    {
        $this->Quantity = $Quantity;
        $this->_elements['Quantity']['charset'] = $charset;
    }
    function getTax()
    {
        return $this->Tax;
    }
    function setTax($Tax, $charset = 'iso-8859-1')
    {
        $this->Tax = $Tax;
        $this->_elements['Tax']['charset'] = $charset;
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
    function getEbayItemPaymentDetailsItem()
    {
        return $this->EbayItemPaymentDetailsItem;
    }
    function setEbayItemPaymentDetailsItem($EbayItemPaymentDetailsItem, $charset = 'iso-8859-1')
    {
        $this->EbayItemPaymentDetailsItem = $EbayItemPaymentDetailsItem;
        $this->_elements['EbayItemPaymentDetailsItem']['charset'] = $charset;
    }
    function getPromoCode()
    {
        return $this->PromoCode;
    }
    function setPromoCode($PromoCode, $charset = 'iso-8859-1')
    {
        $this->PromoCode = $PromoCode;
        $this->_elements['PromoCode']['charset'] = $charset;
    }
    function getProductCategory()
    {
        return $this->ProductCategory;
    }
    function setProductCategory($ProductCategory, $charset = 'iso-8859-1')
    {
        $this->ProductCategory = $ProductCategory;
        $this->_elements['ProductCategory']['charset'] = $charset;
    }
    function getDescription()
    {
        return $this->Description;
    }
    function setDescription($Description, $charset = 'iso-8859-1')
    {
        $this->Description = $Description;
        $this->_elements['Description']['charset'] = $charset;
    }
    function getItemWeight()
    {
        return $this->ItemWeight;
    }
    function setItemWeight($ItemWeight, $charset = 'iso-8859-1')
    {
        $this->ItemWeight = $ItemWeight;
        $this->_elements['ItemWeight']['charset'] = $charset;
    }
    function getItemLength()
    {
        return $this->ItemLength;
    }
    function setItemLength($ItemLength, $charset = 'iso-8859-1')
    {
        $this->ItemLength = $ItemLength;
        $this->_elements['ItemLength']['charset'] = $charset;
    }
    function getItemWidth()
    {
        return $this->ItemWidth;
    }
    function setItemWidth($ItemWidth, $charset = 'iso-8859-1')
    {
        $this->ItemWidth = $ItemWidth;
        $this->_elements['ItemWidth']['charset'] = $charset;
    }
    function getItemHeight()
    {
        return $this->ItemHeight;
    }
    function setItemHeight($ItemHeight, $charset = 'iso-8859-1')
    {
        $this->ItemHeight = $ItemHeight;
        $this->_elements['ItemHeight']['charset'] = $charset;
    }
    function getItemURL()
    {
        return $this->ItemURL;
    }
    function setItemURL($ItemURL, $charset = 'iso-8859-1')
    {
        $this->ItemURL = $ItemURL;
        $this->_elements['ItemURL']['charset'] = $charset;
    }
    function getEnhancedItemData()
    {
        return $this->EnhancedItemData;
    }
    function setEnhancedItemData($EnhancedItemData, $charset = 'iso-8859-1')
    {
        $this->EnhancedItemData = $EnhancedItemData;
        $this->_elements['EnhancedItemData']['charset'] = $charset;
    }
}
