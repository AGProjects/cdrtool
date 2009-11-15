<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * GetAuthDetailsResponseDetailsType
 *
 * @package PayPal
 */
class GetAuthDetailsResponseDetailsType extends XSDSimpleType
{
    /**
     * The first name of the User.
     */
    var $FirstName;

    /**
     * The Last name of the user.
     */
    var $LastName;

    /**
     * The email address of the user.
     */
    var $Email;

    /**
     * Encrypted PayPal customer account identification number.
     */
    var $PayerID;

    function GetAuthDetailsResponseDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'FirstName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LastName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Email' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerID' => 
              array (
                'required' => true,
                'type' => 'UserIDType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getFirstName()
    {
        return $this->FirstName;
    }
    function setFirstName($FirstName, $charset = 'iso-8859-1')
    {
        $this->FirstName = $FirstName;
        $this->_elements['FirstName']['charset'] = $charset;
    }
    function getLastName()
    {
        return $this->LastName;
    }
    function setLastName($LastName, $charset = 'iso-8859-1')
    {
        $this->LastName = $LastName;
        $this->_elements['LastName']['charset'] = $charset;
    }
    function getEmail()
    {
        return $this->Email;
    }
    function setEmail($Email, $charset = 'iso-8859-1')
    {
        $this->Email = $Email;
        $this->_elements['Email']['charset'] = $charset;
    }
    function getPayerID()
    {
        return $this->PayerID;
    }
    function setPayerID($PayerID, $charset = 'iso-8859-1')
    {
        $this->PayerID = $PayerID;
        $this->_elements['PayerID']['charset'] = $charset;
    }
}
