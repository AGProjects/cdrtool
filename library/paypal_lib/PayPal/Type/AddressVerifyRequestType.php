<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * AddressVerifyRequestType
 *
 * @package PayPal
 */
class AddressVerifyRequestType extends AbstractRequestType
{
    /**
     * Email address of buyer to be verified.
     */
    var $Email;

    /**
     * First line of buyer ’s billing or shipping street address to be verified.
     */
    var $Street;

    /**
     * Postal code to be verified.
     */
    var $Zip;

    function AddressVerifyRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'Email' => 
              array (
                'required' => true,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Street' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Zip' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
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
    function getStreet()
    {
        return $this->Street;
    }
    function setStreet($Street, $charset = 'iso-8859-1')
    {
        $this->Street = $Street;
        $this->_elements['Street']['charset'] = $charset;
    }
    function getZip()
    {
        return $this->Zip;
    }
    function setZip($Zip, $charset = 'iso-8859-1')
    {
        $this->Zip = $Zip;
        $this->_elements['Zip']['charset'] = $charset;
    }
}
