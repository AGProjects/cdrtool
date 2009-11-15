<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * CreateRecurringPaymentsProfileResponseDetailsType
 *
 * @package PayPal
 */
class CreateRecurringPaymentsProfileResponseDetailsType extends XSDSimpleType
{
    /**
     * Recurring Billing Profile ID
     */
    var $ProfileID;

    /**
     * Recurring Billing Profile Status
     */
    var $ProfileStatus;

    /**
     * Transaction id from DCC initial payment
     */
    var $TransactionID;

    /**
     * Response from DCC initial payment
     */
    var $DCCProcessorResponse;

    /**
     * Return code if DCC initial payment fails
     */
    var $DCCReturnCode;

    function CreateRecurringPaymentsProfileResponseDetailsType()
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
                'required' => false,
                'type' => 'RecurringPaymentsProfileStatusType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TransactionID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'DCCProcessorResponse' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'DCCReturnCode' => 
              array (
                'required' => false,
                'type' => 'string',
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
    function getTransactionID()
    {
        return $this->TransactionID;
    }
    function setTransactionID($TransactionID, $charset = 'iso-8859-1')
    {
        $this->TransactionID = $TransactionID;
        $this->_elements['TransactionID']['charset'] = $charset;
    }
    function getDCCProcessorResponse()
    {
        return $this->DCCProcessorResponse;
    }
    function setDCCProcessorResponse($DCCProcessorResponse, $charset = 'iso-8859-1')
    {
        $this->DCCProcessorResponse = $DCCProcessorResponse;
        $this->_elements['DCCProcessorResponse']['charset'] = $charset;
    }
    function getDCCReturnCode()
    {
        return $this->DCCReturnCode;
    }
    function setDCCReturnCode($DCCReturnCode, $charset = 'iso-8859-1')
    {
        $this->DCCReturnCode = $DCCReturnCode;
        $this->_elements['DCCReturnCode']['charset'] = $charset;
    }
}
