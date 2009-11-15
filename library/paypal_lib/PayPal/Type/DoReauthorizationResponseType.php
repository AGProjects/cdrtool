<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * DoReauthorizationResponseType
 *
 * @package PayPal
 */
class DoReauthorizationResponseType extends AbstractResponseType
{
    /**
     * A new authorization identification number.
     */
    var $AuthorizationID;

    var $AuthorizationInfo;

    function DoReauthorizationResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'AuthorizationID' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'AuthorizationInfo' => 
              array (
                'required' => false,
                'type' => 'AuthorizationInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getAuthorizationID()
    {
        return $this->AuthorizationID;
    }
    function setAuthorizationID($AuthorizationID, $charset = 'iso-8859-1')
    {
        $this->AuthorizationID = $AuthorizationID;
        $this->_elements['AuthorizationID']['charset'] = $charset;
    }
    function getAuthorizationInfo()
    {
        return $this->AuthorizationInfo;
    }
    function setAuthorizationInfo($AuthorizationInfo, $charset = 'iso-8859-1')
    {
        $this->AuthorizationInfo = $AuthorizationInfo;
        $this->_elements['AuthorizationInfo']['charset'] = $charset;
    }
}
