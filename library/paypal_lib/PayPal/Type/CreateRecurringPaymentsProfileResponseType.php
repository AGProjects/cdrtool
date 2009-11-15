<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * CreateRecurringPaymentsProfileResponseType
 *
 * @package PayPal
 */
class CreateRecurringPaymentsProfileResponseType extends AbstractResponseType
{
    var $CreateRecurringPaymentsProfileResponseDetails;

    function CreateRecurringPaymentsProfileResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'CreateRecurringPaymentsProfileResponseDetails' => 
              array (
                'required' => true,
                'type' => 'CreateRecurringPaymentsProfileResponseDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getCreateRecurringPaymentsProfileResponseDetails()
    {
        return $this->CreateRecurringPaymentsProfileResponseDetails;
    }
    function setCreateRecurringPaymentsProfileResponseDetails($CreateRecurringPaymentsProfileResponseDetails, $charset = 'iso-8859-1')
    {
        $this->CreateRecurringPaymentsProfileResponseDetails = $CreateRecurringPaymentsProfileResponseDetails;
        $this->_elements['CreateRecurringPaymentsProfileResponseDetails']['charset'] = $charset;
    }
}
