<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * DoReferenceTransactionRequestType
 *
 * @package PayPal
 */
class DoReferenceTransactionRequestType extends AbstractRequestType
{
    var $DoReferenceTransactionRequestDetails;

    /**
     * This flag indicates that the response should include FMFDetails
     */
    var $ReturnFMFDetails;

    function DoReferenceTransactionRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'DoReferenceTransactionRequestDetails' => 
              array (
                'required' => true,
                'type' => 'DoReferenceTransactionRequestDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReturnFMFDetails' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getDoReferenceTransactionRequestDetails()
    {
        return $this->DoReferenceTransactionRequestDetails;
    }
    function setDoReferenceTransactionRequestDetails($DoReferenceTransactionRequestDetails, $charset = 'iso-8859-1')
    {
        $this->DoReferenceTransactionRequestDetails = $DoReferenceTransactionRequestDetails;
        $this->_elements['DoReferenceTransactionRequestDetails']['charset'] = $charset;
    }
    function getReturnFMFDetails()
    {
        return $this->ReturnFMFDetails;
    }
    function setReturnFMFDetails($ReturnFMFDetails, $charset = 'iso-8859-1')
    {
        $this->ReturnFMFDetails = $ReturnFMFDetails;
        $this->_elements['ReturnFMFDetails']['charset'] = $charset;
    }
}
