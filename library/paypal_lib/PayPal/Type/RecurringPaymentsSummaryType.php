<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * RecurringPaymentsSummaryType
 *
 * @package PayPal
 */
class RecurringPaymentsSummaryType extends XSDSimpleType
{
    var $NextBillingDate;

    var $NumberCyclesCompleted;

    var $NumberCyclesRemaining;

    var $OutstandingBalance;

    var $FailedPaymentCount;

    var $LastPaymentDate;

    var $LastPaymentAmount;

    function RecurringPaymentsSummaryType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'NextBillingDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'NumberCyclesCompleted' => 
              array (
                'required' => true,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'NumberCyclesRemaining' => 
              array (
                'required' => true,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OutstandingBalance' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FailedPaymentCount' => 
              array (
                'required' => true,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LastPaymentDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LastPaymentAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getNextBillingDate()
    {
        return $this->NextBillingDate;
    }
    function setNextBillingDate($NextBillingDate, $charset = 'iso-8859-1')
    {
        $this->NextBillingDate = $NextBillingDate;
        $this->_elements['NextBillingDate']['charset'] = $charset;
    }
    function getNumberCyclesCompleted()
    {
        return $this->NumberCyclesCompleted;
    }
    function setNumberCyclesCompleted($NumberCyclesCompleted, $charset = 'iso-8859-1')
    {
        $this->NumberCyclesCompleted = $NumberCyclesCompleted;
        $this->_elements['NumberCyclesCompleted']['charset'] = $charset;
    }
    function getNumberCyclesRemaining()
    {
        return $this->NumberCyclesRemaining;
    }
    function setNumberCyclesRemaining($NumberCyclesRemaining, $charset = 'iso-8859-1')
    {
        $this->NumberCyclesRemaining = $NumberCyclesRemaining;
        $this->_elements['NumberCyclesRemaining']['charset'] = $charset;
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
    function getFailedPaymentCount()
    {
        return $this->FailedPaymentCount;
    }
    function setFailedPaymentCount($FailedPaymentCount, $charset = 'iso-8859-1')
    {
        $this->FailedPaymentCount = $FailedPaymentCount;
        $this->_elements['FailedPaymentCount']['charset'] = $charset;
    }
    function getLastPaymentDate()
    {
        return $this->LastPaymentDate;
    }
    function setLastPaymentDate($LastPaymentDate, $charset = 'iso-8859-1')
    {
        $this->LastPaymentDate = $LastPaymentDate;
        $this->_elements['LastPaymentDate']['charset'] = $charset;
    }
    function getLastPaymentAmount()
    {
        return $this->LastPaymentAmount;
    }
    function setLastPaymentAmount($LastPaymentAmount, $charset = 'iso-8859-1')
    {
        $this->LastPaymentAmount = $LastPaymentAmount;
        $this->_elements['LastPaymentAmount']['charset'] = $charset;
    }
}
