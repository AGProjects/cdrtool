<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * BMUpdateButtonResponseType
 *
 * @package PayPal
 */
class BMUpdateButtonResponseType extends AbstractResponseType
{
    var $Website;

    var $Email;

    var $Mobile;

    var $HostedButtonID;

    function BMUpdateButtonResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'Website' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Email' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Mobile' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'HostedButtonID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
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
    function getEmail()
    {
        return $this->Email;
    }
    function setEmail($Email, $charset = 'iso-8859-1')
    {
        $this->Email = $Email;
        $this->_elements['Email']['charset'] = $charset;
    }
    function getMobile()
    {
        return $this->Mobile;
    }
    function setMobile($Mobile, $charset = 'iso-8859-1')
    {
        $this->Mobile = $Mobile;
        $this->_elements['Mobile']['charset'] = $charset;
    }
    function getHostedButtonID()
    {
        return $this->HostedButtonID;
    }
    function setHostedButtonID($HostedButtonID, $charset = 'iso-8859-1')
    {
        $this->HostedButtonID = $HostedButtonID;
        $this->_elements['HostedButtonID']['charset'] = $charset;
    }
}
