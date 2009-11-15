<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * GetAccessPermissionDetailsResponseType
 *
 * @package PayPal
 */
class GetAccessPermissionDetailsResponseType extends AbstractResponseType
{
    var $GetAccessPermissionDetailsResponseDetails;

    function GetAccessPermissionDetailsResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'GetAccessPermissionDetailsResponseDetails' => 
              array (
                'required' => true,
                'type' => 'GetAccessPermissionDetailsResponseDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getGetAccessPermissionDetailsResponseDetails()
    {
        return $this->GetAccessPermissionDetailsResponseDetails;
    }
    function setGetAccessPermissionDetailsResponseDetails($GetAccessPermissionDetailsResponseDetails, $charset = 'iso-8859-1')
    {
        $this->GetAccessPermissionDetailsResponseDetails = $GetAccessPermissionDetailsResponseDetails;
        $this->_elements['GetAccessPermissionDetailsResponseDetails']['charset'] = $charset;
    }
}
