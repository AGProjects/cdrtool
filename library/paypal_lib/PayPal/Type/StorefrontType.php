<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * StorefrontType
 * 
 * Contains the eBay Stores-specific item attributes department number and store
 * location. StorefrontInfo is shown for any item that belongs to an eBay Store
 * owner, regardless of whether it is fixed price or auction type. Returned as null
 * for international fixed price items.
 *
 * @package PayPal
 */
class StorefrontType extends XSDSimpleType
{
    /**
     * assumed this type is specific to add/get/revise item, then each StorefrontType
     * nust have category id, for store details this node makes no sense to use
     */
    var $StoreCategoryID;

    /**
     * in case or revise item for example - to change store category (department) you
     * do not need to change store URL, so it will notbe in request
     */
    var $StoreURL;

    function StorefrontType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'StoreCategoryID' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'StoreURL' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getStoreCategoryID()
    {
        return $this->StoreCategoryID;
    }
    function setStoreCategoryID($StoreCategoryID, $charset = 'iso-8859-1')
    {
        $this->StoreCategoryID = $StoreCategoryID;
        $this->_elements['StoreCategoryID']['charset'] = $charset;
    }
    function getStoreURL()
    {
        return $this->StoreURL;
    }
    function setStoreURL($StoreURL, $charset = 'iso-8859-1')
    {
        $this->StoreURL = $StoreURL;
        $this->_elements['StoreURL']['charset'] = $charset;
    }
}
