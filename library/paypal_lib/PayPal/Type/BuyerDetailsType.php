<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * BuyerDetailsType
 * 
 * Details about the buyer's account passed in by the merchant or partner.
 *
 * @package PayPal
 */
class BuyerDetailsType extends XSDSimpleType
{
    /**
     * The client's unique ID for this user.
     */
    var $BuyerId;

    /**
     * The user name of the user at the marketplaces site.
     */
    var $BuyerUserName;

    /**
     * Date when the user registered with the marketplace.
     */
    var $BuyerRegistrationDate;

    function BuyerDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'BuyerId' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BuyerUserName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BuyerRegistrationDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getBuyerId()
    {
        return $this->BuyerId;
    }
    function setBuyerId($BuyerId, $charset = 'iso-8859-1')
    {
        $this->BuyerId = $BuyerId;
        $this->_elements['BuyerId']['charset'] = $charset;
    }
    function getBuyerUserName()
    {
        return $this->BuyerUserName;
    }
    function setBuyerUserName($BuyerUserName, $charset = 'iso-8859-1')
    {
        $this->BuyerUserName = $BuyerUserName;
        $this->_elements['BuyerUserName']['charset'] = $charset;
    }
    function getBuyerRegistrationDate()
    {
        return $this->BuyerRegistrationDate;
    }
    function setBuyerRegistrationDate($BuyerRegistrationDate, $charset = 'iso-8859-1')
    {
        $this->BuyerRegistrationDate = $BuyerRegistrationDate;
        $this->_elements['BuyerRegistrationDate']['charset'] = $charset;
    }
}
