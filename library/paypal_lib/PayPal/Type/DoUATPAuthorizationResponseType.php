<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/DoAuthorizationResponseType.php';

/**
 * DoUATPAuthorizationResponseType
 *
 * @package PayPal
 */
class DoUATPAuthorizationResponseType extends DoAuthorizationResponseType
{
    var $UATPDetails;

    /**
     * Auth Authorization Code.
     */
    var $AuthorizationCode;

    /**
     * Invoice ID. A pass through.
     */
    var $InvoiceID;

    function DoUATPAuthorizationResponseType()
    {
        parent::DoAuthorizationResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'UATPDetails' => 
              array (
                'required' => true,
                'type' => 'UATPDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AuthorizationCode' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'InvoiceID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getUATPDetails()
    {
        return $this->UATPDetails;
    }
    function setUATPDetails($UATPDetails, $charset = 'iso-8859-1')
    {
        $this->UATPDetails = $UATPDetails;
        $this->_elements['UATPDetails']['charset'] = $charset;
    }
    function getAuthorizationCode()
    {
        return $this->AuthorizationCode;
    }
    function setAuthorizationCode($AuthorizationCode, $charset = 'iso-8859-1')
    {
        $this->AuthorizationCode = $AuthorizationCode;
        $this->_elements['AuthorizationCode']['charset'] = $charset;
    }
    function getInvoiceID()
    {
        return $this->InvoiceID;
    }
    function setInvoiceID($InvoiceID, $charset = 'iso-8859-1')
    {
        $this->InvoiceID = $InvoiceID;
        $this->_elements['InvoiceID']['charset'] = $charset;
    }
}
