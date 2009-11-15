<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * CustomSecurityHeaderType
 * 
 * Custom Securiy Header.
 *
 * @package PayPal
 */
class CustomSecurityHeaderType extends XSDSimpleType
{
    var $eBayAuthToken;

    var $HardExpirationWarning;

    var $Credentials;

    function CustomSecurityHeaderType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'eBayAuthToken' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'HardExpirationWarning' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Credentials' => 
              array (
                'required' => false,
                'type' => 'UserIdPasswordType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function geteBayAuthToken()
    {
        return $this->eBayAuthToken;
    }
    function seteBayAuthToken($eBayAuthToken, $charset = 'iso-8859-1')
    {
        $this->eBayAuthToken = $eBayAuthToken;
        $this->_elements['eBayAuthToken']['charset'] = $charset;
    }
    function getHardExpirationWarning()
    {
        return $this->HardExpirationWarning;
    }
    function setHardExpirationWarning($HardExpirationWarning, $charset = 'iso-8859-1')
    {
        $this->HardExpirationWarning = $HardExpirationWarning;
        $this->_elements['HardExpirationWarning']['charset'] = $charset;
    }
    function getCredentials()
    {
        return $this->Credentials;
    }
    function setCredentials($Credentials, $charset = 'iso-8859-1')
    {
        $this->Credentials = $Credentials;
        $this->_elements['Credentials']['charset'] = $charset;
    }
}
