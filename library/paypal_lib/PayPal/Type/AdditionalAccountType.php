<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * AdditionalAccountType
 * 
 * The AdditionalAccount component represents historical data related to accounts
 * that the user held with a country of residency other than the current one. eBay
 * users can have one active account at a time. For users who change their country
 * of residency and modify their eBay registration to reflect this change, the new
 * country of residence becomes the currently active account. Any account
 * associated with a previous country is treated as an additional account. Because
 * the currency for these additional accounts are different than the active
 * account, each additional account includes an indicator of the currency for that
 * account. Users who never change their country of residence will not have any
 * additional accounts.
 *
 * @package PayPal
 */
class AdditionalAccountType extends XSDSimpleType
{
    var $Balance;

    var $Currency;

    var $AccountCode;

    function AdditionalAccountType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Balance' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Currency' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AccountCode' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
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
    function getCurrency()
    {
        return $this->Currency;
    }
    function setCurrency($Currency, $charset = 'iso-8859-1')
    {
        $this->Currency = $Currency;
        $this->_elements['Currency']['charset'] = $charset;
    }
    function getAccountCode()
    {
        return $this->AccountCode;
    }
    function setAccountCode($AccountCode, $charset = 'iso-8859-1')
    {
        $this->AccountCode = $AccountCode;
        $this->_elements['AccountCode']['charset'] = $charset;
    }
}
