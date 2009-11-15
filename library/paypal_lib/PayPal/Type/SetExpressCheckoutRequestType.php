<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * SetExpressCheckoutRequestType
 *
 * @package PayPal
 */
class SetExpressCheckoutRequestType extends AbstractRequestType
{
    var $SetExpressCheckoutRequestDetails;

    function SetExpressCheckoutRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'SetExpressCheckoutRequestDetails' => 
              array (
                'required' => true,
                'type' => 'SetExpressCheckoutRequestDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getSetExpressCheckoutRequestDetails()
    {
        return $this->SetExpressCheckoutRequestDetails;
    }
    function setSetExpressCheckoutRequestDetails($SetExpressCheckoutRequestDetails, $charset = 'iso-8859-1')
    {
        $this->SetExpressCheckoutRequestDetails = $SetExpressCheckoutRequestDetails;
        $this->_elements['SetExpressCheckoutRequestDetails']['charset'] = $charset;
    }
}
