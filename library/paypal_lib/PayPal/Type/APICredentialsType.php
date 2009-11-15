<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * APICredentialsType
 * 
 * APICredentialsType
 *
 * @package PayPal
 */
class APICredentialsType extends XSDSimpleType
{
    /**
     * Merchant ’s PayPal API username Character length and limitations: 128
     * alphanumeric characters
     */
    var $Username;

    /**
     * Merchant ’s PayPal API password Character length and limitations: 40
     * alphanumeric characters
     */
    var $Password;

    /**
     * Merchant ’s PayPal API signature, if one exists.
     */
    var $Signature;

    /**
     * Merchant ’s PayPal API certificate in PEM format, if one exists
     */
    var $Certificate;

    /**
     * Merchant ’s PayPal API authentication mechanism.
     */
    var $Type;

    function APICredentialsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Username' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Password' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Signature' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Certificate' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Type' => 
              array (
                'required' => true,
                'type' => 'APIAuthenticationType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getUsername()
    {
        return $this->Username;
    }
    function setUsername($Username, $charset = 'iso-8859-1')
    {
        $this->Username = $Username;
        $this->_elements['Username']['charset'] = $charset;
    }
    function getPassword()
    {
        return $this->Password;
    }
    function setPassword($Password, $charset = 'iso-8859-1')
    {
        $this->Password = $Password;
        $this->_elements['Password']['charset'] = $charset;
    }
    function getSignature()
    {
        return $this->Signature;
    }
    function setSignature($Signature, $charset = 'iso-8859-1')
    {
        $this->Signature = $Signature;
        $this->_elements['Signature']['charset'] = $charset;
    }
    function getCertificate()
    {
        return $this->Certificate;
    }
    function setCertificate($Certificate, $charset = 'iso-8859-1')
    {
        $this->Certificate = $Certificate;
        $this->_elements['Certificate']['charset'] = $charset;
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
}
