<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * SetExpressCheckoutResponseType
 *
 * @package PayPal
 */
class SetExpressCheckoutResponseType extends AbstractResponseType
{
    /**
     * A timestamped token by which you identify to PayPal that you are processing this
     * payment with Express Checkout. The token expires after three hours. If you set
     * Token in the SetExpressCheckoutRequest, the value of Token in the response is
     * identical to the value in the request.
     */
    var $Token;

    function SetExpressCheckoutResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'Token' => 
              array (
                'required' => true,
                'type' => 'ExpressCheckoutTokenType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getToken()
    {
        return $this->Token;
    }
    function setToken($Token, $charset = 'iso-8859-1')
    {
        $this->Token = $Token;
        $this->_elements['Token']['charset'] = $charset;
    }
}
