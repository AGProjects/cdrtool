<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * DoReferenceTransactionResponseType
 *
 * @package PayPal
 */
class DoReferenceTransactionResponseType extends AbstractResponseType
{
    var $DoReferenceTransactionResponseDetails;

    var $FMFDetails;

    function DoReferenceTransactionResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'DoReferenceTransactionResponseDetails' => 
              array (
                'required' => true,
                'type' => 'DoReferenceTransactionResponseDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FMFDetails' => 
              array (
                'required' => false,
                'type' => 'FMFDetailsType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getDoReferenceTransactionResponseDetails()
    {
        return $this->DoReferenceTransactionResponseDetails;
    }
    function setDoReferenceTransactionResponseDetails($DoReferenceTransactionResponseDetails, $charset = 'iso-8859-1')
    {
        $this->DoReferenceTransactionResponseDetails = $DoReferenceTransactionResponseDetails;
        $this->_elements['DoReferenceTransactionResponseDetails']['charset'] = $charset;
    }
    function getFMFDetails()
    {
        return $this->FMFDetails;
    }
    function setFMFDetails($FMFDetails, $charset = 'iso-8859-1')
    {
        $this->FMFDetails = $FMFDetails;
        $this->_elements['FMFDetails']['charset'] = $charset;
    }
}
