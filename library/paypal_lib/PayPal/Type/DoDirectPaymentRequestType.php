<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * DoDirectPaymentRequestType
 *
 * @package PayPal
 */
class DoDirectPaymentRequestType extends AbstractRequestType
{
    var $DoDirectPaymentRequestDetails;

    /**
     * This flag indicates that the response should include FMFDetails
     */
    var $ReturnFMFDetails;

    function DoDirectPaymentRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'DoDirectPaymentRequestDetails' => 
              array (
                'required' => true,
                'type' => 'DoDirectPaymentRequestDetailsType',
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

    function getDoDirectPaymentRequestDetails()
    {
        return $this->DoDirectPaymentRequestDetails;
    }
    function setDoDirectPaymentRequestDetails($DoDirectPaymentRequestDetails, $charset = 'iso-8859-1')
    {
        $this->DoDirectPaymentRequestDetails = $DoDirectPaymentRequestDetails;
        $this->_elements['DoDirectPaymentRequestDetails']['charset'] = $charset;
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
