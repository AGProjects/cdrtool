<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * DoNonReferencedCreditRequestType
 *
 * @package PayPal
 */
class DoNonReferencedCreditRequestType extends AbstractRequestType
{
    var $DoNonReferencedCreditRequestDetails;

    function DoNonReferencedCreditRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'DoNonReferencedCreditRequestDetails' => 
              array (
                'required' => true,
                'type' => 'DoNonReferencedCreditRequestDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getDoNonReferencedCreditRequestDetails()
    {
        return $this->DoNonReferencedCreditRequestDetails;
    }
    function setDoNonReferencedCreditRequestDetails($DoNonReferencedCreditRequestDetails, $charset = 'iso-8859-1')
    {
        $this->DoNonReferencedCreditRequestDetails = $DoNonReferencedCreditRequestDetails;
        $this->_elements['DoNonReferencedCreditRequestDetails']['charset'] = $charset;
    }
}
