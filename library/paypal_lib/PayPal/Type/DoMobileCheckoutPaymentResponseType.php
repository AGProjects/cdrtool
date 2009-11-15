<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * DoMobileCheckoutPaymentResponseType
 *
 * @package PayPal
 */
class DoMobileCheckoutPaymentResponseType extends AbstractResponseType
{
    var $DoMobileCheckoutPaymentResponseDetails;

    function DoMobileCheckoutPaymentResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'DoMobileCheckoutPaymentResponseDetails' => 
              array (
                'required' => true,
                'type' => 'DoMobileCheckoutPaymentResponseDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getDoMobileCheckoutPaymentResponseDetails()
    {
        return $this->DoMobileCheckoutPaymentResponseDetails;
    }
    function setDoMobileCheckoutPaymentResponseDetails($DoMobileCheckoutPaymentResponseDetails, $charset = 'iso-8859-1')
    {
        $this->DoMobileCheckoutPaymentResponseDetails = $DoMobileCheckoutPaymentResponseDetails;
        $this->_elements['DoMobileCheckoutPaymentResponseDetails']['charset'] = $charset;
    }
}
