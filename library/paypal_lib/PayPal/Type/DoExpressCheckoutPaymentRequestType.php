<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * DoExpressCheckoutPaymentRequestType
 *
 * @package PayPal
 */
class DoExpressCheckoutPaymentRequestType extends AbstractRequestType
{
    var $DoExpressCheckoutPaymentRequestDetails;

    /**
     * This flag indicates that the response should include FMFDetails
     */
    var $ReturnFMFDetails;

    function DoExpressCheckoutPaymentRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'DoExpressCheckoutPaymentRequestDetails' => 
              array (
                'required' => true,
                'type' => 'DoExpressCheckoutPaymentRequestDetailsType',
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

    function getDoExpressCheckoutPaymentRequestDetails()
    {
        return $this->DoExpressCheckoutPaymentRequestDetails;
    }
    function setDoExpressCheckoutPaymentRequestDetails($DoExpressCheckoutPaymentRequestDetails, $charset = 'iso-8859-1')
    {
        $this->DoExpressCheckoutPaymentRequestDetails = $DoExpressCheckoutPaymentRequestDetails;
        $this->_elements['DoExpressCheckoutPaymentRequestDetails']['charset'] = $charset;
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
