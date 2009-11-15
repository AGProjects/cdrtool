<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * SellerDetailsType
 * 
 * Details about the seller.
 *
 * @package PayPal
 */
class SellerDetailsType extends XSDSimpleType
{
    /**
     * Unique identifier for the seller.
     */
    var $SellerId;

    /**
     * The user name of the user at the marketplaces site.
     */
    var $SellerUserName;

    /**
     * Date when the user registered with the marketplace.
     */
    var $SellerRegistrationDate;

    function SellerDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'SellerId' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SellerUserName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SellerRegistrationDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getSellerId()
    {
        return $this->SellerId;
    }
    function setSellerId($SellerId, $charset = 'iso-8859-1')
    {
        $this->SellerId = $SellerId;
        $this->_elements['SellerId']['charset'] = $charset;
    }
    function getSellerUserName()
    {
        return $this->SellerUserName;
    }
    function setSellerUserName($SellerUserName, $charset = 'iso-8859-1')
    {
        $this->SellerUserName = $SellerUserName;
        $this->_elements['SellerUserName']['charset'] = $charset;
    }
    function getSellerRegistrationDate()
    {
        return $this->SellerRegistrationDate;
    }
    function setSellerRegistrationDate($SellerRegistrationDate, $charset = 'iso-8859-1')
    {
        $this->SellerRegistrationDate = $SellerRegistrationDate;
        $this->_elements['SellerRegistrationDate']['charset'] = $charset;
    }
}
