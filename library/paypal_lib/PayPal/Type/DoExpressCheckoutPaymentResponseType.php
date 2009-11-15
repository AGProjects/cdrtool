<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * DoExpressCheckoutPaymentResponseType
 *
 * @package PayPal
 */
class DoExpressCheckoutPaymentResponseType extends AbstractResponseType
{
    var $DoExpressCheckoutPaymentResponseDetails;

    var $FMFDetails;

    function DoExpressCheckoutPaymentResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'DoExpressCheckoutPaymentResponseDetails' => 
              array (
                'required' => true,
                'type' => 'DoExpressCheckoutPaymentResponseDetailsType',
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

    function getDoExpressCheckoutPaymentResponseDetails()
    {
        return $this->DoExpressCheckoutPaymentResponseDetails;
    }
    function setDoExpressCheckoutPaymentResponseDetails($DoExpressCheckoutPaymentResponseDetails, $charset = 'iso-8859-1')
    {
        $this->DoExpressCheckoutPaymentResponseDetails = $DoExpressCheckoutPaymentResponseDetails;
        $this->_elements['DoExpressCheckoutPaymentResponseDetails']['charset'] = $charset;
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
