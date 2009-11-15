<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * BusinessInfoType
 * 
 * BusinessInfoType
 *
 * @package PayPal
 */
class BusinessInfoType extends XSDSimpleType
{
    /**
     * Type of business, such as corporation or sole proprietorship
     */
    var $Type;

    /**
     * Official name of business
     */
    var $Name;

    /**
     * Merchant ’s business postal address
     */
    var $Address;

    /**
     * Business ’s primary telephone number
     */
    var $WorkPhone;

    /**
     * Line of business, as defined in the enumerations
     */
    var $Category;

    /**
     * Business sub-category, as defined in the enumerations
     */
    var $SubCategory;

    /**
     * Average transaction price, as defined by the enumerations.
     */
    var $AveragePrice;

    /**
     * Average monthly sales volume, as defined by the enumerations.
     */
    var $AverageMonthlyVolume;

    /**
     * Main sales venue, such as eBay
     */
    var $SalesVenue;

    /**
     * Primary URL of business
     */
    var $Website;

    /**
     * Percentage of revenue attributable to online sales, as defined by the
     * enumerations
     */
    var $RevenueFromOnlineSales;

    /**
     * Date the merchant ’s business was established
     */
    var $BusinessEstablished;

    /**
     * Email address to contact business ’s customer service
     */
    var $CustomerServiceEmail;

    /**
     * Telephone number to contact business ’s customer service
     */
    var $CustomerServicePhone;

    function BusinessInfoType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Type' => 
              array (
                'required' => false,
                'type' => 'BusinessTypeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Name' => 
              array (
                'required' => false,
                'type' => 'NameType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Address' => 
              array (
                'required' => false,
                'type' => 'AddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'WorkPhone' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Category' => 
              array (
                'required' => false,
                'type' => 'BusinessCategoryType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SubCategory' => 
              array (
                'required' => false,
                'type' => 'BusinessSubCategoryType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AveragePrice' => 
              array (
                'required' => false,
                'type' => 'AverageTransactionPriceType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AverageMonthlyVolume' => 
              array (
                'required' => false,
                'type' => 'AverageMonthlyVolumeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SalesVenue' => 
              array (
                'required' => false,
                'type' => 'SalesVenueType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Website' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RevenueFromOnlineSales' => 
              array (
                'required' => false,
                'type' => 'PercentageRevenueFromOnlineSalesType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BusinessEstablished' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CustomerServiceEmail' => 
              array (
                'required' => false,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CustomerServicePhone' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getType()
    {
        return $this->Type;
    }
    function setType($Type, $charset = 'iso-8859-1')
    {
        $this->Type = $Type;
        $this->_elements['Type']['charset'] = $charset;
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
    function getAddress()
    {
        return $this->Address;
    }
    function setAddress($Address, $charset = 'iso-8859-1')
    {
        $this->Address = $Address;
        $this->_elements['Address']['charset'] = $charset;
    }
    function getWorkPhone()
    {
        return $this->WorkPhone;
    }
    function setWorkPhone($WorkPhone, $charset = 'iso-8859-1')
    {
        $this->WorkPhone = $WorkPhone;
        $this->_elements['WorkPhone']['charset'] = $charset;
    }
    function getCategory()
    {
        return $this->Category;
    }
    function setCategory($Category, $charset = 'iso-8859-1')
    {
        $this->Category = $Category;
        $this->_elements['Category']['charset'] = $charset;
    }
    function getSubCategory()
    {
        return $this->SubCategory;
    }
    function setSubCategory($SubCategory, $charset = 'iso-8859-1')
    {
        $this->SubCategory = $SubCategory;
        $this->_elements['SubCategory']['charset'] = $charset;
    }
    function getAveragePrice()
    {
        return $this->AveragePrice;
    }
    function setAveragePrice($AveragePrice, $charset = 'iso-8859-1')
    {
        $this->AveragePrice = $AveragePrice;
        $this->_elements['AveragePrice']['charset'] = $charset;
    }
    function getAverageMonthlyVolume()
    {
        return $this->AverageMonthlyVolume;
    }
    function setAverageMonthlyVolume($AverageMonthlyVolume, $charset = 'iso-8859-1')
    {
        $this->AverageMonthlyVolume = $AverageMonthlyVolume;
        $this->_elements['AverageMonthlyVolume']['charset'] = $charset;
    }
    function getSalesVenue()
    {
        return $this->SalesVenue;
    }
    function setSalesVenue($SalesVenue, $charset = 'iso-8859-1')
    {
        $this->SalesVenue = $SalesVenue;
        $this->_elements['SalesVenue']['charset'] = $charset;
    }
    function getWebsite()
    {
        return $this->Website;
    }
    function setWebsite($Website, $charset = 'iso-8859-1')
    {
        $this->Website = $Website;
        $this->_elements['Website']['charset'] = $charset;
    }
    function getRevenueFromOnlineSales()
    {
        return $this->RevenueFromOnlineSales;
    }
    function setRevenueFromOnlineSales($RevenueFromOnlineSales, $charset = 'iso-8859-1')
    {
        $this->RevenueFromOnlineSales = $RevenueFromOnlineSales;
        $this->_elements['RevenueFromOnlineSales']['charset'] = $charset;
    }
    function getBusinessEstablished()
    {
        return $this->BusinessEstablished;
    }
    function setBusinessEstablished($BusinessEstablished, $charset = 'iso-8859-1')
    {
        $this->BusinessEstablished = $BusinessEstablished;
        $this->_elements['BusinessEstablished']['charset'] = $charset;
    }
    function getCustomerServiceEmail()
    {
        return $this->CustomerServiceEmail;
    }
    function setCustomerServiceEmail($CustomerServiceEmail, $charset = 'iso-8859-1')
    {
        $this->CustomerServiceEmail = $CustomerServiceEmail;
        $this->_elements['CustomerServiceEmail']['charset'] = $charset;
    }
    function getCustomerServicePhone()
    {
        return $this->CustomerServicePhone;
    }
    function setCustomerServicePhone($CustomerServicePhone, $charset = 'iso-8859-1')
    {
        $this->CustomerServicePhone = $CustomerServicePhone;
        $this->_elements['CustomerServicePhone']['charset'] = $charset;
    }
}
