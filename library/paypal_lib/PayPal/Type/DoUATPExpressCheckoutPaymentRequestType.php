<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/DoExpressCheckoutPaymentRequestType.php';

/**
 * DoUATPExpressCheckoutPaymentRequestType
 *
 * @package PayPal
 */
class DoUATPExpressCheckoutPaymentRequestType extends DoExpressCheckoutPaymentRequestType
{
    function DoUATPExpressCheckoutPaymentRequestType()
    {
        parent::DoExpressCheckoutPaymentRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
    }

}
