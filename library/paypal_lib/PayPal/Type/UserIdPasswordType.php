<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * UserIdPasswordType
 *
 * @package PayPal
 */
class UserIdPasswordType extends XSDSimpleType
{
    var $AppId;

    var $DevId;

    var $AuthCert;

    /**
     * The username is the identifier for an account.
     */
    var $Username;

    /**
     * Password contains the current password associated with the username.
     */
    var $Password;

    /**
     * Signature for Three Token authentication.
     */
    var $Signature;

    /**
     * This field identifies an account (e.g., payment) on whose behalf the operation
     * is being performed. For instance one account holder may delegate the abililty to
     * perform certain operations to another account holder. This delegation is done
     * through a separate mechanism. If the base username has not been authorized by
     * the subject the request will be rejected.
     */
    var $Subject;

    function UserIdPasswordType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'AppId' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'DevId' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AuthCert' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
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
              'Subject' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getAppId()
    {
        return $this->AppId;
    }
    function setAppId($AppId, $charset = 'iso-8859-1')
    {
        $this->AppId = $AppId;
        $this->_elements['AppId']['charset'] = $charset;
    }
    function getDevId()
    {
        return $this->DevId;
    }
    function setDevId($DevId, $charset = 'iso-8859-1')
    {
        $this->DevId = $DevId;
        $this->_elements['DevId']['charset'] = $charset;
    }
    function getAuthCert()
    {
        return $this->AuthCert;
    }
    function setAuthCert($AuthCert, $charset = 'iso-8859-1')
    {
        $this->AuthCert = $AuthCert;
        $this->_elements['AuthCert']['charset'] = $charset;
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
    function getSubject()
    {
        return $this->Subject;
    }
    function setSubject($Subject, $charset = 'iso-8859-1')
    {
        $this->Subject = $Subject;
        $this->_elements['Subject']['charset'] = $charset;
    }
}
