<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * CreateMobilePaymentRequestType
 *
 * @package PayPal
 */
class CreateMobilePaymentRequestType extends AbstractRequestType
{
    var $CreateMobilePaymentRequestDetails;

    function CreateMobilePaymentRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'CreateMobilePaymentRequestDetails' => 
              array (
                'required' => true,
                'type' => 'CreateMobilePaymentRequestDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getCreateMobilePaymentRequestDetails()
    {
        return $this->CreateMobilePaymentRequestDetails;
    }
    function setCreateMobilePaymentRequestDetails($CreateMobilePaymentRequestDetails, $charset = 'iso-8859-1')
    {
        $this->CreateMobilePaymentRequestDetails = $CreateMobilePaymentRequestDetails;
        $this->_elements['CreateMobilePaymentRequestDetails']['charset'] = $charset;
    }
}
