<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * ManageRecurringPaymentsProfileStatusResponseType
 *
 * @package PayPal
 */
class ManageRecurringPaymentsProfileStatusResponseType extends AbstractResponseType
{
    var $ManageRecurringPaymentsProfileStatusResponseDetails;

    function ManageRecurringPaymentsProfileStatusResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'ManageRecurringPaymentsProfileStatusResponseDetails' => 
              array (
                'required' => true,
                'type' => 'ManageRecurringPaymentsProfileStatusResponseDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getManageRecurringPaymentsProfileStatusResponseDetails()
    {
        return $this->ManageRecurringPaymentsProfileStatusResponseDetails;
    }
    function setManageRecurringPaymentsProfileStatusResponseDetails($ManageRecurringPaymentsProfileStatusResponseDetails, $charset = 'iso-8859-1')
    {
        $this->ManageRecurringPaymentsProfileStatusResponseDetails = $ManageRecurringPaymentsProfileStatusResponseDetails;
        $this->_elements['ManageRecurringPaymentsProfileStatusResponseDetails']['charset'] = $charset;
    }
}
