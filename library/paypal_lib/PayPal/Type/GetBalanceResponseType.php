<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * GetBalanceResponseType
 *
 * @package PayPal
 */
class GetBalanceResponseType extends AbstractResponseType
{
    var $Balance;

    var $BalanceTimeStamp;

    var $BalanceHoldings;

    function GetBalanceResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'Balance' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'BalanceTimeStamp' => 
              array (
                'required' => true,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'BalanceHoldings' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getBalance()
    {
        return $this->Balance;
    }
    function setBalance($Balance, $charset = 'iso-8859-1')
    {
        $this->Balance = $Balance;
        $this->_elements['Balance']['charset'] = $charset;
    }
    function getBalanceTimeStamp()
    {
        return $this->BalanceTimeStamp;
    }
    function setBalanceTimeStamp($BalanceTimeStamp, $charset = 'iso-8859-1')
    {
        $this->BalanceTimeStamp = $BalanceTimeStamp;
        $this->_elements['BalanceTimeStamp']['charset'] = $charset;
    }
    function getBalanceHoldings()
    {
        return $this->BalanceHoldings;
    }
    function setBalanceHoldings($BalanceHoldings, $charset = 'iso-8859-1')
    {
        $this->BalanceHoldings = $BalanceHoldings;
        $this->_elements['BalanceHoldings']['charset'] = $charset;
    }
}
