<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * UpdateAccessPermissionsRequestType
 *
 * @package PayPal
 */
class UpdateAccessPermissionsRequestType extends AbstractRequestType
{
    /**
     * Unique PayPal customer account number, the value of which was returned by
     * GetAuthDetails Response.
     */
    var $PayerID;

    function UpdateAccessPermissionsRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'PayerID' => 
              array (
                'required' => true,
                'type' => 'UserIDType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
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
