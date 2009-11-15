<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * CreateRecurringPaymentsProfileRequestDetailsType
 *
 * @package PayPal
 */
class CreateRecurringPaymentsProfileRequestDetailsType extends XSDSimpleType
{
    /**
     * Billing Agreement token (required if Express Checkout)
     */
    var $Token;

    /**
     * Information about the credit card to be charged (required if Direct Payment)
     */
    var $CreditCard;

    /**
     * Customer Information for this Recurring Payments
     */
    var $RecurringPaymentsProfileDetails;

    /**
     * Schedule Information for this Recurring Payments
     */
    var $ScheduleDetails;

    function CreateRecurringPaymentsProfileRequestDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Token' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CreditCard' => 
              array (
                'required' => false,
                'type' => 'CreditCardDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RecurringPaymentsProfileDetails' => 
              array (
                'required' => true,
                'type' => 'RecurringPaymentsProfileDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ScheduleDetails' => 
              array (
                'required' => true,
                'type' => 'ScheduleDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
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
    function getCreditCard()
    {
        return $this->CreditCard;
    }
    function setCreditCard($CreditCard, $charset = 'iso-8859-1')
    {
        $this->CreditCard = $CreditCard;
        $this->_elements['CreditCard']['charset'] = $charset;
    }
    function getRecurringPaymentsProfileDetails()
    {
        return $this->RecurringPaymentsProfileDetails;
    }
    function setRecurringPaymentsProfileDetails($RecurringPaymentsProfileDetails, $charset = 'iso-8859-1')
    {
        $this->RecurringPaymentsProfileDetails = $RecurringPaymentsProfileDetails;
        $this->_elements['RecurringPaymentsProfileDetails']['charset'] = $charset;
    }
    function getScheduleDetails()
    {
        return $this->ScheduleDetails;
    }
    function setScheduleDetails($ScheduleDetails, $charset = 'iso-8859-1')
    {
        $this->ScheduleDetails = $ScheduleDetails;
        $this->_elements['ScheduleDetails']['charset'] = $charset;
    }
}
