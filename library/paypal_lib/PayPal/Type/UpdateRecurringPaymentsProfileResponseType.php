<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * UpdateRecurringPaymentsProfileResponseType
 *
 * @package PayPal
 */
class UpdateRecurringPaymentsProfileResponseType extends AbstractResponseType
{
    var $UpdateRecurringPaymentsProfileResponseDetails;

    function UpdateRecurringPaymentsProfileResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'UpdateRecurringPaymentsProfileResponseDetails' => 
              array (
                'required' => true,
                'type' => 'UpdateRecurringPaymentsProfileResponseDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getUpdateRecurringPaymentsProfileResponseDetails()
    {
        return $this->UpdateRecurringPaymentsProfileResponseDetails;
    }
    function setUpdateRecurringPaymentsProfileResponseDetails($UpdateRecurringPaymentsProfileResponseDetails, $charset = 'iso-8859-1')
    {
        $this->UpdateRecurringPaymentsProfileResponseDetails = $UpdateRecurringPaymentsProfileResponseDetails;
        $this->_elements['UpdateRecurringPaymentsProfileResponseDetails']['charset'] = $charset;
    }
}
