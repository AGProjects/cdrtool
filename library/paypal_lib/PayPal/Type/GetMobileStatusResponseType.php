<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * GetMobileStatusResponseType
 *
 * @package PayPal
 */
class GetMobileStatusResponseType extends AbstractResponseType
{
    /**
     * Indicates whether the phone is activated for mobile payments
     */
    var $IsActivated;

    /**
     * Indicates whether there is a payment pending from the phone
     */
    var $PaymentPending;

    function GetMobileStatusResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'IsActivated' => 
              array (
                'required' => true,
                'type' => 'integer',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'PaymentPending' => 
              array (
                'required' => true,
                'type' => 'integer',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getIsActivated()
    {
        return $this->IsActivated;
    }
    function setIsActivated($IsActivated, $charset = 'iso-8859-1')
    {
        $this->IsActivated = $IsActivated;
        $this->_elements['IsActivated']['charset'] = $charset;
    }
    function getPaymentPending()
    {
        return $this->PaymentPending;
    }
    function setPaymentPending($PaymentPending, $charset = 'iso-8859-1')
    {
        $this->PaymentPending = $PaymentPending;
        $this->_elements['PaymentPending']['charset'] = $charset;
    }
}
