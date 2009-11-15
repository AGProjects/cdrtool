<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * GetRecurringPaymentsProfileDetailsResponseDetailsType
 *
 * @package PayPal
 */
class GetRecurringPaymentsProfileDetailsResponseDetailsType extends XSDSimpleType
{
    /**
     * Recurring Billing Profile ID
     */
    var $ProfileID;

    var $ProfileStatus;

    var $Description;

    var $AutoBillOutstandingAmount;

    var $MaxFailedPayments;

    var $RecurringPaymentsProfileDetails;

    var $CurrentRecurringPaymentsPeriod;

    var $RecurringPaymentsSummary;

    var $CreditCard;

    var $TrialRecurringPaymentsPeriod;

    var $RegularRecurringPaymentsPeriod;

    var $TrialAmountPaid;

    var $RegularAmountPaid;

    var $AggregateAmount;

    var $AggregateOptionalAmount;

    var $FinalPaymentDueDate;

    function GetRecurringPaymentsProfileDetailsResponseDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ProfileID' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ProfileStatus' => 
              array (
                'required' => true,
                'type' => 'RecurringPaymentsProfileStatusType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Description' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AutoBillOutstandingAmount' => 
              array (
                'required' => true,
                'type' => 'AutoBillType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MaxFailedPayments' => 
              array (
                'required' => true,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RecurringPaymentsProfileDetails' => 
              array (
                'required' => true,
                'type' => 'RecurringPaymentsProfileDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CurrentRecurringPaymentsPeriod' => 
              array (
                'required' => false,
                'type' => 'BillingPeriodDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RecurringPaymentsSummary' => 
              array (
                'required' => true,
                'type' => 'RecurringPaymentsSummaryType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CreditCard' => 
              array (
                'required' => false,
                'type' => 'CreditCardDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TrialRecurringPaymentsPeriod' => 
              array (
                'required' => false,
                'type' => 'BillingPeriodDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RegularRecurringPaymentsPeriod' => 
              array (
                'required' => false,
                'type' => 'BillingPeriodDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TrialAmountPaid' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RegularAmountPaid' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AggregateAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AggregateOptionalAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FinalPaymentDueDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getProfileID()
    {
        return $this->ProfileID;
    }
    function setProfileID($ProfileID, $charset = 'iso-8859-1')
    {
        $this->ProfileID = $ProfileID;
        $this->_elements['ProfileID']['charset'] = $charset;
    }
    function getProfileStatus()
    {
        return $this->ProfileStatus;
    }
    function setProfileStatus($ProfileStatus, $charset = 'iso-8859-1')
    {
        $this->ProfileStatus = $ProfileStatus;
        $this->_elements['ProfileStatus']['charset'] = $charset;
    }
    function getDescription()
    {
        return $this->Description;
    }
    function setDescription($Description, $charset = 'iso-8859-1')
    {
        $this->Description = $Description;
        $this->_elements['Description']['charset'] = $charset;
    }
    function getAutoBillOutstandingAmount()
    {
        return $this->AutoBillOutstandingAmount;
    }
    function setAutoBillOutstandingAmount($AutoBillOutstandingAmount, $charset = 'iso-8859-1')
    {
        $this->AutoBillOutstandingAmount = $AutoBillOutstandingAmount;
        $this->_elements['AutoBillOutstandingAmount']['charset'] = $charset;
    }
    function getMaxFailedPayments()
    {
        return $this->MaxFailedPayments;
    }
    function setMaxFailedPayments($MaxFailedPayments, $charset = 'iso-8859-1')
    {
        $this->MaxFailedPayments = $MaxFailedPayments;
        $this->_elements['MaxFailedPayments']['charset'] = $charset;
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
    function getCurrentRecurringPaymentsPeriod()
    {
        return $this->CurrentRecurringPaymentsPeriod;
    }
    function setCurrentRecurringPaymentsPeriod($CurrentRecurringPaymentsPeriod, $charset = 'iso-8859-1')
    {
        $this->CurrentRecurringPaymentsPeriod = $CurrentRecurringPaymentsPeriod;
        $this->_elements['CurrentRecurringPaymentsPeriod']['charset'] = $charset;
    }
    function getRecurringPaymentsSummary()
    {
        return $this->RecurringPaymentsSummary;
    }
    function setRecurringPaymentsSummary($RecurringPaymentsSummary, $charset = 'iso-8859-1')
    {
        $this->RecurringPaymentsSummary = $RecurringPaymentsSummary;
        $this->_elements['RecurringPaymentsSummary']['charset'] = $charset;
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
    function getTrialRecurringPaymentsPeriod()
    {
        return $this->TrialRecurringPaymentsPeriod;
    }
    function setTrialRecurringPaymentsPeriod($TrialRecurringPaymentsPeriod, $charset = 'iso-8859-1')
    {
        $this->TrialRecurringPaymentsPeriod = $TrialRecurringPaymentsPeriod;
        $this->_elements['TrialRecurringPaymentsPeriod']['charset'] = $charset;
    }
    function getRegularRecurringPaymentsPeriod()
    {
        return $this->RegularRecurringPaymentsPeriod;
    }
    function setRegularRecurringPaymentsPeriod($RegularRecurringPaymentsPeriod, $charset = 'iso-8859-1')
    {
        $this->RegularRecurringPaymentsPeriod = $RegularRecurringPaymentsPeriod;
        $this->_elements['RegularRecurringPaymentsPeriod']['charset'] = $charset;
    }
    function getTrialAmountPaid()
    {
        return $this->TrialAmountPaid;
    }
    function setTrialAmountPaid($TrialAmountPaid, $charset = 'iso-8859-1')
    {
        $this->TrialAmountPaid = $TrialAmountPaid;
        $this->_elements['TrialAmountPaid']['charset'] = $charset;
    }
    function getRegularAmountPaid()
    {
        return $this->RegularAmountPaid;
    }
    function setRegularAmountPaid($RegularAmountPaid, $charset = 'iso-8859-1')
    {
        $this->RegularAmountPaid = $RegularAmountPaid;
        $this->_elements['RegularAmountPaid']['charset'] = $charset;
    }
    function getAggregateAmount()
    {
        return $this->AggregateAmount;
    }
    function setAggregateAmount($AggregateAmount, $charset = 'iso-8859-1')
    {
        $this->AggregateAmount = $AggregateAmount;
        $this->_elements['AggregateAmount']['charset'] = $charset;
    }
    function getAggregateOptionalAmount()
    {
        return $this->AggregateOptionalAmount;
    }
    function setAggregateOptionalAmount($AggregateOptionalAmount, $charset = 'iso-8859-1')
    {
        $this->AggregateOptionalAmount = $AggregateOptionalAmount;
        $this->_elements['AggregateOptionalAmount']['charset'] = $charset;
    }
    function getFinalPaymentDueDate()
    {
        return $this->FinalPaymentDueDate;
    }
    function setFinalPaymentDueDate($FinalPaymentDueDate, $charset = 'iso-8859-1')
    {
        $this->FinalPaymentDueDate = $FinalPaymentDueDate;
        $this->_elements['FinalPaymentDueDate']['charset'] = $charset;
    }
}
