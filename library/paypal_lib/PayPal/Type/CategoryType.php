<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * CategoryType
 * 
 * Container for data on the primary category of listing.
 *
 * @package PayPal
 */
class CategoryType extends XSDSimpleType
{
    var $AutoPayEnabled;

    var $B2BVATEnabled;

    var $CatalogEnabled;

    var $CategoryID;

    var $CategoryLevel;

    var $CategoryName;

    var $CategoryParentID;

    var $CategoryParentName;

    /**
     * CSIDList is not present if Attributes enabled.
     */
    var $CSIDList;

    var $Expired;

    var $IntlAutosFixedCat;

    var $LeafCategory;

    var $Virtual;

    function CategoryType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'AutoPayEnabled' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'B2BVATEnabled' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CatalogEnabled' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CategoryID' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CategoryLevel' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CategoryName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CategoryParentID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CategoryParentName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CSIDList' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Expired' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'IntlAutosFixedCat' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LeafCategory' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Virtual' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getAutoPayEnabled()
    {
        return $this->AutoPayEnabled;
    }
    function setAutoPayEnabled($AutoPayEnabled, $charset = 'iso-8859-1')
    {
        $this->AutoPayEnabled = $AutoPayEnabled;
        $this->_elements['AutoPayEnabled']['charset'] = $charset;
    }
    function getB2BVATEnabled()
    {
        return $this->B2BVATEnabled;
    }
    function setB2BVATEnabled($B2BVATEnabled, $charset = 'iso-8859-1')
    {
        $this->B2BVATEnabled = $B2BVATEnabled;
        $this->_elements['B2BVATEnabled']['charset'] = $charset;
    }
    function getCatalogEnabled()
    {
        return $this->CatalogEnabled;
    }
    function setCatalogEnabled($CatalogEnabled, $charset = 'iso-8859-1')
    {
        $this->CatalogEnabled = $CatalogEnabled;
        $this->_elements['CatalogEnabled']['charset'] = $charset;
    }
    function getCategoryID()
    {
        return $this->CategoryID;
    }
    function setCategoryID($CategoryID, $charset = 'iso-8859-1')
    {
        $this->CategoryID = $CategoryID;
        $this->_elements['CategoryID']['charset'] = $charset;
    }
    function getCategoryLevel()
    {
        return $this->CategoryLevel;
    }
    function setCategoryLevel($CategoryLevel, $charset = 'iso-8859-1')
    {
        $this->CategoryLevel = $CategoryLevel;
        $this->_elements['CategoryLevel']['charset'] = $charset;
    }
    function getCategoryName()
    {
        return $this->CategoryName;
    }
    function setCategoryName($CategoryName, $charset = 'iso-8859-1')
    {
        $this->CategoryName = $CategoryName;
        $this->_elements['CategoryName']['charset'] = $charset;
    }
    function getCategoryParentID()
    {
        return $this->CategoryParentID;
    }
    function setCategoryParentID($CategoryParentID, $charset = 'iso-8859-1')
    {
        $this->CategoryParentID = $CategoryParentID;
        $this->_elements['CategoryParentID']['charset'] = $charset;
    }
    function getCategoryParentName()
    {
        return $this->CategoryParentName;
    }
    function setCategoryParentName($CategoryParentName, $charset = 'iso-8859-1')
    {
        $this->CategoryParentName = $CategoryParentName;
        $this->_elements['CategoryParentName']['charset'] = $charset;
    }
    function getCSIDList()
    {
        return $this->CSIDList;
    }
    function setCSIDList($CSIDList, $charset = 'iso-8859-1')
    {
        $this->CSIDList = $CSIDList;
        $this->_elements['CSIDList']['charset'] = $charset;
    }
    function getExpired()
    {
        return $this->Expired;
    }
    function setExpired($Expired, $charset = 'iso-8859-1')
    {
        $this->Expired = $Expired;
        $this->_elements['Expired']['charset'] = $charset;
    }
    function getIntlAutosFixedCat()
    {
        return $this->IntlAutosFixedCat;
    }
    function setIntlAutosFixedCat($IntlAutosFixedCat, $charset = 'iso-8859-1')
    {
        $this->IntlAutosFixedCat = $IntlAutosFixedCat;
        $this->_elements['IntlAutosFixedCat']['charset'] = $charset;
    }
    function getLeafCategory()
    {
        return $this->LeafCategory;
    }
    function setLeafCategory($LeafCategory, $charset = 'iso-8859-1')
    {
        $this->LeafCategory = $LeafCategory;
        $this->_elements['LeafCategory']['charset'] = $charset;
    }
    function getVirtual()
    {
        return $this->Virtual;
    }
    function setVirtual($Virtual, $charset = 'iso-8859-1')
    {
        $this->Virtual = $Virtual;
        $this->_elements['Virtual']['charset'] = $charset;
    }
}
