<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * UpdateRecurringPaymentsProfileRequestDetailsType
 *
 * @package PayPal
 */
class UpdateRecurringPaymentsProfileRequestDetailsType extends XSDSimpleType
{
    var $ProfileID;

    var $Note;

    var $Description;

    var $SubscriberName;

    var $SubscriberShippingAddress;

    var $ProfileReference;

    var $AdditionalBillingCycles;

    var $Amount;

    var $ShippingAmount;

    var $TaxAmount;

    var $OutstandingBalance;

    var $AutoBillOutstandingAmount;

    var $MaxFailedPayments;

    /**
     * Information about the credit card to be charged (required if Direct Payment)
     */
    var $CreditCard;

    /**
     * When does this Profile begin billing?
     */
    var $BillingStartDate;

    /**
     * Trial period of this schedule
     */
    var $TrialPeriod;

    var $PaymentPeriod;

    function UpdateRecurringPaymentsProfileRequestDetailsType()
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
              'Note' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Description' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SubscriberName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SubscriberShippingAddress' => 
              array (
                'required' => false,
                'type' => 'AddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ProfileReference' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AdditionalBillingCycles' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Amount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TaxAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OutstandingBalance' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AutoBillOutstandingAmount' => 
              array (
                'required' => false,
                'type' => 'AutoBillType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MaxFailedPayments' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CreditCard' => 
              array (
                'required' => false,
                'type' => 'CreditCardDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingStartDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TrialPeriod' => 
              array (
                'required' => false,
                'type' => 'BillingPeriodDetailsType_Update',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentPeriod' => 
              array (
                'required' => false,
                'type' => 'BillingPeriodDetailsType_Update',
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
    function getNote()
    {
        return $this->Note;
    }
    function setNote($Note, $charset = 'iso-8859-1')
    {
        $this->Note = $Note;
        $this->_elements['Note']['charset'] = $charset;
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
    function getSubscriberName()
    {
        return $this->SubscriberName;
    }
    function setSubscriberName($SubscriberName, $charset = 'iso-8859-1')
    {
        $this->SubscriberName = $SubscriberName;
        $this->_elements['SubscriberName']['charset'] = $charset;
    }
    function getSubscriberShippingAddress()
    {
        return $this->SubscriberShippingAddress;
    }
    function setSubscriberShippingAddress($SubscriberShippingAddress, $charset = 'iso-8859-1')
    {
        $this->SubscriberShippingAddress = $SubscriberShippingAddress;
        $this->_elements['SubscriberShippingAddress']['charset'] = $charset;
    }
    function getProfileReference()
    {
        return $this->ProfileReference;
    }
    function setProfileReference($ProfileReference, $charset = 'iso-8859-1')
    {
        $this->ProfileReference = $ProfileReference;
        $this->_elements['ProfileReference']['charset'] = $charset;
    }
    function getAdditionalBillingCycles()
    {
        return $this->AdditionalBillingCycles;
    }
    function setAdditionalBillingCycles($AdditionalBillingCycles, $charset = 'iso-8859-1')
    {
        $this->AdditionalBillingCycles = $AdditionalBillingCycles;
        $this->_elements['AdditionalBillingCycles']['charset'] = $charset;
    }
    function getAmount()
    {
        return $this->Amount;
    }
    function setAmount($Amount, $charset = 'iso-8859-1')
    {
        $this->Amount = $Amount;
        $this->_elements['Amount']['charset'] = $charset;
    }
    function getShippingAmount()
    {
        return $this->ShippingAmount;
    }
    function setShippingAmount($ShippingAmount, $charset = 'iso-8859-1')
    {
        $this->ShippingAmount = $ShippingAmount;
        $this->_elements['ShippingAmount']['charset'] = $charset;
    }
    function getTaxAmount()
    {
        return $this->TaxAmount;
    }
    function setTaxAmount($TaxAmount, $charset = 'iso-8859-1')
    {
        $this->TaxAmount = $TaxAmount;
        $this->_elements['TaxAmount']['charset'] = $charset;
    }
    function getOutstandingBalance()
    {
        return $this->OutstandingBalance;
    }
    function setOutstandingBalance($OutstandingBalance, $charset = 'iso-8859-1')
    {
        $this->OutstandingBalance = $OutstandingBalance;
        $this->_elements['OutstandingBalance']['charset'] = $charset;
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
    function getCreditCard()
    {
        return $this->CreditCard;
    }
    function setCreditCard($CreditCard, $charset = 'iso-8859-1')
    {
        $this->CreditCard = $CreditCard;
        $this->_elements['CreditCard']['charset'] = $charset;
    }
    function getBillingStartDate()
    {
        return $this->BillingStartDate;
    }
    function setBillingStartDate($BillingStartDate, $charset = 'iso-8859-1')
    {
        $this->BillingStartDate = $BillingStartDate;
        $this->_elements['BillingStartDate']['charset'] = $charset;
    }
    function getTrialPeriod()
    {
        return $this->TrialPeriod;
    }
    function setTrialPeriod($TrialPeriod, $charset = 'iso-8859-1')
    {
        $this->TrialPeriod = $TrialPeriod;
        $this->_elements['TrialPeriod']['charset'] = $charset;
    }
    function getPaymentPeriod()
    {
        return $this->PaymentPeriod;
    }
    function setPaymentPeriod($PaymentPeriod, $charset = 'iso-8859-1')
    {
        $this->PaymentPeriod = $PaymentPeriod;
        $this->_elements['PaymentPeriod']['charset'] = $charset;
    }
}
